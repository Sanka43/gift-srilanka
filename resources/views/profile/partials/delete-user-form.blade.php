<section class="space-y-6">
    <header>
        <h2 class="font-display text-lg font-semibold text-stone-800">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-stone-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center px-4 py-2.5 bg-danger-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-danger-700 focus:outline-none focus:ring-2 focus:ring-danger-500 focus:ring-offset-2 transition ease-in-out duration-200"
    >
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
            @csrf
            @method('delete')

            <h2 class="font-display text-lg font-semibold text-stone-800">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-2 text-sm text-stone-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="{{ __('Password') }}"
                    class="mt-1 block w-full rounded-xl border border-stone-300 shadow-sm focus:border-danger-500 focus:ring-danger-500 text-sm"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-wrap gap-3 justify-end">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="inline-flex items-center px-4 py-2.5 bg-white border border-stone-300 rounded-xl font-semibold text-sm text-stone-700 hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2 transition ease-in-out duration-200"
                >
                    {{ __('Cancel') }}
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2.5 bg-danger-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-danger-700 focus:outline-none focus:ring-2 focus:ring-danger-500 focus:ring-offset-2 transition ease-in-out duration-200"
                >
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
