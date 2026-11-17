<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Contact information (shown on Contact page and elsewhere)
    |--------------------------------------------------------------------------
    | Override in .env with CONTACT_EMAIL, CONTACT_PHONE, CONTACT_ADDRESS.
    */
    'email' => env('CONTACT_EMAIL', 'info@giftsrilanka.com'),
    'phone' => env('CONTACT_PHONE', '071 202 0642 / 077 773 2569'),
    'address' => env('CONTACT_ADDRESS', 'Colombo, Sri Lanka'),
    'whatsapp' => env('CONTACT_WHATSAPP', '072 552 4212'),
    
    // google analytics 
    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID', 'G-0QQ27RPKS6'),
    
    /*
    |--------------------------------------------------------------------------
    | Chat: welcome note (shown after the customer’s first message until an admin replies)
    |--------------------------------------------------------------------------
    */
    'chat_welcome_note' => env(
        'CHAT_WELCOME_NOTE',
        'Hi 👋
            Looking for a gift?
            Tell me the occasion, I’ll help you choose the best one 🎁'
    ),

    // Guest chat controls for shared-host friendly rollout.
    'chat_guest_enabled' => env('CHAT_GUEST_ENABLED', false),
    'chat_guest_cookie_days' => (int) env('CHAT_GUEST_COOKIE_DAYS', 7),
    'chat_polling_interval_ms' => (int) env('CHAT_POLL_MS', 5000),
    'chat_guest_auto_note' => env(
        'CHAT_GUEST_AUTO_NOTE',
        'Welcome to Gifts Sri Lanka! We are happy to help you. Please register or sign in to keep your chat history safe and continue the conversation anytime.'
    ),
];
