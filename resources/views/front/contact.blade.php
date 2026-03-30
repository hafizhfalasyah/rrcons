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
        <title>RRCONS | Kontak - Konsultasi dan Solusi Cepat</title>

        <!-- SEO Meta Tag -->
        <meta name="description" content="Ingin konsultasi atau membutuhkan layanan kami? Hubungi RR Group sekarang juga melalui form kontak yang tersedia. Kami siap membantu Anda kapan saja.">
        <meta name="keywords" content="Hubungi Kami, RR Group, Konsultasi Bisnis, Solusi Cepat, Form Kontak, Bantuan Pelanggan">
        <meta name="author" content="RR Consultant Indonesia">
        <meta property="og:title" content="RRCONS | Kontak - Konsultasi dan Solusi Cepat">
        <meta property="og:description" content="Form kontak untuk mendapatkan bantuan dan solusi terbaik dari RR Consultant, RR Future, dan RR Vision.">
        {{-- <meta property="og:image" content="{{ asset('images/contact-og.jpg') }}"> --}}
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
                            <img src="{{ asset('front/image/indonesia.png') }}" alt="Translate Icon" class="w-5 h-5" />
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.292l3.71-4.06a.75.75 0 011.1 1.02l-4.25 4.65a.75.75 0 01-1.1 0l-4.25-4.65a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="langDropdownDesktop" class="hidden absolute right-0 mt-2 min-w-[140px] bg-white rounded-md shadow-md z-50">
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
            <section class="container max-w-screen-xl rounded-[2rem] overflow-hidden mx-auto mt-10 px-4 relative text-white min-h-[600px] pt-36 sm:py-48 mb-50" style="background-image: url('{{ asset('storage/back/contact/hero/' . $hero->bg_img) }}'); background-size: cover; background-position: center; margin-top: 8rem;">
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
                                <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                                <input type="text" placeholder="Nama Lengkap" class="w-full p-3 border border-gray-300 rounded" />
                            </div>
                            <div class="w-full md:w-1/2 mt-4 md:mt-0">
                                <label class="block text-sm font-medium mb-1">Email Address</label>
                                <input type="email" placeholder="Email" class="w-full p-3 border border-gray-300 rounded" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Nomer Telepon</label>
                            <input type="text" placeholder="Nomer Telepon" class="w-full p-3 border border-gray-300 rounded" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Pesan</label>
                            <textarea rows="4" placeholder="Pesan Kamu..." class="w-full p-3 border border-gray-300 rounded resize-none"></textarea>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded shadow">Kirim Pesan</button>
                    </form>
                </div>

                <!-- Google Map -->
                <div class="w-full md:w-1/2">
                    <iframe class="w-full h-full min-h-[400px]" src="https://maps.google.com/maps?q=Jl.%20Perum%20Gadang%20Regency%20No.P2%20No.%2044%2C%20Gadang%2C%20Kec.%20Sukun%20Kota%20Malang%20Jawa%20Timur&t=&z=17&ie=UTF8&iwloc=&output=embed" frameborder="0" allowfullscreen></iframe>
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
                            <span class="font-semibold">Alamat:</span><br>
                            {!! $us['Alamat'] ?? '-' !!}
                        </p>
                    </div>

                    <div>
                        <div class="flex justify-center text-blue-600 text-3xl mb-2">
                            <i data-feather="phone"></i>
                        </div>
                        <p>
                            <span class="font-semibold">Telepon:</span><br>
                            {{ $us['Telepon'] ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <div class="flex justify-center text-blue-600 text-3xl mb-2">
                            <i data-feather="mail"></i>
                        </div>
                        <p>
                            <span class="font-semibold">Email:</span><br>
                            <a href="mailto:{{ $us['Email'] ?? '' }}" class="text-blue-500">
                                {{ $us['Email'] ?? '-' }}
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </section>
        <!-- end of contact -->

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
                        <h1 class="text-xl font-semibold">Rita <span class="text-blue-600">Russo</span></h1>
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
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">Contact</h3>
                    <p class="text-gray-500">+91 945 658 3256</p>
                    <p class="text-gray-500">support@pagedone.com</p>
                </div>

                <!-- Office - ml-auto hanya di desktop -->
                <div class="ml-0 md:ml-auto">
                    <h3 class="text-lg text-blue-600 font-semibold mb-2">Office</h3>
                    <p class="text-gray-500">Monday - Friday</p>
                    <p class="text-gray-500">9AM - 7PM</p>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-6"></div>

            <!-- Bottom Footer -->
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 space-y-4 md:space-y-0">
                <p><span class="text-blue-600">Solveit 2025</span> , All rights reserved.</p>

                <div class="flex space-x-6">
                    <a href="#" class="hover:underline">Terms</a>
                    <a href="#" class="hover:underline">Privacy</a>
                    <a href="#" class="hover:underline">Cookies</a>
                </div>

                <div class="flex space-x-4">
                    <!-- Instagram -->
                    <a href="#" aria-label="Instagram">
                    <svg class="w-6 h-6 text-gray-600 hover:text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5A4.25 4.25 0 0 0 20.5 16.25v-8.5A4.25 4.25 0 0 0 16.25 3.5h-8.5Zm9 2.25a.75.75 0 0 1 1.5 0v1a.75.75 0 0 1-1.5 0v-1ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"/>
                    </svg>
                    </a>

                    <!-- TikTok -->
                    <a href="#" aria-label="TikTok">
                    <svg class="w-6 h-6 text-gray-600 hover:text-black" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.75 2h2.5a5.25 5.25 0 0 0 5.25 5.25v2.5a7.75 7.75 0 0 1-5.25-1.85v7.1a6.25 6.25 0 1 1-6.25-6.25h.75v2.5h-.75a3.75 3.75 0 1 0 3.75 3.75V2Z"/>
                    </svg>
                    </a>

                    <!-- X -->
                    <a href="#" aria-label="X">
                    <svg class="w-6 h-6 text-gray-600 hover:text-black" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.5 10.5 21 3h-2.25l-5.25 6.25L9 3H3l6.93 10.02L3 21h2.25l5.7-6.77L15 21h6l-6.5-10.5Zm-2.07 2.45-.66-.98L5.1 4.5H8.1l4.17 6.15.66.98 6.93 10.02H16.9l-4.47-6.65Z"/>
                    </svg>
                    </a>

                    <!-- Facebook -->
                    <a href="#" aria-label="Facebook">
                    <svg class="w-6 h-6 text-gray-600 hover:text-blue-600" fill="currentColor" viewBox="0 0 24 24">
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
