<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-primary-600 font-medium text-white shadow-soft hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-300']) }}>
    {{ $slot }}
</button>
