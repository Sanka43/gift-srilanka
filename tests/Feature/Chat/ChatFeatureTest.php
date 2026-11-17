<?php

namespace Tests\Feature\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Role;
use App\Models\User;
use App\Support\ChatGuestIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ChatFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_send_a_chat_message(): void
    {
        config()->set('contact.chat_guest_enabled', false);

        $response = $this->postJson(route('chat.messages.store'), [
            'body' => 'Hello admin',
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_can_send_and_load_messages_when_guest_chat_enabled(): void
    {
        config()->set('contact.chat_guest_enabled', true);

        $sendResponse = $this->postJson(route('chat.messages.store'), [
            'body' => 'Hi, I am a guest.',
        ]);

        $sendResponse
            ->assertCreated()
            ->assertJsonPath('message.sender.id', null)
            ->assertJsonPath('message.sender.name', 'Guest');

        $cookie = collect($sendResponse->headers->getCookies())
            ->first(fn ($item) => $item->getName() === ChatGuestIdentity::COOKIE_NAME);
        $this->assertNotNull($cookie);

        $conversationResponse = $this
            ->withCookie($cookie->getName(), $cookie->getValue())
            ->getJson(route('chat.conversation'));

        $conversationResponse
            ->assertOk()
            ->assertJsonPath('conversation.customer.id', null)
            ->assertJsonPath('messages.0.body', 'Hi, I am a guest.');
    }

    public function test_guest_cookie_reuses_same_conversation(): void
    {
        config()->set('contact.chat_guest_enabled', true);

        $firstResponse = $this->postJson(route('chat.messages.store'), [
            'body' => 'First guest message',
        ]);
        $cookie = collect($firstResponse->headers->getCookies())
            ->first(fn ($item) => $item->getName() === ChatGuestIdentity::COOKIE_NAME);
        $this->assertNotNull($cookie);

        $this
            ->withCookie($cookie->getName(), $cookie->getValue())
            ->postJson(route('chat.messages.store'), [
                'body' => 'Second guest message',
            ])
            ->assertCreated();

        $this->assertSame(1, Conversation::query()->whereNotNull('guest_token')->count());
        $this->assertSame(2, Conversation::firstOrFail()->messages()->count());
    }

    public function test_authenticated_customer_can_send_and_view_own_messages(): void
    {
        Event::fake([MessageSent::class]);

        $customer = $this->createUser('customer');

        $this->actingAs($customer);

        $createResponse = $this->postJson(route('chat.messages.store'), [
            'body' => 'Need help with my order',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('message.sender.id', $customer->id);

        $conversationResponse = $this->getJson(route('chat.conversation'));

        $conversationResponse
            ->assertOk()
            ->assertJsonPath('conversation.customer.id', $customer->id)
            ->assertJsonPath('messages.0.body', 'Need help with my order');

        Event::assertDispatched(MessageSent::class);
    }

    public function test_customer_conversation_can_return_unread_admin_message_count_without_marking_read(): void
    {
        $customer = $this->createUser('customer');
        $admin = $this->createUser('admin');
        $conversation = Conversation::create(['customer_id' => $customer->id]);

        $conversation->messages()->create([
            'sender_id' => $admin->id,
            'body' => 'Unread admin reply',
        ]);

        $response = $this->actingAs($customer)->getJson(route('chat.conversation', [
            'mark_read' => false,
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('unread_messages_count', 1);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'body' => 'Unread admin reply',
            'read_at' => null,
        ]);
    }

    public function test_admin_can_reply_to_customer_conversation(): void
    {
        $customer = $this->createUser('customer');
        $admin = $this->createUser('admin');
        $conversation = Conversation::create(['customer_id' => $customer->id]);

        $conversation->messages()->create([
            'sender_id' => $customer->id,
            'body' => 'Can you update me?',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.chats.messages.store', $conversation), [
            'body' => 'We are checking this for you.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message.sender.id', $admin->id)
            ->assertJsonPath('message.sender.is_admin', true);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'body' => 'We are checking this for you.',
        ]);
    }

    public function test_customer_can_send_image_attachment(): void
    {
        Storage::fake('public');

        $customer = $this->createUser('customer');

        $response = $this->actingAs($customer)->post(route('chat.messages.store'), [
            'body' => '',
            'attachment' => UploadedFile::fake()->create('order-note.png', 256, 'image/png'),
        ], [
            'Accept' => 'application/json',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message.attachment.name', 'order-note.png')
            ->assertJsonPath('message.attachment.is_image', true);

        $message = Conversation::firstOrFail()->messages()->firstOrFail();

        $this->assertNotNull($message->attachment_path);
        Storage::disk('public')->assertExists($message->attachment_path);
    }

    public function test_admin_can_reply_with_pdf_attachment(): void
    {
        Storage::fake('public');

        $customer = $this->createUser('customer');
        $admin = $this->createUser('admin');
        $conversation = Conversation::create(['customer_id' => $customer->id]);

        $conversation->messages()->create([
            'sender_id' => $customer->id,
            'body' => 'Please review this file',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.chats.messages.store', $conversation), [
            'body' => '',
            'attachment' => UploadedFile::fake()->create('invoice.pdf', 320, 'application/pdf'),
        ], [
            'Accept' => 'application/json',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message.attachment.name', 'invoice.pdf')
            ->assertJsonPath('message.attachment.is_pdf', true);

        $message = $conversation->fresh()->messages()->latest('id')->firstOrFail();

        Storage::disk('public')->assertExists($message->attachment_path);
    }

    public function test_admin_cannot_use_customer_chat_endpoints(): void
    {
        $admin = $this->createUser('admin');

        $conversationResponse = $this->actingAs($admin)->getJson(route('chat.conversation'));
        $conversationResponse->assertForbidden();

        $sendResponse = $this->actingAs($admin)->postJson(route('chat.messages.store'), [
            'body' => 'Admin message from storefront',
        ]);
        $sendResponse->assertForbidden();
    }

    public function test_admin_can_only_reply_after_customer_message_exists(): void
    {
        $customer = $this->createUser('customer');
        $admin = $this->createUser('admin');
        $conversation = Conversation::create(['customer_id' => $customer->id]);

        $response = $this->actingAs($admin)->postJson(route('admin.chats.messages.store', $conversation), [
            'body' => 'Checking in first',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'Admins can only reply after a customer sends a message.');
    }

    public function test_admin_chat_summary_returns_new_read_and_unread_customer_message_counts(): void
    {
        $customer = $this->createUser('customer');
        $admin = $this->createUser('admin');
        $conversation = Conversation::create(['customer_id' => $customer->id]);

        $conversation->messages()->create([
            'sender_id' => $customer->id,
            'body' => 'Unread customer message',
            'created_at' => now(),
        ]);

        $conversation->messages()->create([
            'sender_id' => $customer->id,
            'body' => 'Read customer message',
            'read_at' => now(),
            'created_at' => now(),
        ]);

        $conversation->messages()->create([
            'sender_id' => $admin->id,
            'body' => 'Admin reply should not count',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.chats.summary'));

        $response
            ->assertOk()
            ->assertJsonPath('stats.new_messages', 2)
            ->assertJsonPath('stats.unread_messages', 1)
            ->assertJsonPath('stats.read_messages', 1)
            ->assertJsonPath('conversations.0.customer.id', $customer->id)
            ->assertJsonPath('conversations.0.unread_messages_count', 1);
    }

    public function test_customer_cannot_access_another_customers_conversation(): void
    {
        $owner = $this->createUser('customer');
        $otherCustomer = $this->createUser('customer');
        $conversation = Conversation::create(['customer_id' => $owner->id]);

        $response = $this->actingAs($otherCustomer)->getJson(route('chat.conversations.show', $conversation));

        $response->assertForbidden();
    }

    public function test_chat_messages_are_sanitized_before_storage(): void
    {
        $customer = $this->createUser('customer');

        $response = $this->actingAs($customer)->postJson(route('chat.messages.store'), [
            'body' => '<script>alert(1)</script><b>Hello</b> <i>team</i>',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message.body', 'alert(1)Hello team');

        $this->assertDatabaseHas('messages', [
            'sender_id' => $customer->id,
            'body' => 'alert(1)Hello team',
        ]);
    }

    public function test_chat_message_requires_text_or_supported_attachment(): void
    {
        $customer = $this->createUser('customer');

        $emptyResponse = $this->actingAs($customer)->postJson(route('chat.messages.store'), [
            'body' => '',
        ]);

        $emptyResponse
            ->assertStatus(422)
            ->assertJsonValidationErrors(['body']);

        $unsupportedFileResponse = $this->actingAs($customer)->post(route('chat.messages.store'), [
            'body' => '',
            'attachment' => UploadedFile::fake()->create('notes.txt', 5, 'text/plain'),
        ], [
            'Accept' => 'application/json',
        ]);

        $unsupportedFileResponse
            ->assertStatus(422)
            ->assertJsonValidationErrors(['attachment']);
    }

    public function test_chat_send_endpoint_is_rate_limited(): void
    {
        $customer = $this->createUser('customer');

        $this->actingAs($customer);

        for ($index = 1; $index <= 20; $index++) {
            $this->postJson(route('chat.messages.store'), [
                'body' => 'Message ' . $index,
            ])->assertCreated();
        }

        $rateLimitedResponse = $this->postJson(route('chat.messages.store'), [
            'body' => 'Message 21',
        ]);

        $rateLimitedResponse->assertStatus(429);
    }

    private function createUser(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::create([
            'name' => ucfirst($roleName) . ' User',
            'email' => $roleName . '-' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
        ]);
    }
}
