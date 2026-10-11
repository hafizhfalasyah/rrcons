<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'seo' => [
        'breadcrumb' => [
            'home' => 'Home',
            'blog' => 'Blog',
        ],

        'index' => [
            'title' => 'RRCONS | Blog - Digital Insights, Technology, and Business Solutions',
            'description' => 'Discover informative articles about technology, application development, web design, and digital solutions. Gain inspiration and insights from experts at RR Group Blog.',
            'keywords' => 'Technology Blog, Digital Solutions, Web Design, App Development, Digital Transformation, RR Blog',
            'og_title' => 'RRCONS | Blog - Digital Insights, Technology, and Business Solutions',
            'og_description' => 'The latest insights and information about the digital world to support your business growth.',
        ],

        'detail' => [
            'title' => 'RRCONS | Blog Details - Digital Insights, Technology, and Business Solutions',
            'description' => 'Read informative articles about technology, application development, web design, and digital solutions from RRCONS.',
            'keywords' => 'Technology Blog Details, Digital Solutions, Web Design, App Development, Digital Transformation, RRCONS',
            'og_title' => 'RRCONS | Blog Details - Digital Insights, Technology, and Business Solutions',
            'og_description' => 'Read the latest insights and information about the digital world to support your business growth.',
        ],

        'author' => 'RR Consultant Indonesia',

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

        'menu' => [
            'home' => 'Home',
            'rr_future' => 'RR Future',
            'service' => 'Services',
            'blog' => 'Blog',
            'contact' => 'Contact',
            'login' => 'Login',
        ],

        'mobile' => [
            'title' => 'Menu',
            'open_menu' => 'Open navigation menu',
            'close_menu' => 'Close navigation menu',
        ],

        'language' => [
            'label' => 'Language',
            'select' => 'Select Language',
            'indonesian' => 'Indonesian',
            'english' => 'English',
            'open_selector' => 'Open language selector',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Sections
    |--------------------------------------------------------------------------
    */

    'sections' => [

        'blog' => [

            'index' => [

                'hero' => [

                    'title_prefix' => 'All',
                    'title_highlight' => 'Blogs',
                    'description' => 'Discover articles, insights, and valuable information about technology, application development, web design, and other digital solutions. Every article is designed to inspire, provide insight, and help you understand the latest developments in the digital world.',

                ],

                'heading' => 'All Blogs',

                'search_placeholder' => 'Search articles...',

                'search_aria_label' => 'Search articles',

                'all_categories' => 'All Categories',

                'pagination' => [

                    'showing' => 'Showing',
                    'to' => 'to',
                    'of' => 'of',
                    'results' => 'results',

                    'navigation' => 'Article page navigation',

                    'previous' => 'Previous page',

                    'next' => 'Next page',

                    'page' => 'Page :page',

                    'current_page' => 'Current page :page',

                ],

                'ajax' => [

                    'error' => 'An error occurred while loading articles.',

                ],

            ],

            'detail' => [

                'category' => [

                    'uncategorized' => 'Uncategorized',

                ],

                'author' => [

                    'default' => 'Admin',

                ],

                'date' => [

                    'format' => 'M d, Y',

                ],

                'response' => [

                    'heading' => 'Share your response',

                    'name_placeholder' => 'Your name',

                    'email_placeholder' => 'Email address',

                    'content_placeholder' => 'Write your response...',

                    'submit' => 'Submit',

                ],

                'comments' => [

                    'heading' => 'Comments (:count)',

                    'empty' => 'There are no comments yet.',

                    'success' => 'Thank you! Your comment will appear once it has been approved.',

                ],

                'sidebar' => [

                    'recent_posts' => 'Recent Posts',

                    'categories' => 'Categories',

                    'posts' => 'Posts',

                    'post' => 'Post',

                    'search_placeholder' => 'Type to search...',

                ],

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

            'open_chat' => 'Open WhatsApp support',

            'close_chat' => 'Close WhatsApp support',

            'icon_alt' => 'WhatsApp icon',

            'logo_alt' => 'RRCONS Logo',

            'support_name' => 'RRCONS Support',

            'online' => 'online',

            'greeting' => 'Hi,',

            'question' => 'How can we help you?',

            'chat_now' => 'Chat Now',

            'powered_by' => 'by',

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */

    'footer' => [

        'logo_alt' => 'RRCONS Logo',

        'brand_name' => 'Rita Russo',

        'brand_highlight' => 'Consultant',

        'description' => 'Since 2012, RR Cons, RR Future Tech, and RR Vision have been trusted partners for various institutions and companies, delivering solutions tailored to business needs through collaboration, technical expertise, and creativity.',

        'address_title' => 'Address',

        'address' => 'Jl. Perum Gadang Regency No. P2 No. 44, Gadang, Sukun District, Malang City, East Java 65149',

        'contact_title' => 'Contact',

        'office_hours_title' => 'Office Hours',

        'monday_friday' => 'Monday - Friday',

        'saturday' => 'Saturday',

        'copyright' => 'All rights reserved.',

        'instagram' => 'Instagram',

        'tiktok' => 'TikTok',

        'x' => 'X',

        'facebook' => 'Facebook',

    ],

    /*
    |--------------------------------------------------------------------------
    | Common
    |--------------------------------------------------------------------------
    */

    'common' => [

        'message' => 'Hello, I would like to ask a question',

    ],

];