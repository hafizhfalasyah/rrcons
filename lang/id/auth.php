<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'seo' => [
        'title' => 'RRCONS | Halaman Login',
        'description' => 'Masuk ke sistem administrasi RRCONS.',
        'keywords' => 'RRCONS, Login, Admin',
        'author' => 'RRCONS',
        'og_title' => 'RRCONS | Halaman Login',
        'og_description' => 'Masuk ke sistem administrasi RRCONS.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation' => [

        'brand' => [
            'name' => 'RRCONS',
            'logo_alt' => 'Logo RRCONS',
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

            'avatar_alt' => 'Avatar pengguna',

            'heading' => 'Selamat Datang',

            'subtitle' => 'Silakan masuk untuk melanjutkan.',

            'email' => [

                'label' => 'Alamat Email',

                'placeholder' => 'Masukkan alamat email',

            ],

            'password' => [

                'label' => 'Kata Sandi',

                'placeholder' => 'Masukkan kata sandi',

            ],

            'submit' => 'Masuk',

            'forgot_password' => 'Lupa kata sandi?',

            'footer' => 'Admin RRCONS',

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    */

    'widgets' => [

        // Disiapkan untuk widget autentikasi berikutnya

    ],

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */

    'footer' => [

        'copyright' => 'RRCONS',

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

        'failed' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',

        'password' => 'Kata sandi yang diberikan tidak benar.',

        'throttle' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam :seconds detik.',

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        'validation' => [

            'email' => [

                'required' => 'Alamat email wajib diisi.',

                'email' => 'Alamat email harus menggunakan format yang valid.',

                'max' => 'Alamat email tidak boleh lebih dari :max karakter.',

            ],

            'password' => [

                'required' => 'Kata sandi wajib diisi.',

                'string' => 'Kata sandi harus berupa teks.',

                'min' => 'Kata sandi minimal terdiri dari :min karakter.',

            ],

        ],

    ],

];