<?php

namespace Tests\Feature\Chat;

use App\Models\Conversation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChatBroadcastChannelTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_is_authorized_for_their_own_conversation_channel(): void
    {
        $customer = $this->createUser('customer');
        $conversation = Conversation::create(['customer_id' => $customer->id]);

        $channel = $this->conversationChannelCallback();

        $this->assertTrue($channel($customer, $conversation->id));
    }

    public function test_other_customer_is_not_authorized_for_someone_elses_conversation_channel(): void
    {
        $owner = $this->createUser('customer');
        $otherCustomer = $this->createUser('customer');
        $conversation = Conversation::create(['customer_id' => $owner->id]);

        $channel = $this->conversationChannelCallback();

        $this->assertFalse($channel($otherCustomer, $conversation->id));
    }

    public function test_admin_is_authorized_for_any_conversation_channel(): void
    {
        $customer = $this->createUser('customer');
        $admin = $this->createUser('admin');
        $conversation = Conversation::create(['customer_id' => $customer->id]);

        $channel = $this->conversationChannelCallback();

        $this->assertTrue($channel($admin, $conversation->id));
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

    private function conversationChannelCallback(): callable
    {
        $callback = Broadcast::getFacadeRoot()->driver()->getChannels()->get('chat.conversation.{conversationId}');

        $this->assertIsCallable($callback);

        return $callback;
    }
}
