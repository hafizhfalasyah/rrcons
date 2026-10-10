<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'seo' => [
        'breadcrumb' => 'Kontak',
        'title' => 'RRCONS | Kontak - Konsultasi dan Solusi Cepat',
        'description' => 'Ingin berkonsultasi atau membutuhkan layanan kami? Hubungi RR Group melalui formulir kontak yang tersedia. Kami siap membantu menemukan solusi yang sesuai dengan kebutuhan Anda.',
        'keywords' => 'hubungi kami, RR Group, konsultasi bisnis, solusi cepat, formulir kontak, bantuan pelanggan',
        'author' => 'RR Consultant Indonesia',
        'og_title' => 'RRCONS | Kontak - Konsultasi dan Solusi Cepat',
        'og_description' => 'Hubungi RR Consultant, RR Future, dan RR Vision untuk mendapatkan bantuan dan solusi yang sesuai dengan kebutuhan Anda.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation' => [

        'brand' => [
            'name' => 'RRCONS',
            'prefix' => 'RR',
            'suffix' => 'CONS',
            'logo_alt' => 'Logo RR Consultant Indonesia',
        ],

        'menu' => [
            'home' => 'Beranda',
            'rr_future' => 'RR Future',
            'service' => 'Layanan',
            'blog' => 'Blog',
            'contact' => 'Kontak',
            'login' => 'Masuk',
        ],

        'mobile' => [
            'title' => 'Menu',
            'open_menu' => 'Buka menu navigasi',
            'close_menu' => 'Tutup menu navigasi',
        ],

        'language' => [
            'label' => 'Bahasa',
            'select' => 'Pilih bahasa',
            'indonesian' => 'Indonesia',
            'english' => 'English',
            'open_selector' => 'Buka pilihan bahasa',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Sections
    |--------------------------------------------------------------------------
    */

    'sections' => [

        'hero' => [
            //
        ],

        'contact' => [

            'form' => [
                'full_name' => [
                    'label' => 'Nama Lengkap',
                    'placeholder' => 'Masukkan nama lengkap',
                ],

                'email' => [
                    'label' => 'Alamat Email',
                    'placeholder' => 'Masukkan alamat email',
                ],

                'phone' => [
                    'label' => 'Nomor Telepon',
                    'placeholder' => 'Masukkan nomor telepon',
                ],

                'message' => [
                    'label' => 'Pesan',
                    'placeholder' => 'Tuliskan pesan Anda...',
                ],

                'submit' => 'Kirim Pesan',
            ],

            'map' => [
                'title' => 'Lokasi kantor RR Consultant Indonesia di Google Maps',
            ],

            'information' => [
                'address' => 'Alamat',
                'phone' => 'Telepon',
                'email' => 'Email',
                'address_fallback' => 'Alamat belum tersedia',
                'phone_number' => '+62 812-5262-4284',
                'email_address' => 'ritarussoconsultant@gmail.com',
                'open_whatsapp' => 'Hubungi RR Consultant Indonesia melalui WhatsApp',
                'send_email' => 'Kirim email kepada RR Consultant Indonesia',
            ],

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    */

    'widgets' => [

        'whatsapp' => [
            'button_label' => 'Buka layanan WhatsApp',
            'icon_alt' => 'Ikon WhatsApp',
            'logo_alt' => 'Logo RR Consultant Indonesia',
            'support_name' => 'Dukungan RRCONS',
            'status' => 'online',
            'close_label' => 'Tutup layanan WhatsApp',
            'greeting' => 'Hai,',
            'message' => 'Ada yang bisa kami bantu?',
            'button' => 'Chat Sekarang',
            'whatsapp_message' => 'Halo, saya ingin bertanya.',
            'powered_by' => 'oleh',
            'provider' => 'Solveit',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */

    'footer' => [

        'brand' => [
            'logo_alt' => 'Logo RR Consultant Indonesia',
            'name' => 'Rita Russo',
            'highlighted_name' => 'Consultant',
            'description' => 'Sejak 2012, RR Cons, RR Future Tech, dan RR Vision telah menjadi mitra terpercaya bagi berbagai lembaga dan perusahaan. Kami menghadirkan solusi yang disesuaikan dengan kebutuhan bisnis melalui kolaborasi, keahlian teknis, dan kreativitas.',
        ],

        'address' => [
            'title' => 'Alamat',
            'content' => 'Jl. Perum Gadang Regency No. P2 No. 44, Gadang, Kec. Sukun, Kota Malang, Jawa Timur 65149',
        ],

        'contact' => [
            'title' => 'Kontak',
            'phone' => '+62 812-5262-4284',
            'email' => 'ritarussoconsultant@gmail.com',
            'phone_label' => 'Hubungi RR Consultant Indonesia melalui telepon',
            'email_label' => 'Kirim email kepada RR Consultant Indonesia',
        ],

        'office_hours' => [
            'title' => 'Jam Kantor',
            'weekdays' => 'Senin - Jumat',
            'weekdays_time' => '08.00 - 16.00',
            'saturday' => 'Sabtu',
            'saturday_time' => '08.00 - 12.00',
        ],

        'copyright' => [
            'brand' => '© Rita Russo Consultant',
            'year' => '2025',
            'text' => 'Seluruh hak dilindungi.',
        ],

        'links' => [
            'terms' => 'Ketentuan',
            'privacy' => 'Privasi',
            'cookies' => 'Cookie',
        ],

        'social_media' => [
            'instagram' => 'Kunjungi Instagram',
            'tiktok' => 'Kunjungi TikTok',
            'x' => 'Kunjungi X',
            'facebook' => 'Kunjungi Facebook',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Common
    |--------------------------------------------------------------------------
    */

    'common' => [
        'message' => '',
    ],

];