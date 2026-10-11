@php
    $currentLocale = app()->getLocale();

    $activeLanguageFlag = $currentLocale === 'en'
        ? 'front/image/united-kingdom.png'
        : 'front/image/indonesia.png';

    $activeLanguageName = $currentLocale === 'en'
        ? __('contact.navigation.language.english')
        : __('contact.navigation.language.indonesian');

    $whatsappMessage = rawurlencode(
        __('contact.widgets.whatsapp.whatsapp_message')
    );
@endphp

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ open: false }"
    xmlns="http://www.w3.org/1999/xhtml"
>
     <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        @include('front.partials.seo', ['seo' => $seo])


        <!-- Icon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('front/image/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Feather Icons -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.17.4/dist/cdn.min.js"></script>




        <style>
            .nav-link::before {
                content: '';
                position: absolute;
                height: 3px;
                width: 0;
                left: 0;
                bottom: -5px;
                background-color: #3b82f6; /* blue-600 */
                transition: width 0.3s ease-in-out;
            }

            .nav-link:hover::before,
            .nav-link.active::before {
                width: 100%;
                height: 4px; /* Perbesar underline saat active */
            }

            /* Custom scrollbar untuk elemen tertentu */
            .body::-webkit-scrollbar {
                width: 5px;
            }

            .body::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .body::-webkit-scrollbar-thumb {
                background: #3b82f6; /* Warna utama scrollbar */
                border-radius: 20px;
            }
        </style>

        <!-- Tailwind (dikompilasi, lihat tailwind.config.js). Terakhir di <head> agar urutan cascade sama seperti Play CDN -->
        <link rel="stylesheet" href="{{ asset_version('front/css/app.css') }}">
    </head>

    <body class="body font-body" x-data="{ open: false }">

        <!-- start of navbar -->
        <nav class="fixed top-0 left-0 w-full py-5 shadow-sm bg-white z-40 text-lg">
            <div class="max-w-screen-xl mx-auto px-6 flex items-center justify-between">
                <!-- Brand -->
                <a
                    href="{{ route('home') }}"
                    class="flex items-center space-x-3"
                    aria-label="{{ __('contact.navigation.brand.name') }}"
                >
                    <div class="rounded-full bg-black shadow-lg">
                        <img
                            src="{{ asset('front/image/logo.png') }}"
                            alt="{{ __('contact.navigation.brand.logo_alt') }}"
                            class="h-12 w-auto"
                        >
                    </div>

                    <div class="text-3xl font-bold">
                        <span class="text-gray-900">{{ __('contact.navigation.brand.prefix') }}</span><span class="text-blue-600">{{ __('contact.navigation.brand.suffix') }}</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-6 font-medium">
                    <a
                        href="{{ route('home') }}"
                        class="relative nav-link {{ Request::routeIs('home') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                    >
                        {{ __('contact.navigation.menu.home') }}
                    </a>

                    <a
                        href="{{ route('rrfuture') }}"
                        class="relative nav-link {{ Request::routeIs('rrfuture') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                    >
                        {{ __('contact.navigation.menu.rr_future') }}
                    </a>

                    <a
                        href="{{ route('service') }}"
                        class="relative nav-link {{ Request::routeIs('service') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                    >
                        {{ __('contact.navigation.menu.service') }}
                    </a>

                    <a
                        href="{{ route('blog') }}"
                        class="relative nav-link {{ Request::routeIs('blog') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                    >
                        {{ __('contact.navigation.menu.blog') }}
                    </a>

                    <a
                        href="{{ route('contact') }}"
                        class="relative nav-link {{ Request::routeIs('contact') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                        aria-current="{{ Request::routeIs('contact') ? 'page' : 'false' }}"
                    >
                        {{ __('contact.navigation.menu.contact') }}
                    </a>
                </div>

                <!-- Language Switcher and Login -->
                <div class="hidden items-center gap-4 md:flex">

                    <!-- Desktop Language Switcher -->
                    <div class="relative">
                        <button
                            id="langToggleDesktop"
                            type="button"
                            class="flex items-center gap-2 rounded-lg px-2 py-2 text-sm text-gray-700 transition hover:bg-gray-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                            aria-label="{{ __('contact.navigation.language.open_selector') }}"
                            aria-haspopup="true"
                            aria-expanded="false"
                            aria-controls="langDropdownDesktop"
                        >
                            <img
                                src="{{ asset($activeLanguageFlag) }}"
                                alt="{{ $activeLanguageName }}"
                                class="h-5 w-5 rounded-full object-cover"
                            >

                            <span class="font-medium">
                                {{ $activeLanguageName }}
                            </span>

                            <svg
                                id="langArrowDesktop"
                                class="ml-1 h-4 w-4 transition-transform duration-200"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.292l3.71-4.06a.75.75 0 011.1 1.02l-4.25 4.65a.75.75 0 01-1.1 0l-4.25-4.65a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>

                        <div
                            id="langDropdownDesktop"
                            class="absolute right-0 z-50 mt-2 hidden min-w-[180px] overflow-hidden rounded-xl border border-gray-100 bg-white py-1 shadow-xl"
                        >
                            <a
                                href="{{ route('set.language', 'id') }}"
                                lang="id"
                                class="flex w-full items-center px-4 py-3 text-sm transition hover:bg-gray-100
                                    {{ $currentLocale === 'id'
                                        ? 'bg-blue-50 font-semibold text-blue-600'
                                        : 'text-gray-700'
                                    }}"
                            >
                                <img
                                    src="{{ asset('front/image/indonesia.png') }}"
                                    alt="{{ __('contact.navigation.language.indonesian') }}"
                                    class="h-5 w-5 rounded-full object-cover"
                                >

                                <span class="ml-3">
                                    {{ __('contact.navigation.language.indonesian') }}
                                </span>

                                @if ($currentLocale === 'id')
                                    <svg
                                        class="ml-auto h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                @endif
                            </a>

                            <a
                                href="{{ route('set.language', 'en') }}"
                                lang="en"
                                class="flex w-full items-center px-4 py-3 text-sm transition hover:bg-gray-100
                                    {{ $currentLocale === 'en'
                                        ? 'bg-blue-50 font-semibold text-blue-600'
                                        : 'text-gray-700'
                                    }}"
                            >
                                <img
                                    src="{{ asset('front/image/united-kingdom.png') }}"
                                    alt="{{ __('contact.navigation.language.english') }}"
                                    class="h-5 w-5 rounded-full object-cover"
                                >

                                <span class="ml-3">
                                    {{ __('contact.navigation.language.english') }}
                                </span>

                                @if ($currentLocale === 'en')
                                    <svg
                                        class="ml-auto h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <a
                        href="{{ route('login') }}"
                        class="rounded-full bg-blue-600 px-8 py-2 text-lg font-semibold text-white transition duration-300 hover:scale-105 hover:bg-blue-700"
                    >
                        {{ __('contact.navigation.menu.login') }}
                    </a>
                </div>

                <!-- Hamburger Menu -->
                <div class="md:hidden">
                    <button
                        id="menu-toggle"
                        type="button"
                        class="focus:outline-none"
                        aria-label="{{ __('contact.navigation.mobile.open_menu') }}"
                        aria-controls="mobile-menu"
                        aria-expanded="false"
                    >
                        <svg
                            class="w-7 h-7 text-gray-800"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </nav>
        <!-- end of navbar -->

        <!-- start of mobile menu -->
        <div id="mobile-menu" class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col text-[20px]">
            <div class="p-6 flex items-center justify-between border-b">
                <div class="text-3xl font-semibold text-gray-900">
                    {{ __('contact.navigation.mobile.title') }}
                </div>

                <button
                    id="close-menu"
                    type="button"
                    class="focus:outline-none"
                    aria-label="{{ __('contact.navigation.mobile.close_menu') }}"
                >
                    <svg
                        class="w-6 h-6 text-gray-800"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
            <nav class="flex-grow p-6 flex flex-col space-y-6">
                <a href="{{ route('home') }}" class="relative nav-link {{ Request::routeIs('home') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">
                    {{ __('contact.navigation.menu.home') }}
                </a>

                <a href="{{ route('rrfuture') }}" class="relative nav-link {{ Request::routeIs('rrfuture') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">
                    {{ __('contact.navigation.menu.rr_future') }}
                </a>

                <a href="{{ route('service') }}" class="relative nav-link {{ Request::routeIs('service') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">
                    {{ __('contact.navigation.menu.service') }}
                </a>

                <a href="{{ route('blog') }}" class="relative nav-link {{ Request::routeIs('blog') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">
                    {{ __('contact.navigation.menu.blog') }}
                </a>

                <a href="{{ route('contact') }}" class="relative nav-link {{ Request::routeIs('contact') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">
                    {{ __('contact.navigation.menu.contact') }}
                </a>

                <a
                    href="{{ route('login') }}"
                    class="inline-block bg-blue-600 text-white text-lg px-6 py-3 rounded-full font-semibold hover:bg-blue-700 transition self-start"
                >
                    {{ __('contact.navigation.menu.login') }}
                </a>

                <!-- Mobile Language Switcher -->
                <div class="border-t border-gray-200 pt-5">
                    <div class="relative w-full">
                        <button
                            id="langToggleMobile"
                            type="button"
                            class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm text-gray-700 transition hover:bg-gray-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                            aria-label="{{ __('contact.navigation.language.open_selector') }}"
                            aria-haspopup="true"
                            aria-expanded="false"
                            aria-controls="langDropdownMobile"
                        >
                            <div class="flex items-center gap-3">
                                <img
                                    src="{{ asset($activeLanguageFlag) }}"
                                    alt="{{ $activeLanguageName }}"
                                    class="h-5 w-5 rounded-full object-cover"
                                >

                                <div class="text-left">
                                    <span class="block text-xs text-gray-500">
                                        {{ __('contact.navigation.language.label') }}
                                    </span>

                                    <span class="block font-medium">
                                        {{ $activeLanguageName }}
                                    </span>
                                </div>
                            </div>

                            <svg
                                id="langArrowMobile"
                                class="h-4 w-4 transition-transform duration-200"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.292l3.71-4.06a.75.75 0 011.1 1.02l-4.25 4.65a.75.75 0 01-1.1 0l-4.25-4.65a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>

                        <div
                            id="langDropdownMobile"
                            class="mt-2 hidden w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md"
                        >
                            <a
                                href="{{ route('set.language', 'id') }}"
                                lang="id"
                                class="flex w-full items-center px-4 py-3 text-sm transition hover:bg-gray-100
                                    {{ $currentLocale === 'id'
                                        ? 'bg-blue-50 font-semibold text-blue-600'
                                        : 'text-gray-700'
                                    }}"
                            >
                                <img
                                    src="{{ asset('front/image/indonesia.png') }}"
                                    alt="{{ __('contact.navigation.language.indonesian') }}"
                                    class="h-5 w-5 rounded-full object-cover"
                                >

                                <span class="ml-3">
                                    {{ __('contact.navigation.language.indonesian') }}
                                </span>

                                @if ($currentLocale === 'id')
                                    <svg
                                        class="ml-auto h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                @endif
                            </a>

                            <a
                                href="{{ route('set.language', 'en') }}"
                                lang="en"
                                class="flex w-full items-center px-4 py-3 text-sm transition hover:bg-gray-100
                                    {{ $currentLocale === 'en'
                                        ? 'bg-blue-50 font-semibold text-blue-600'
                                        : 'text-gray-700'
                                    }}"
                            >
                                <img
                                    src="{{ asset('front/image/united-kingdom.png') }}"
                                    alt="{{ __('contact.navigation.language.english') }}"
                                    class="h-5 w-5 rounded-full object-cover"
                                >

                                <span class="ml-3">
                                    {{ __('contact.navigation.language.english') }}
                                </span>

                                @if ($currentLocale === 'en')
                                    <svg
                                        class="ml-auto h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <!-- end of mobile menu -->

        <!-- start of hero -->
        @foreach ($heroes as $hero)
            <section class="container max-w-screen-xl rounded-[2rem] overflow-hidden mx-auto mt-10 px-4 relative text-white min-h-[600px] pt-36 sm:py-48 mb-50" style="background-image: url('{{ asset('storage/' . $hero->bg_img) }}'); background-size: cover; background-position: center; margin-top: 8rem;">
                <div class="absolute inset-0 bg-black opacity-75"></div> <!-- Dark Overlay -->
                <div class="relative z-10 max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="uppercase text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-snug mb-4">
                        {!! highlightLastWord($hero->contactMenu->title ?? '') !!}
                    </h1>
                    <p class="text-sm sm:text-base md:text-lg leading-relaxed text-gray-200 max-w-3xl mx-auto">{{ $hero->contactMenu->sub_title ?? '' }}</p>
                </div>
            </section>
        @endforeach
        <!-- end of hero -->

        <!-- start of contact -->
        <section class="pt-36 max-w-screen-xl mx-auto px-4">
            @if ($usCollection->first())
                <div class="text-center mb-12">
                    <h2 class="uppercase text-4xl font-bold text-gray-900 mb-4">{!! highlightLastWord($usCollection->first()->contactMenu->title ?? '') !!}</h2>
                    <p class="text-lg text-gray-500">{{ $usCollection->first()->contactMenu->sub_title ?? '' }}</p>
                </div>
            @endif

            <!-- Form & Map Container -->
            <div class="bg-white shadow-md rounded-xl overflow-hidden flex flex-col md:flex-row">

                <!-- Contact Form -->
                <div class="w-full md:w-1/2 p-8 bg-gray-100">
                    <form class="space-y-5">
                        <div class="flex flex-col md:flex-row md:gap-4">
                            <div class="w-full md:w-1/2">
                                <label
                                    for="full_name"
                                    class="block text-sm font-medium mb-1"
                                >
                                    {{ __('contact.sections.contact.form.full_name.label') }}
                                </label>

                                <input
                                    id="full_name"
                                    name="full_name"
                                    type="text"
                                    autocomplete="name"
                                    placeholder="{{ __('contact.sections.contact.form.full_name.placeholder') }}"
                                    class="w-full p-3 border border-gray-300 rounded-xl"
                                >
                            </div>

                            <div class="w-full md:w-1/2 mt-4 md:mt-0">
                                <label
                                    for="email"
                                    class="block text-sm font-medium mb-1"
                                >
                                    {{ __('contact.sections.contact.form.email.label') }}
                                </label>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="{{ __('contact.sections.contact.form.email.placeholder') }}"
                                    class="w-full p-3 border border-gray-300 rounded-xl"
                                >
                            </div>
                        </div>

                        <div>
                            <label
                                for="phone"
                                class="block text-sm font-medium mb-1"
                            >
                                {{ __('contact.sections.contact.form.phone.label') }}
                            </label>

                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                autocomplete="tel"
                                inputmode="tel"
                                placeholder="{{ __('contact.sections.contact.form.phone.placeholder') }}"
                                class="w-full p-3 border border-gray-300 rounded-xl"
                            >
                        </div>

                        <div>
                            <label
                                for="message"
                                class="block text-sm font-medium mb-1"
                            >
                                {{ __('contact.sections.contact.form.message.label') }}
                            </label>

                            <textarea
                                id="message"
                                name="message"
                                rows="4"
                                placeholder="{{ __('contact.sections.contact.form.message.placeholder') }}"
                                class="w-full p-3 border border-gray-300 rounded-xl resize-none"
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow"
                        >
                            {{ __('contact.sections.contact.form.submit') }}
                        </button>
                    </form>
                </div>

                <!-- Google Map -->
                <div class="w-full md:w-1/2">
                    <iframe
                        class="w-full h-full min-h-[400px]"
                        src="https://maps.google.com/maps?q=Jl.%20Perum%20Gadang%20Regency%20No.P2%20No.%2044%2C%20Gadang%2C%20Kec.%20Sukun%20Kota%20Malang%20Jawa%20Timur&t=&z=17&ie=UTF8&iwloc=&output=embed"
                        title="{{ __('contact.sections.contact.map.title') }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>

            <!-- Contact Info (Centered) -->
            <div class="flex justify-center mt-12">
                <div class="grid grid-cols-1 ml-12 sm:grid-cols-2 md:grid-cols-3 gap-8 text-center text-gray-700 max-w-5xl w-full">

                    <div>
                        <div class="flex justify-center text-blue-600 text-3xl mb-2">
                            <i data-feather="map-pin"></i>
                        </div>
                        <p>
                            <span class="font-semibold">
                                {{ __('contact.sections.contact.information.address') }}:
                            </span>
                            <br>

                            {{ $us['Alamat']
                                ?? __('contact.sections.contact.information.address_fallback')
                            }}
                        </p>
                    </div>

                    <div>
                        <div class="flex justify-center text-blue-600 text-3xl mb-2">
                            <i data-feather="phone"></i>
                        </div>
                        <p>
                            <span class="font-semibold">{{ __('contact.sections.contact.information.phone') }}:</span><br>
                            <a href="https://wa.me/6281252624284" target="_blank" rel="noopener" class="hover:text-blue-500"
                               aria-label="{{ __('contact.sections.contact.information.open_whatsapp') }}">
                                {{ __('contact.sections.contact.information.phone_number') }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <div class="flex justify-center text-blue-600 text-3xl mb-2">
                            <i data-feather="mail"></i>
                        </div>
                        <p>
                            <span class="font-semibold">{{ __('contact.sections.contact.information.email') }}:</span><br>
                            <a href="mailto:{{ __('contact.sections.contact.information.email_address') }}" class="hover:text-blue-500"
                               aria-label="{{ __('contact.sections.contact.information.send_email') }}">
                                {{ __('contact.sections.contact.information.email_address') }}
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </section>
        <!-- end of contact -->

        <!-- start of whatsapp -->
        <section id="whatsapp-support" class="relative">
            <button
                id="waBtn"
                type="button"
                class="fixed bottom-6 right-6 bg-green-500 rounded-full p-3 shadow-lg hover:bg-green-600 transition duration-300 z-50"
                aria-label="{{ __('contact.widgets.whatsapp.button_label') }}"
                aria-controls="chatBox"
                aria-expanded="false"
            >
                <img
                    src="https://img.icons8.com/ios-filled/30/ffffff/whatsapp.png"
                    alt="{{ __('contact.widgets.whatsapp.icon_alt') }}"
                >
            </button>

            <div
                id="chatBox"
                class="fixed bottom-24 right-6 w-80 rounded-xl shadow-lg overflow-hidden hidden z-40"
            >
                <!-- Header -->
                <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <img
                            src="{{ asset('front/image/logo.png') }}"
                            alt="{{ __('contact.widgets.whatsapp.logo_alt') }}"
                            class="w-12 h-12 bg-black rounded-full shadow-lg"
                        >

                        <div>
                            <div class="font-bold text-sm">
                                {{ __('contact.widgets.whatsapp.support_name') }}
                            </div>

                            <div class="text-xs text-green-400">
                                {{ __('contact.widgets.whatsapp.status') }}
                            </div>
                        </div>
                    </div>

                    <button
                        id="closeBtn"
                        type="button"
                        class="text-white text-xl font-bold"
                        aria-label="{{ __('contact.widgets.whatsapp.close_label') }}"
                    >
                        &times;
                    </button>
                </div>

                <!-- Body -->
                <div class="p-4 bg-white bg-opacity-80">
                    <div class="mb-6">
                        <div class="text-sm font-semibold text-gray-700 mb-1">
                            {{ __('contact.widgets.whatsapp.support_name') }}
                        </div>

                        <div class="bg-white text-sm text-gray-800 p-3 rounded-lg shadow">
                            {{ __('contact.widgets.whatsapp.greeting') }}
                            <br>
                            {{ __('contact.widgets.whatsapp.message') }}
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-white px-4 pt-4 pb-6 text-center">
                    <a
                        href="https://wa.me/6281252624284?text={{ $whatsappMessage }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="block w-full rounded-full bg-green-500 py-3 text-sm font-semibold text-white shadow transition duration-300 hover:bg-green-600"
                    >
                        {{ __('contact.widgets.whatsapp.button') }}
                    </a>

                    <p class="text-xs text-gray-500 mt-2">
                        ⚡ {{ __('contact.widgets.whatsapp.powered_by') }}
                        <span class="text-green-600 font-semibold">
                            {{ __('contact.widgets.whatsapp.provider') }}
                        </span>
                    </p>
                </div>
            </div>
        </section>
        <!-- end of whatsapp -->

        <!-- start of footer -->
        <footer class="py-10 mt-36 max-w-screen-xl mx-auto bg-white border-t border-gray-200 rounded-lg px-4">
            <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 items-start">

                <!-- Logo and Description -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <img
                            src="{{ asset('front/image/logo.png') }}"
                            alt="{{ __('contact.footer.brand.logo_alt') }}"
                            class="h-8 w-8"
                        >

                        <h2 class="text-xl font-semibold">
                            {{ __('contact.footer.brand.name') }}
                            <span class="text-blue-600">
                                {{ __('contact.footer.brand.highlighted_name') }}
                            </span>
                        </h2>
                    </div>

                    <p class="text-gray-600 text-sm leading-relaxed">
                        {{ __('contact.footer.brand.description') }}
                    </p>
                </div>

                <!-- Address -->
                <div class="sm:pl-0 md:pl-24">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">
                        {{ __('contact.footer.address.title') }}
                    </h3>

                    <p class="text-gray-500">
                        {{ __('contact.footer.address.content') }}
                    </p>
                </div>

                <!-- Contact -->
                <div class="sm:pl-0 md:pl-32">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">
                        {{ __('contact.footer.contact.title') }}
                    </h3>

                    <p class="text-gray-500">
                        {{ __('contact.footer.contact.phone') }}
                    </p>

                    <p class="text-gray-500">
                        {{ __('contact.footer.contact.email') }}
                    </p>
                </div>

                <!-- Office Hours -->
                <div class="ml-0 md:ml-auto">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">
                        {{ __('contact.footer.office_hours.title') }}
                    </h3>

                    <p class="text-gray-500">
                        {{ __('contact.footer.office_hours.weekdays') }}
                    </p>

                    <p class="text-gray-500">
                        {{ __('contact.footer.office_hours.weekdays_time') }}
                    </p>

                    <p class="mt-2 text-gray-500">
                        {{ __('contact.footer.office_hours.saturday') }}
                    </p>

                    <p class="text-gray-500">
                        {{ __('contact.footer.office_hours.saturday_time') }}
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-200 my-6"></div>

            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 space-y-4 md:space-y-0">
                <p>
                    <span class="text-blue-600">
                        {{ __('contact.footer.copyright.brand') }}
                        {{ __('contact.footer.copyright.year') }}
                    </span>,
                    {{ __('contact.footer.copyright.text') }}
                </p>

                <div class="flex space-x-6">
                    <a href="#" class="hover:underline">
                        {{ __('contact.footer.links.terms') }}
                    </a>

                    <a href="#" class="hover:underline">
                        {{ __('contact.footer.links.privacy') }}
                    </a>

                    <a href="#" class="hover:underline">
                        {{ __('contact.footer.links.cookies') }}
                    </a>
                </div>

                <div class="flex space-x-4">
                    <a
                        href="#"
                        aria-label="{{ __('contact.footer.social_media.instagram') }}"
                    >
                        <svg
                            class="w-6 h-6 text-gray-600 hover:text-pink-500"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5A4.25 4.25 0 0 0 20.5 16.25v-8.5A4.25 4.25 0 0 0 16.25 3.5h-8.5Zm9 2.25a.75.75 0 0 1 1.5 0v1a.75.75 0 0 1-1.5 0v-1ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"/>
                        </svg>
                    </a>

                    <a
                        href="#"
                        aria-label="{{ __('contact.footer.social_media.tiktok') }}"
                    >
                        <svg
                            class="w-6 h-6 text-gray-600 hover:text-black"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M12.75 2h2.5a5.25 5.25 0 0 0 5.25 5.25v2.5a7.75 7.75 0 0 1-5.25-1.85v7.1a6.25 6.25 0 1 1-6.25-6.25h.75v2.5h-.75a3.75 3.75 0 1 0 3.75 3.75V2Z"/>
                        </svg>
                    </a>

                    <a
                        href="#"
                        aria-label="{{ __('contact.footer.social_media.x') }}"
                    >
                        <svg
                            class="w-6 h-6 text-gray-600 hover:text-black"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M14.5 10.5 21 3h-2.25l-5.25 6.25L9 3H3l6.93 10.02L3 21h2.25l5.7-6.77L15 21h6l-6.5-10.5Zm-2.07 2.45-.66-.98L5.1 4.5H8.1l4.17 6.15.66.98 6.93 10.02H16.9l-4.47-6.65Z"/>
                        </svg>
                    </a>

                    <a
                        href="#"
                        aria-label="{{ __('contact.footer.social_media.facebook') }}"
                    >
                        <svg
                            class="w-6 h-6 text-gray-600 hover:text-blue-600"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M14.5 3H10A4 4 0 0 0 6 7v3H3v4h3v7h4v-7h3.5l1-4H10V7a1 1 0 0 1 1-1h3.5V3Z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </footer>
        <!-- end of footer -->

        <script>
            feather.replace();
        </script>

        <script src="{{ asset_version('front/js/index.js') }}"></script>

        @include('front.partials.analytics')

    </body>

</html>