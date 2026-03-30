@php
    if (!function_exists('highlightLastWord')) {
        function highlightLastWord($text, $class = 'text-blue-500') {
            $words = explode(' ', trim($text));
            if (count($words) === 0) return '';

            $lastWord = array_pop($words);
            $firstPart = implode(' ', $words);

            return $firstPart . ' <span class="' . $class . '">' . $lastWord . '</span>';
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en" x-data="{ open: false }" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>RRCONS | Beranda - Solusi Bisnis Terbaik untuk Masa Depan</title>

        <!-- SEO Meta Tag -->
        <meta name="description" content="RR Consultant Indonesia hadir untuk memberikan solusi bisnis yang memadai dan berkelanjutan. Temukan layanan kami, profil tim, pelatihan bisnis, serta blog dan galeri acara.">
        <meta name="keywords" content="RR Consultant, Konsultan Bisnis, Solusi Bisnis, Pelatihan, Transformasi Digital, Layanan Profesional">
        <meta name="author" content="RR Consultant Indonesia">
        <meta property="og:title" content="RRCONS | Beranda - Solusi Bisnis Terbaik untuk Masa Depan">
        <meta property="og:description" content="Kami memberikan solusi yang sesuai kebutuhan bisnis Anda, dari layanan profesional hingga pelatihan dan media digital.">
        {{-- <meta property="og:image" content="{{ asset('images/home-og.jpg') }}"> --}}
        <meta property="og:url" content="{{ url()->current() }}">
        {{-- <meta name="twitter:card" content="summary_large_image"> --}}

        <!-- Tailwind CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

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
            @keyframes fadeInUp {
                0% {
                    opacity: 0;
                    transform: translateY(20px);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .fade-in-up {
                opacity: 0;
                animation: fadeInUp 1s ease forwards;
            }

            .card-zoom {
                transition: transform 0.5s ease, box-shadow 0.5s ease;
                transform-origin: center;
                cursor: pointer;
                z-index: 1;
                position: relative;
            }

            .card-zoom:hover {
                transform: scale(2) translateY(-25%);
                box-shadow: 0 15px 25px rgba(0, 0, 0, 0.25);
            }

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

            .card-hover {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .card-hover:hover {
                transform: scale(1.05) translateY(-5px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
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
                <div class="flex items-center space-x-3">
                    <div class="bg-black rounded-full shadow-lg">
                        <img src="{{ asset('front/image/logo.png') }}" alt="Logo" class="h-12 w-auto">
                    </div>
                    <div class="text-3xl font-bold">
                        <span class="text-gray-900">RR</span><span class="text-blue-600">CONS</span>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-6 font-medium">
                    <a href="{{ route('home') }}" class="relative nav-link {{ Request::routeIs('home') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">Home</a>
                    <a href="{{ route('rrfuture') }}" class="relative nav-link {{ Request::routeIs('rrfuture') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">RR Future</a>
                    <a href="{{ route('service') }}" class="relative nav-link {{ Request::routeIs('service') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">Service</a>
                    <a href="{{ route('blog') }}" class="relative nav-link {{ Request::routeIs('blog') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">Blog</a>
                    <a href="{{ route('contact') }}" class="relative nav-link {{ Request::routeIs('contact') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">Contact</a>
                </div>

                <!-- Language Switcher & Login -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Language Switcher -->
                    <div class="relative">
                        <button id="langToggleDesktop" class="flex items-center gap-2 text-sm text-gray-700 hover:text-blue-600 focus:outline-none">
                            <img src="{{ asset(app()->getLocale() === 'en' ? 'front/image/united-kingdom.png' : 'front/image/indonesia.png') }}" alt="Translate Icon" class="w-5 h-5" />
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.292l3.71-4.06a.75.75 0 011.1 1.02l-4.25 4.65a.75.75 0 01-1.1 0l-4.25-4.65a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="langDropdownDesktop" class="hidden absolute right-0 mt-2 min-w-[140px] bg-white rounded-md shadow-md z-50">
                            <a href="{{ route('set.language', 'id') }}" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:rounded-md">
                                <img src="{{ asset('front/image/indonesia.png') }}" alt="Indonesia" class="w-5 h-5" />
                                <span class="ml-5 font-medium {{ app()->getLocale() === 'id' ? 'text-blue-600' : '' }}">Indonesia</span>
                            </a>
                            <a href="{{ route('set.language', 'en') }}" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:rounded-md">
                                <img src="{{ asset('front/image/united-kingdom.png') }}" alt="English" class="w-5 h-5" />
                                <span class="ml-5 font-medium {{ app()->getLocale() === 'en' ? 'text-blue-600' : '' }}">English</span>
                            </a>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <a href="{{ route('login') }}" class="bg-blue-600 text-white text-lg px-8 py-2 rounded-full font-semibold hover:bg-blue-700 transition hover:scale-105 duration-300">Login</a>
                </div>

                <!-- Hamburger Menu -->
                <div class="md:hidden">
                    <button id="menu-toggle" class="focus:outline-none" aria-label="Toggle menu">
                        <svg class="w-7 h-7 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </nav>
        <!-- end of navbar -->

        <!-- start of mobile menu -->
        <div id="mobile-menu" class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col text-[20px]">
            <div class="p-6 flex items-center justify-between border-b">
                <div class="text-3xl font-semibold text-gray-900">Menu</div>
                <button id="close-menu" class="focus:outline-none" aria-label="Close menu">
                    <svg class="w-6 h-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-grow p-6 flex flex-col space-y-6">
                <a href="{{ route('home') }}" class="relative nav-link {{ Request::routeIs('home') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">Home</a>
                <a href="{{ route('rrfuture') }}" class="relative nav-link {{ Request::routeIs('rrfuture') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">RR Future</a>
                <a href="{{ route('service') }}" class="relative nav-link {{ Request::routeIs('service') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">Service</a>
                <a href="{{ route('blog') }}" class="relative nav-link {{ Request::routeIs('blog') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">Blog</a>
                <a href="{{ route('contact') }}" class="relative nav-link {{ Request::routeIs('contact') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]">Contact</a>
                <a href="{{ route('login') }}" class="inline-block bg-blue-600 text-white text-lg px-6 py-3 rounded-full font-semibold hover:bg-purple-700 transition self-start">Login</a>

                <!-- Language Switcher - Mobile -->
                <div class="md:hidden pl-6 pr-6 border-t border-gray-200 pt-4">
                    <div class="relative w-full">
                        <button id="langToggleMobile" class="flex justify-between items-center w-full px-4 py-2 text-sm text-gray-700 hover:text-blue-600 focus:outline-none">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('front/image/indonesia.png') }}" alt="Translate Icon" class="w-5 h-5" />
                                <span>Pilih Bahasa</span>
                            </div>
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.292l3.71-4.06a.75.75 0 011.1 1.02l-4.25 4.65a.75.75 0 01-1.1 0l-4.25-4.65a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div id="langDropdownMobile" class="hidden absolute right-0 mt-2 min-w-[140px] bg-white rounded-md shadow-md z-50">
                            <button class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:rounded-md" id="langIDDesktop">
                                <img src="{{ asset('front/image/indonesia.png') }}" alt="Indonesia" class="w-5 h-5" />
                                <span class="ml-5">Indonesia</span>
                            </button>
                            <button class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:rounded-md" id="langENDesktop">
                                <img src="{{ asset('front/image/united-kingdom.png') }}" alt="English" class="w-5 h-5" />
                                <span class="ml-5">English</span>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <!-- end of mobile menu -->

        <!-- start of hero -->
        @foreach ($heroes as $hero)
            <section class="container max-w-screen-xl rounded-[2rem] overflow-hidden mx-auto mt-10 px-4 relative text-white min-h-[600px] pt-36 sm:py-48 mb-50" style="background-image: url('{{ asset('storage/back/home/hero/' . $hero->bg_img) }}'); background-size: cover; background-position: center; margin-top: 8rem;">
                <div class="absolute inset-0 bg-black opacity-75"></div> <!-- Dark Overlay -->
                <div class="relative z-10 max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="uppercase text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-snug mb-4">
                        {!! highlightLastWord($hero->homeMenu->title ?? '') !!}
                    </h1>
                    <p class="text-sm sm:text-base md:text-lg leading-relaxed text-gray-200 max-w-3xl mx-auto">{{ $hero->homeMenu->sub_title ?? '' }}</p>
                </div>
            </section>
        @endforeach
        <!-- end of hero -->

        <!-- start of about -->
        @foreach ($abouts as $about)
            <section class="pt-36 max-w-6xl mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="uppercase text-4xl font-bold text-gray-900 mb-4">{!! highlightLastWord($about->homeMenu->title ?? '') !!}</h2>
                    <p class="text-lg text-gray-500">{{ $about->homeMenu->sub_title ?? '' }}</p>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="md:w-1/2 w-full flex justify-center">
                        <img src="{{ asset('storage/back/home/about/' . $about->img) }}" alt="Team Working"
                        class="rounded-2xl w-full max-w-sm scale-95 object-cover drop-shadow-[0_15px_30px_rgba(0,0,0,1)]">
                    </div>

                    <!-- Text -->
                    <div class="md:w-1/2 w-full flex flex-col justify-center text-justify text-gray-600 text-lg px-4">
                        <p class="mb-6">
                            {!! nl2br(e($about->text)) !!}
                        </p>
                    </div>
                </div>
            </section>
        @endforeach
        <!-- end of about -->

        <!-- start of services -->
        <section class="pt-36 bg-white">
            <div class="container max-w-screen-xl mx-auto px-4">
                <!-- Title -->
                @if ($services->first())
                    <div class="text-center mb-12">
                        <h2 class="uppercase text-4xl font-bold text-gray-900 mb-4">{!! highlightLastWord($services->first()->homeMenu->title ?? '') !!}</h2>
                        <p class="text-lg text-gray-500">{{ $services->first()->homeMenu->sub_title ?? '' }}</p>
                    </div>
                @endif

                <!-- Services grid -->
                <div class="flex flex-col sm:flex-row gap-8">
                    <!-- Column 1 -->
                    <div class="flex flex-col flex-1 space-y-8">
                        @php
                            $service1 = $services->firstWhere('id', 1);
                            $service2 = $services->firstWhere('id', 2);
                        @endphp
                        <div class="bg-white flex items-center p-6 rounded-2xl shadow-xl border border-purple-300 fade-in-up card-zoom w-full max-w-[900px]" style="animation-delay: 0.5s;">
                            <img src="{{ asset('front/image/service/cmp.png') }}" alt="{{ $service1->name }}" class="w-1/4 rounded-lg object-cover" />
                            <div class="pl-8">
                                <h3 class="text-blue-600 text-xl font-semibold mb-1">
                                    {{ $service1->name }}
                                </h3>
                                <p class="text-gray-600 text-sm font-normal">
                                    {{ $service1->desc }}
                                </p>
                            </div>
                        </div>
                        <div class="bg-white flex items-center p-6 rounded-2xl shadow-xl border border-purple-300 fade-in-up card-zoom w-full max-w-[900px]" style="animation-delay: 1s;">
                            <img src="{{ asset('front/image/service/srd.png') }}" alt="{{ $service2->name }}" class="w-1/4 rounded-lg object-cover" />
                            <div class="pl-8">
                                <h3 class="text-blue-600 text-xl font-semibold mb-1">
                                    {{ $service2->name }}
                                </h3>
                                <p class="text-gray-600 text-sm font-normal">
                                    {{ $service2->desc }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="flex flex-col flex-1 space-y-8 -mt-4 sm:mt-14">
                        @php
                            $service3 = $services->firstWhere('id', 3);
                            $service4 = $services->firstWhere('id', 4);
                        @endphp
                        <div class="bg-white flex items-center p-6 rounded-2xl shadow-xl border border-purple-300 fade-in-up card-zoom w-full max-w-[900px]" style="animation-delay: 0.5s;">
                            <img src="{{ asset('front/image/service/hcm.png') }}" alt="{{ $service3->name }}" class="w-1/4 rounded-lg object-cover" />
                            <div class="pl-8">
                                <h3 class="text-blue-600 text-xl font-semibold mb-1">
                                    {{ $service3->name }}
                                </h3>
                                <p class="text-gray-600 text-sm font-normal">
                                    {{ $service3->desc }}
                                </p>
                            </div>
                        </div>
                        <div class="bg-white flex items-center p-6 rounded-2xl shadow-xl border border-purple-300 fade-in-up card-zoom w-full max-w-[900px]" style="animation-delay: 1s;">
                            <img src="{{ asset('front/image/service/cb.png') }}" alt="{{ $service4->name }}" class="w-1/4 rounded-lg object-cover" />
                            <div class="pl-8">
                                <h3 class="text-blue-600 text-xl font-semibold mb-1">
                                    {{ $service4->name }}
                                </h3>
                                <p class="text-gray-600 text-sm font-normal">
                                    {{ $service4->desc }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end of End services -->

        <!-- start of Advantage -->
        <section class="pt-36 container max-w-screen-xl mx-auto px-4">
            <!-- Judul dan Deskripsi -->
            @if ($advantages->first())
                <div class="text-center mb-20">
                    <h2 class="uppercase text-4xl font-bold text-gray-900 mb-4">{!! highlightLastWord($advantages->first()->homeMenu->title ?? '') !!}</h2>
                    <p class="text-lg text-gray-500">{{ $advantages->first()->homeMenu->sub_title ?? '' }}</p>
                </div>
            @endif

            <!-- Grid Card -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($advantages as $index => $advantage)
                    @php
                        // Tentukan gambar berdasarkan urutan
                        $images = [
                            'front/image/superiority/profesional.png',
                            'front/image/superiority/ci.png',
                            'front/image/superiority/integrity.png',
                            'front/image/superiority/cso.png',
                        ];
                        $aos = ['fade-right', 'fade-up', 'fade-down', 'fade-left'];
                        $img = $images[$index] ?? $images[0];
                        $aosAnim = $aos[$index] ?? 'fade-up';
                    @endphp

                    <div class="group relative bg-white rounded-2xl shadow-md px-6 pt-16 pb-8 text-center transition duration-500 ease-in-out hover:scale-105 hover:shadow-2xl h-80 flex flex-col items-center justify-start overflow-visible mt-10 sm:mt-0" data-aos="{{ $aosAnim }}">
                        <div class="absolute top-7 group-hover:top-0 left-1/2 transform -translate-x-1/2 group-hover:-translate-y-1/2 transition-all duration-500 ease-in-out w-24 h-24 bg-blue-100 flex items-center justify-center rounded-full shadow-lg z-20 border-4 border-white">
                            <img src="{{ asset($img) }}" alt="Logo {{ $index + 1 }}" class="w-14 h-14 object-contain group-hover:scale-110 transition-transform duration-500" />
                        </div>
                        <div class="mt-20 text-center transition-all duration-500 ease-in-out">
                            <h2 class="text-theme-blue text-lg font-semibold mb-2 group-hover:scale-105 group-hover:-translate-y-10 transition-all">
                                {{ strtoupper($advantage->title) }}
                            </h2>
                            <p class="text-gray-500 text-sm px-2 group-hover:scale-105 group-hover:-translate-y-4 group-hover:opacity-90 transition-all">
                                {{ $advantage->desc }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        <!-- end of Advantage -->

        <!-- start of team -->
        <section class="pt-36 px-4 max-w-screen-xl mx-auto px-4 text-center">
            @if ($profiles->first())
                <div class="text-center mb-12">
                    <h2 class="uppercase text-4xl font-bold text-gray-900 mb-4">{!! highlightLastWord($profiles->first()->homeMenu->title ?? '') !!}</h2>
                    <p class="text-lg text-gray-500">{{ $profiles->first()->homeMenu->sub_title ?? '' }}</p>
                </div>
            @endif

            <div class="flex justify-center items-center mb-6 space-x-4">
                <button id="scrollLeft" class="w-10 h-10 rounded-full text-blue-600 hover:bg-gray-200 flex items-center justify-center text-xl shadow">
                    &lt;
                </button>
                <button id="scrollRight" class="w-10 h-10 rounded-full text-blue-600 hover:bg-gray-200 flex items-center justify-center text-xl shadow">
                    &gt;
                </button>
            </div>

            <!-- Carousel Wrapper -->
            <div id="carousel" class="overflow-hidden">
                <div class="flex space-x-6 px-2 mb-1">

                    <!-- Cards -->
                    @foreach ($profiles as $profile)
                        <div class="bg-white shadow rounded-xl overflow-hidden text-center p-4 flex-shrink-0 w-64 scroll-snap-item">
                            <img src="{{ asset('storage/back/home/profile/' . $profile->img) }}" alt="{{ $profile->name }}" class="mx-auto mb-4 w-full h-80 object-cover" />
                            <h3 class="font-semibold text-md">{{ $profile->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $profile->position }}</p>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
        <!-- end of team -->

        <!-- start of bussiness coaching -->
        <section class="pt-36 px-4 max-w-6xl mx-auto text-center">

            @if ($business->first())
                <div class="text-center mb-12">
                    <h2 class="uppercase text-4xl font-bold text-gray-900 mb-4">{!! highlightLastWord($business->first()->homeMenu->title ?? '') !!}</h2>
                    <p class="text-lg text-gray-500">{{ $business->first()->homeMenu->sub_title ?? '' }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($business as $item)
                    <div class="transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="w-full aspect-[4/3] overflow-hidden">
                            <img
                                src="{{ asset('storage/back/home/business/' . $item->img) }}"
                                alt="{{ $item->title }}"
                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-105 coaching-image cursor-pointer"
                            >
                        </div>
                        <div class="p-4 text-left">
                            <h3 class="font-semibold text-lg text-gray-800 mb-2">{{ $item->title }}</h3>
                            <p class="text-gray-600 text-sm mb-3">{{ $item->desc }}</p>
                            <a href="{{ $item->form_link }}" target="_blank" class="inline-block px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Daftar Sekarang
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Lightbox -->
            <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
                <div class="relative max-w-4xl mx-auto p-2 sm:p-4 rounded-lg">

                    <!-- Gambar & Tombol X dalam satu container -->
                    <div class="relative inline-block">
                        <!-- Tombol Close di dalam gambar -->
                        <button id="close-lightbox"
                        class="absolute top-2 right-2 z-10 text-white text-lg font-bold cursor-pointer
                            rounded-full bg-gray-900 w-8 h-8 flex items-center justify-center shadow-md">
                        &times;
                        </button>
                        <!-- Gambar -->
                        <img src="" alt="Full size image" id="lightbox-img" class="max-w-full max-h-[80vh] rounded-md">
                    </div>

                </div>
            </div>

        </section>
        <!-- end of bussiness coaching -->

        <!-- start of social media -->
        <section class="pt-36 px-6 max-w-6xl mx-auto text-center">

            @if ($sosmed->first())
                <div class="text-center mb-12">
                    <h2 class="uppercase text-4xl font-bold text-gray-900 mb-4">{!! highlightLastWord($sosmed->first()->homeMenu->title ?? '') !!}</h2>
                    <p class="text-lg text-gray-500">{{ $sosmed->first()->homeMenu->sub_title ?? '' }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($sosmed as $index => $item)
                    <div class="card-hover bg-white rounded-xl shadow-md overflow-hidden" data-aos="zoom-in">
                        <div class="w-full aspect-[9/16] overflow-hidden relative group" data-video-id="video{{ $index }}">
                            <video id="video{{ $index }}" class="w-full h-full object-cover transition-transform duration-300" muted loop playsinline preload="metadata">
                                <source src="{{ asset('storage/back/home/sosmed/' . $item->video) }}" type="video/mp4">
                                Browser tidak mendukung video.
                            </video>
                            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-25 group-hover:bg-opacity-40 transition pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="video-icon h-12 w-12 text-white opacity-80 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-5.197-3.03A1 1 0 008 9v6a1 1 0 001.555.832l5.197-3.03a1 1 0 000-1.664z" />
                                </svg>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-gray-800 mb-2">{{ $item->title }}</h3>
                            @php
                                $platformText = 'Lihat di Sosial Media';

                                if (Str::contains($item->sosmed_link, 'instagram.com')) {
                                    $platformText = 'Lihat di Instagram';
                                } elseif (Str::contains($item->sosmed_link, 'tiktok.com')) {
                                    $platformText = 'Lihat di TikTok';
                                } elseif (Str::contains($item->sosmed_link, 'youtube.com') || Str::contains($item->sosmed_link, 'youtu.be')) {
                                    $platformText = 'Lihat di YouTube';
                                }
                            @endphp

                            <a href="{{ $item->sosmed_link }}" target="_blank" class="inline-block mt-2 px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                {{ $platformText }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </section>
        <!-- end of social media -->

        <!-- start of galery -->
        <section class="pt-36 text-center max-w-screen-xl mx-auto px-6">
            <!-- Judul & Deskripsi -->
            @if ($gallery->first())
                <div class="text-center mb-12">
                    <h2 class="uppercase text-4xl font-bold text-gray-900 mb-4">{!! highlightLastWord($gallery->first()->homeMenu->title ?? '') !!}</h2>
                    <p class="text-lg text-gray-500">{{ $gallery->first()->homeMenu->sub_title ?? '' }}</p>
                </div>
            @endif

            <!-- Dropdown Filter -->
            <div class="flex flex-wrap justify-center items-center gap-3 mb-8 text-sm">
                <!-- Dropdown Bulan -->
                <select id="filterMonth" class="border border-gray-300 rounded-full px-4 py-1 focus:ring focus:ring-blue-400">
                    <option value="all" selected>-- Semua Bulan --</option>
                    @foreach ($months as $m)
                        @php
                            $namaBulan = \Carbon\Carbon::create()->month($m)->translatedFormat('F');
                            $valBulan = str_pad($m, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <option value="{{ $valBulan }}">{{ $namaBulan }}</option>
                    @endforeach
                </select>

                <!-- Dropdown Tahun -->
                <select id="filterYear" class="border border-gray-300 rounded-full px-4 py-1 focus:ring focus:ring-blue-400 text-sm">
                    <option value="all" selected>-- Semua Tahun --</option>
                    @foreach ($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Gallery Items -->
            <div id="galleryWrapper" class="columns-1 sm:columns-2 md:columns-3 gap-4 space-y-4">
                @foreach ($gallery as $item)
                    @php
                        $bulanTahun = \Carbon\Carbon::create($item->year, $item->month)->translatedFormat('F Y');
                        $tanggalDataAttr = \Carbon\Carbon::create($item->year, $item->month)->format('Y-m');
                    @endphp

                    <div class="relative group overflow-hidden rounded-xl shadow-xl" data-year="{{ $item->year }}" data-date="{{ $tanggalDataAttr }}">
                        <img src="{{ asset('storage/back/home/gallery/' . $item->img) }}" alt="{{ $item->title }}" class="w-full object-cover h-auto group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-black/20 transition duration-500 ease-in-out group-hover:bg-black/30"></div>
                        <div class="absolute bottom-0 left-0 px-4 py-3 text-white opacity-0 group-hover:opacity-100 transition duration-500">
                            <div class="bg-black/60 px-3 py-2 rounded-tr-xl">
                                <p class="text-base font-semibold">{{ $item->title }}</p>
                                <p class="text-sm opacity-80">{{ $bulanTahun }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </section>
        <!-- end of galery -->

        <!-- start of blog -->
        <section class="pt-36 max-w-screen-xl mx-auto text-gray-900 px-4 mb-32">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12">
                <!-- Left Title -->
                <div>
                    <h2 class="uppercase text-4xl font-bold text-gray-900 mb-4">
                        Temukan Blog Terbaru <br><span class="text-blue-600 text-left">Kami</span>
                    </h2>
                </div>

                <!-- Right Filter -->
                <div id="categoryFilter" class="text-md font-semibold text-gray-500 space-x-4">
                    <a href="#" data-category="" class="category-link {{ request('category') ? 'hover:underline' : 'text-blue-600 italic' }}">Semua Blog</a>

                    @foreach ($categories as $category)
                        <a href="#" data-category="{{ $category->slug }}" class="category-link {{ request('category') === $category->slug ? 'text-blue-600 italic' : 'hover:underline' }}">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>

            <!-- Section: Articles Grid -->
            <div id="articlesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @include('front.partials.articles', ['articles' => $articles])
            </div>

            <!-- Section: Call to Action Button -->
            <div class="mt-10 text-center">
                <a href="{{ route('blog') }}">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded shadow-lg">
                        Lihat Semua Blog
                    </button>
                </a>
            </div>
        </section>
        <!-- end of blog -->

        <!-- start of whatsapp -->
        <section id="whatsapp-support" class="relative">

            <!-- Tombol WhatsApp -->
            <button id="waBtn" class="fixed bottom-6 right-6 bg-green-500 rounded-full p-3 shadow-lg hover:bg-green-600 transition duration-300 z-50">
                <img src="https://img.icons8.com/ios-filled/30/ffffff/whatsapp.png" alt="WhatsApp Icon" />
            </button>

            <!-- Chat Box -->
            <div id="chatBox" class="fixed bottom-24 right-6 w-80 rounded-xl shadow-lg overflow-hidden hidden z-40">

                <!-- Header -->
                <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('front/image/logo.png') }}" alt="Logo" class="rounded-2xl w-12 h-12 bg-black rounded-full shadow-lg" />
                        <div>
                            <div class="font-bold text-sm">Support RRCONS</div>
                            <div class="text-xs text-green-500">online</div>
                        </div>
                    </div>
                    <button id="closeBtn" class="text-white text-xl font-bold">&times;</button>
                </div>

                <!-- Body dengan background gambar -->
                <div class="p-4 bg-white bg-opacity-80">
                    <!-- Bubble Chat -->
                    <div class="mb-6">
                        <div class="text-sm font-semibold text-gray-700 mb-1">Support RRCONS</div>
                        <div class="bg-white text-sm text-gray-800 p-3 rounded-lg shadow">
                            Hi,<br />Ada yang bisa kami bantu?
                        </div>
                    </div>
                </div>

                <!-- Footer putih dengan tombol dan info -->
                <div class="bg-white px-4 pt-4 pb-6 text-center">
                    <a href="https://wa.me/6281234567890?text=Halo%20saya%20mau%20bertanya" target="_blank" rel="noopener noreferrer">
                        <button class="bg-green-500 text-white text-sm font-semibold w-full py-3 rounded-full shadow hover:bg-green-600 transition duration-300">
                        Chat Sekarang
                        </button>
                    </a>
                    <p class="text-xs text-gray-500 mt-2">⚡ by <span class="text-green-600 font-semibold">Solveit</span></p>
                </div>
            </div>
        </section>
        <!-- end of whatsapp -->

        <!-- start of footer -->
        <footer class="py-10 mt-36 max-w-screen-xl mx-auto bg-white border-t border-gray-200 rounded-lg px-4">
            <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 items-start">

                <!-- Logo & Description -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('front/image/logo.png') }}" alt="RR Logo" class="h-8 w-8">
                        <h1 class="text-xl font-semibold">Rita Russo <span class="text-blue-600">Consultant</span></h1>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Sejak 2012, RR Cons, RR Future Tech, dan RR Vision telah menjadi mitra terpercaya bagi berbagai lembaga dan perusahaan, menghadirkan solusi yang disesuaikan dengan kebutuhan bisnis melalui kolaborasi, keahlian teknis, dan kreativitas
                    </p>
                </div>

                <!-- Address - pl 24 hanya di desktop -->
                <div class="sm:pl-0 md:pl-24">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">Alamat</h3>
                    <p class="text-gray-500">
                        Jl. Perum Gadang Regency No.P2 No. 44, Gadang, Kec. Sukun, Kota Malang, Jawa Timur 65149
                    </p>
                </div>

                <!-- Contact - pl 32 hanya di desktop -->
                <div class="sm:pl-0 md:pl-32">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">Kontak</h3>
                    <p class="text-gray-500">+91 945 658 3256</p>
                    <p class="text-gray-500">support@pagedone.com</p>
                </div>

                <!-- Office - ml-auto hanya di desktop -->
                <div class="ml-0 md:ml-auto">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">Jam Kantor</h3>
                    <p class="text-gray-500">Senin - Jum'at</p>
                    <p class="text-gray-500">08.00 - 16.00</p>
                    <p class="text-gray-500">Sabtu</p>
                    <p class="text-gray-500">08.00 - 12.00</p>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-6"></div>

            <!-- Bottom Footer -->
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 space-y-4 md:space-y-0">
                <p><span class="text-blue-600">RRCONS 2025</span> , All rights reserved.</p>

                <div class="flex space-x-4">
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/rrcons_/" aria-label="Instagram" target="_blank">
                        <svg class="w-6 h-6 text-gray-600 hover:text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5A4.25 4.25 0 0 0 20.5 16.25v-8.5A4.25 4.25 0 0 0 16.25 3.5h-8.5Zm9 2.25a.75.75 0 0 1 1.5 0v1a.75.75 0 0 1-1.5 0v-1ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"/>
                        </svg>
                    </a>

                    <!-- TikTok -->
                    <a href="#" aria-label="TikTok" target="_blank">
                        <svg class="w-6 h-6 text-gray-600 hover:text-black" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.75 2h2.5a5.25 5.25 0 0 0 5.25 5.25v2.5a7.75 7.75 0 0 1-5.25-1.85v7.1a6.25 6.25 0 1 1-6.25-6.25h.75v2.5h-.75a3.75 3.75 0 1 0 3.75 3.75V2Z"/>
                        </svg>
                    </a>

                    <!-- X -->
                    <a href="#" aria-label="X" target="_blank">
                        <svg class="w-6 h-6 text-gray-600 hover:text-black" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.5 10.5 21 3h-2.25l-5.25 6.25L9 3H3l6.93 10.02L3 21h2.25l5.7-6.77L15 21h6l-6.5-10.5Zm-2.07 2.45-.66-.98L5.1 4.5H8.1l4.17 6.15.66.98 6.93 10.02H16.9l-4.47-6.65Z"/>
                        </svg>
                    </a>

                    <!-- Facebook -->
                    <a href="#" aria-label="Facebook" target="_blank">
                        <svg class="w-6 h-6 text-gray-600 hover:text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.5 3H10A4 4 0 0 0 6 7v3H3v4h3v7h4v-7h3.5l1-4H10V7a1 1 0 0 1 1-1h3.5V3Z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </footer>
        <!-- end of footer -->

        <!-- start of poster -->
        <section x-data="popupController" x-init="init()" class="relative">

            <!-- Gambar sumber yang disembunyikan -->
            <div id="image-source" class="hidden">
                @foreach ($posters as $poster)
                    <img src="{{ asset('storage/back/home/poster/' . $poster->img) }}" alt="{{ $poster->name }}" />
                @endforeach
            </div>

            <!-- Popup -->
            <div x-show="open" x-transition.opacity.scale class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">

                <div @click.away="open = false" class="relative p-0 bg-transparent shadow-none">

                    <!-- Tombol Close -->
                    <button @click="open = false" class="absolute top-2 right-2 bg-white/80 hover:bg-red-500 hover:text-white transition p-1.5 rounded-full z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Gambar Poster -->
                    <img :src="images[currentIndex]" class="w-auto h-auto max-w-[90vw] max-h-[90vh] object-contain rounded-xl" />

                    <!-- Tombol Navigasi Kiri -->
                    <button @click="goToPrevImage()" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/70 hover:bg-gray-300 p-2 rounded-full z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <!-- Tombol Navigasi Kanan -->
                    <button @click="goToNextImage()" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/70 hover:bg-gray-300 p-2 rounded-full z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                </div>
            </div>

        </section>
        <!-- end of poster -->

        <!-- Render Feather Icons -->
        <script>
            feather.replace();
        </script>

        <!-- Filtering Dropdown Gallery JS -->
        <script>
            // Fungsi utama filter galeri
            function filterGallery() {
                const month = document.getElementById('filterMonth').value;
                const year = document.getElementById('filterYear').value;
                const items = document.querySelectorAll('[data-date]');

                items.forEach(item => {
                    const [itemYear, itemMonth] = item.getAttribute('data-date').split('-');

                    const matchMonth = (month === 'all') || (itemMonth === month);
                    const matchYear = (year === 'all') || (itemYear === year);

                    const shouldShow = matchMonth && matchYear;
                    item.classList.toggle('hidden', !shouldShow);
                });
            }

            // Jalankan filter saat halaman dimuat & saat dropdown berubah
            document.addEventListener('DOMContentLoaded', () => {
                const monthDropdown = document.getElementById('filterMonth');
                const yearDropdown = document.getElementById('filterYear');

                monthDropdown.addEventListener('change', filterGallery);
                yearDropdown.addEventListener('change', filterGallery);

                // Filter awal saat load
                filterGallery();
            });
        </script>

        <!-- Category Filtering Blog JS -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const links = document.querySelectorAll('.category-link');
                const articlesGrid = document.getElementById('articlesGrid');

                links.forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        const slug = this.dataset.category;

                        // Tampilkan loading jika mau
                        articlesGrid.innerHTML = '<p class="text-gray-500 col-span-3">Memuat artikel...</p>';

                        fetch(`/get-articles?category=${slug}`)
                            .then(res => res.json())
                            .then(data => {
                                articlesGrid.innerHTML = data.html;

                                // Highlight kategori aktif
                                links.forEach(l => l.classList.remove('text-blue-600', 'italic'));
                                this.classList.add('text-blue-600', 'italic');
                            })
                            .catch(() => {
                                articlesGrid.innerHTML = '<p class="text-red-500 col-span-3">Gagal memuat artikel.</p>';
                            });
                    });
                });
            });
        </script>

        <!-- AOS JS -->
        <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
        <script>
            AOS.init({
            duration: 800,
            once: true,
            });
        </script>
        <script>
            // Cek jika lebar layar kurang dari 768px (tailwind breakpoint untuk sm)
            if (window.innerWidth < 768) {
                document.querySelectorAll('[data-aos]').forEach(el => {
                el.removeAttribute('data-aos');
                el.removeAttribute('data-aos-delay');
                });
            }
        </script>

        <script src="{{ asset('front/js/index.js') }}"></script>

    </body>
</html>
