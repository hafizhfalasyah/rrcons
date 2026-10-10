<?php

return [

    // Dipakai langsung oleh Laravel (AuthenticatesUsers / ThrottlesLogins)
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',


    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'seo' => [
        'title' => 'RRCONS | Login Page',
        'description' => 'Login to the RRCONS administration system.',
        'keywords' => 'RRCONS, Login, Admin',
        'author' => 'RRCONS',
        'og_title' => 'RRCONS | Login Page',
        'og_description' => 'Login to the RRCONS administration system.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation' => [

        'brand' => [
            'name' => 'RRCONS',
            'logo_alt' => 'RRCONS Logo',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Sections
    |--------------------------------------------------------------------------
    */

    'sections' => [

        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        'login' => [

            'avatar_alt' => 'User avatar',

            'heading' => 'Welcome',

            'subtitle' => 'Please log in to continue.',

            'email' => [
                'label' => 'Email Address',
                'placeholder' => 'Enter your email address',
            ],

            'password' => [
                'label' => 'Password',
                'placeholder' => 'Enter your password',
            ],

            'submit' => 'Login',

            'forgot_password' => 'Forgot your password?',

            'footer' => 'RRCONS Admin',

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    */

    'widgets' => [

        // Reserved for future authentication widgets

    ],

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */

    'footer' => [

        'copyright' => '© Rita Russo Consultant',

    ],

    /*
    |--------------------------------------------------------------------------
    | Common
    |--------------------------------------------------------------------------
    */

    'common' => [

        /*
        |--------------------------------------------------------------------------
        | Authentication Messages
        |--------------------------------------------------------------------------
        */

        'failed' => 'These credentials do not match our records.',

        'password' => 'The provided password is incorrect.',

        'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        'validation' => [

            'email' => [

                'required' => 'The email address field is required.',

                'email' => 'The email address must be a valid email address.',

                'max' => 'The email address may not be greater than :max characters.',

            ],

            'password' => [

                'required' => 'The password field is required.',

                'string' => 'The password must be a string.',

                'min' => 'The password must be at least :min characters.',

            ],

        ],

    ],

];