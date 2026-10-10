@php
    if (!function_exists('highlightLastWord')) {
        function highlightLastWord($text, $class = 'text-blue-500') {
            $words = explode(' ', trim($text));

            if (count($words) === 0) {
                return '';
            }

            $lastWord = array_pop($words);
            $firstPart = implode(' ', $words);

            return $firstPart
                . ' <span class="'
                . e($class)
                . '">'
                . e($lastWord)
                . '</span>';
        }
    }

    $currentLocale = app()->getLocale();

    $activeLanguageFlag = $currentLocale === 'en'
        ? 'front/image/united-kingdom.png'
        : 'front/image/indonesia.png';

    $activeLanguageName = $currentLocale === 'en'
        ? __('rrfuture.navigation.language.english')
        : __('rrfuture.navigation.language.indonesian');

    $whatsappMessage = rawurlencode(
        __('rrfuture.widgets.whatsapp.whatsapp_message')
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
        <title>{{ __('rrfuture.seo.title') }}</title>

        <!-- SEO Meta Tag -->
        <meta
            name="description"
            content="{{ __('rrfuture.seo.description') }}"
        >
        <meta
            name="keywords"
            content="{{ __('rrfuture.seo.keywords') }}"
        >
        <meta
            name="author"
            content="{{ __('rrfuture.seo.author') }}"
        >

        <meta
            property="og:title"
            content="{{ __('rrfuture.seo.og_title') }}"
        >
        <meta
            property="og:description"
            content="{{ __('rrfuture.seo.og_description') }}"
        >
        <meta
            property="og:url"
            content="{{ url()->current() }}"
        >

        <!-- Tailwind CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Icon -->
        <link
            rel="icon"
            type="image/png"
            sizes="32x32"
            href="{{ asset('front/image/logo.png') }}"
        >

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Feather Icons -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- AOS CSS -->
        <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />

        <!--flatpickr-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <!-- Lottie Player CDN -->
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

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

    </head>

    <body class="body font-body" x-data="{ open: false }">

        <!-- start of navbar -->
        <nav class="fixed top-0 left-0 w-full py-5 shadow-sm bg-white z-40 text-lg">
            <div class="max-w-screen-xl mx-auto px-6 flex items-center justify-between">
                <!-- Brand -->
                <a
                    href="{{ route('home') }}"
                    class="flex items-center space-x-3"
                    aria-label="{{ __('rrfuture.navigation.brand.name') }}"
                >
                    <div class="rounded-full bg-black shadow-lg">
                        <img
                            src="{{ asset('front/image/logo.png') }}"
                            alt="{{ __('rrfuture.navigation.brand.logo_alt') }}"
                            class="h-12 w-auto"
                        >
                    </div>

                    <div class="text-3xl font-bold">
                        <span class="text-gray-900">{{ __('rrfuture.navigation.brand.prefix') }}</span><span class="text-blue-600">{{ __('rrfuture.navigation.brand.suffix') }}</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-6 font-medium">
                    <a
                        href="{{ route('home') }}"
                        class="relative nav-link {{ Request::routeIs('home') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                    >
                        {{ __('rrfuture.navigation.menu.home') }}
                    </a>

                    <a
                        href="{{ route('rrfuture') }}"
                        class="relative nav-link {{ Request::routeIs('rrfuture') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                    >
                        {{ __('rrfuture.navigation.menu.rr_future') }}
                    </a>

                    <a
                        href="{{ route('service') }}"
                        class="relative nav-link {{ Request::routeIs('service') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                    >
                        {{ __('rrfuture.navigation.menu.service') }}
                    </a>

                    <a
                        href="{{ route('blog') }}"
                        class="relative nav-link {{ Request::routeIs('blog') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                    >
                        {{ __('rrfuture.navigation.menu.blog') }}
                    </a>

                    <a
                        href="{{ route('contact') }}"
                        class="relative nav-link {{ Request::routeIs('contact') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                    >
                        {{ __('rrfuture.navigation.menu.contact') }}
                    </a>
                </div>

                <!-- Language Switcher and Login -->
                <div class="hidden items-center gap-4 md:flex">
                    <div class="relative">
                        <button
                            id="langToggleDesktop"
                            type="button"
                            class="flex items-center gap-2 rounded-lg px-2 py-2 text-sm text-gray-700 transition hover:bg-gray-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                            aria-label="{{ __('rrfuture.navigation.language.open_selector') }}"
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
                                    alt="{{ __('rrfuture.navigation.language.indonesian') }}"
                                    class="h-5 w-5 rounded-full object-cover"
                                >

                                <span class="ml-3">
                                    {{ __('rrfuture.navigation.language.indonesian') }}
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
                                    alt="{{ __('rrfuture.navigation.language.english') }}"
                                    class="h-5 w-5 rounded-full object-cover"
                                >

                                <span class="ml-3">
                                    {{ __('rrfuture.navigation.language.english') }}
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

                    <a
                        href="{{ route('login') }}"
                        class="rounded-full bg-blue-600 px-8 py-2 text-lg font-semibold text-white transition duration-300 hover:scale-105 hover:bg-blue-700"
                    >
                        {{ __('rrfuture.navigation.menu.login') }}
                    </a>
                </div>

                <!-- Hamburger Menu -->
                <div class="md:hidden">
                    <button
                        id="menu-toggle"
                        class="focus:outline-none"
                        aria-label="{{ __('rrfuture.navigation.mobile.open_menu') }}"
                    >
                        <svg
                            class="w-7 h-7 text-gray-800"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
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
        <div
            id="mobile-menu"
            class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col text-[20px]"
        >
            <div class="p-6 flex items-center justify-between border-b">
                <div class="text-3xl font-semibold text-gray-900">
                    {{ __('rrfuture.navigation.mobile.title') }}
                </div>

                <button
                    id="close-menu"
                    class="focus:outline-none"
                    aria-label="{{ __('rrfuture.navigation.mobile.close_menu') }}"
                >
                    <svg
                        class="w-6 h-6 text-gray-800"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
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
                <a
                    href="{{ route('home') }}"
                    class="relative nav-link {{ Request::routeIs('home') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('rrfuture.navigation.menu.home') }}
                </a>

                <a
                    href="{{ route('rrfuture') }}"
                    class="relative nav-link {{ Request::routeIs('rrfuture') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('rrfuture.navigation.menu.rr_future') }}
                </a>

                <a
                    href="{{ route('service') }}"
                    class="relative nav-link {{ Request::routeIs('service') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('rrfuture.navigation.menu.service') }}
                </a>

                <a
                    href="{{ route('blog') }}"
                    class="relative nav-link {{ Request::routeIs('blog') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('rrfuture.navigation.menu.blog') }}
                </a>

                <a
                    href="{{ route('contact') }}"
                    class="relative nav-link {{ Request::routeIs('contact') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('rrfuture.navigation.menu.contact') }}
                </a>

                <a
                    href="{{ route('login') }}"
                    class="inline-block bg-blue-600 text-white text-lg px-6 py-3 rounded-full font-semibold hover:bg-blue-700 transition self-start"
                >
                    {{ __('rrfuture.navigation.menu.login') }}
                </a>

                <!-- Mobile Language Switcher -->
                <div class="border-t border-gray-200 pt-5">
                    <div class="relative w-full">
                        <button
                            id="langToggleMobile"
                            type="button"
                            class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm text-gray-700 transition hover:bg-gray-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                            aria-label="{{ __('rrfuture.navigation.language.open_selector') }}"
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
                                        {{ __('rrfuture.navigation.language.label') }}
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
                                    alt="{{ __('rrfuture.navigation.language.indonesian') }}"
                                    class="h-5 w-5 rounded-full object-cover"
                                >

                                <span class="ml-3">
                                    {{ __('rrfuture.navigation.language.indonesian') }}
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
                                    alt="{{ __('rrfuture.navigation.language.english') }}"
                                    class="h-5 w-5 rounded-full object-cover"
                                >

                                <span class="ml-3">
                                    {{ __('rrfuture.navigation.language.english') }}
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
                        {!! highlightLastWord($hero->rrfutureMenu->title ?? '') !!}
                    </h1>
                    <p class="text-sm sm:text-base md:text-lg leading-relaxed text-gray-200 max-w-3xl mx-auto">{{ $hero->rrfutureMenu->sub_title ?? '' }}</p>
                </div>
            </section>
        @endforeach
        <!-- end of hero -->

        <!-- start of product -->
        <section class="pt-36 max-w-7xl mx-auto px-6">
            @if ($products->first())
                <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-12">
                    <div>
                        <span class="bg-blue-100 text-blue-600 px-3 py-1 text-sm rounded-full">
                            {{ __('rrfuture.sections.product.label') }}
                        </span>
                        <h2 class="text-3xl font-bold mt-2">{!! $products->first()->rrfutureMenu->title ?? '' !!}</h2>
                    </div>
                    <p class="text-gray-500 max-w-xl mt-6 md:mt-0">{{ $products->first()->rrfutureMenu->sub_title ?? '' }}</p>
                </div>
            @endif

            <!-- Cards -->
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($products as $index => $product)
                    @php
                        $icons = ['cpu', 'monitor', 'briefcase']; // urutan ikon tetap
                        $colors = ['text-purple-600', 'text-green-600', 'text-red-600']; // urutan warna tetap

                        $icon = $icons[$index % count($icons)];
                        $color = $colors[$index % count($colors)];
                    @endphp

                    <div class="bg-white p-6 border rounded-xl shadow hover:shadow-md transition">
                        <div class="{{ $color }} mb-4">
                            <i data-feather="{{ $icon }}" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $product->desc }}</p>
                    </div>
                @endforeach
            </div>

        </section>
        <!-- end of product -->

        <!-- start of whatsapp -->
        <section id="whatsapp-support" class="relative">
            <button
                id="waBtn"
                type="button"
                class="fixed bottom-6 right-6 bg-green-500 rounded-full p-3 shadow-lg hover:bg-green-600 transition duration-300 z-50"
                aria-label="{{ __('rrfuture.widgets.whatsapp.button_label') }}"
            >
                <img
                    src="https://img.icons8.com/ios-filled/30/ffffff/whatsapp.png"
                    alt="{{ __('rrfuture.widgets.whatsapp.icon_alt') }}"
                >
            </button>

            <div
                id="chatBox"
                class="fixed bottom-24 right-6 w-80 rounded-xl shadow-lg overflow-hidden hidden z-40"
            >
                <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <img
                            src="{{ asset('front/image/logo.png') }}"
                            alt="{{ __('rrfuture.widgets.whatsapp.logo_alt') }}"
                            class="w-12 h-12 bg-black rounded-full shadow-lg"
                        >

                        <div>
                            <div class="font-bold text-sm">
                                {{ __('rrfuture.widgets.whatsapp.support_name') }}
                            </div>

                            <div class="text-xs text-green-400">
                                {{ __('rrfuture.widgets.whatsapp.status') }}
                            </div>
                        </div>
                    </div>

                    <button
                        id="closeBtn"
                        type="button"
                        class="text-white text-xl font-bold"
                        aria-label="{{ __('rrfuture.widgets.whatsapp.close_label') }}"
                    >
                        &times;
                    </button>
                </div>

                <div class="p-4 bg-white bg-opacity-80">
                    <div class="mb-6">
                        <div class="text-sm font-semibold text-gray-700 mb-1">
                            {{ __('rrfuture.widgets.whatsapp.support_name') }}
                        </div>

                        <div class="bg-white text-sm text-gray-800 p-3 rounded-lg shadow">
                            {{ __('rrfuture.widgets.whatsapp.greeting') }}
                            <br>
                            {{ __('rrfuture.widgets.whatsapp.message') }}
                        </div>
                    </div>
                </div>

                <div class="bg-white px-4 pt-4 pb-6 text-center">
                    <a
                        href="https://wa.me/6281252624284?text={{ $whatsappMessage }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="block w-full rounded-full bg-green-500 py-3 text-sm font-semibold text-white shadow transition duration-300 hover:bg-green-600"
                    >
                        {{ __('rrfuture.widgets.whatsapp.button') }}
                    </a>

                    <p class="text-xs text-gray-500 mt-2">
                        ⚡ {{ __('rrfuture.widgets.whatsapp.powered_by') }}
                        <span class="text-green-600 font-semibold">
                            {{ __('rrfuture.widgets.whatsapp.provider') }}
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
                            alt="{{ __('rrfuture.footer.brand.logo_alt') }}"
                            class="h-8 w-8"
                        >

                        <h1 class="text-xl font-semibold">
                            {{ __('rrfuture.footer.brand.name') }}
                            <span class="text-blue-600">
                                {{ __('rrfuture.footer.brand.highlighted_name') }}
                            </span>
                        </h1>
                    </div>

                    <p class="text-gray-600 text-sm leading-relaxed">
                        {{ __('rrfuture.footer.brand.description') }}
                    </p>
                </div>

                <!-- Address -->
                <div class="sm:pl-0 md:pl-24">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">
                        {{ __('rrfuture.footer.address.title') }}
                    </h3>

                    <p class="text-gray-500">
                        {{ __('rrfuture.footer.address.content') }}
                    </p>
                </div>

                <!-- Contact -->
                <div class="sm:pl-0 md:pl-32">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">
                        {{ __('rrfuture.footer.contact.title') }}
                    </h3>

                    <p class="text-gray-500">
                        {{ __('rrfuture.footer.contact.phone') }}
                    </p>

                    <p class="text-gray-500">
                        {{ __('rrfuture.footer.contact.email') }}
                    </p>
                </div>

                <!-- Office Hours -->
                <div class="ml-0 md:ml-auto">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">
                        {{ __('rrfuture.footer.office_hours.title') }}
                    </h3>

                    <p class="text-gray-500">
                        {{ __('rrfuture.footer.office_hours.weekdays') }}
                    </p>

                    <p class="text-gray-500">
                        {{ __('rrfuture.footer.office_hours.weekdays_time') }}
                    </p>

                    <p class="mt-2 text-gray-500">
                        {{ __('rrfuture.footer.office_hours.saturday') }}
                    </p>

                    <p class="text-gray-500">
                        {{ __('rrfuture.footer.office_hours.saturday_time') }}
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-200 my-6"></div>

            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 space-y-4 md:space-y-0">
                <p>
                    <span class="text-blue-600">
                        {{ __('rrfuture.footer.copyright.brand') }}
                        {{ __('rrfuture.footer.copyright.year') }}
                    </span>,
                    {{ __('rrfuture.footer.copyright.text') }}
                </p>

                <div class="flex space-x-6">
                    <a href="#" class="hover:underline">
                        {{ __('rrfuture.footer.links.terms') }}
                    </a>

                    <a href="#" class="hover:underline">
                        {{ __('rrfuture.footer.links.privacy') }}
                    </a>

                    <a href="#" class="hover:underline">
                        {{ __('rrfuture.footer.links.cookies') }}
                    </a>
                </div>

                <div class="flex space-x-4">
                    <a
                        href="#"
                        aria-label="{{ __('rrfuture.footer.social_media.instagram') }}"
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
                        aria-label="{{ __('rrfuture.footer.social_media.tiktok') }}"
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
                        aria-label="{{ __('rrfuture.footer.social_media.x') }}"
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
                        aria-label="{{ __('rrfuture.footer.social_media.facebook') }}"
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

        <script src="{{ asset('front/js/index.js') }}"></script>

</body>
</html>