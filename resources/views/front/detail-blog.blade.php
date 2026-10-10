@php
    $currentLocale = app()->getLocale();

    $activeLanguageFlag = $currentLocale === 'en'
        ? 'front/image/united-kingdom.png'
        : 'front/image/indonesia.png';

    $activeLanguageName = $currentLocale === 'en'
        ? __('blog.navigation.language.english')
        : __('blog.navigation.language.indonesian');

    $whatsappUrl = 'https://wa.me/6281252624284?text='
        . urlencode(__('blog.common.message'));
@endphp

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', $currentLocale) }}"
    x-data="{ open: false }"
    xmlns="http://www.w3.org/1999/xhtml"
>
<head>
    <meta charset="UTF-8">

    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>{{ $article->title }} | RRCONS</title>

    <!-- SEO Meta Tags -->
    <meta
        name="description"
        content="{{ Str::limit(strip_tags($article->desc), 160) }}"
    >

    <meta
        name="keywords"
        content="{{ __('blog.seo.detail.keywords') }}"
    >

    <meta
        name="author"
        content="{{ __('blog.seo.author') }}"
    >

    <meta
        property="og:title"
        content="{{ $article->title }} | RRCONS"
    >

    <meta
        property="og:description"
        content="{{ Str::limit(strip_tags($article->desc), 160) }}"
    >

    <meta
        property="og:image"
        content="{{ asset('storage/' . $article->img) }}"
    >

    <meta
        property="og:url"
        content="{{ url()->current() }}"
    >

    <meta
        property="og:type"
        content="article"
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
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- Feather Icons -->
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js"
        crossorigin="anonymous"
    ></script>

    <!-- Alpine.js -->
    <script
        defer
        src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"
    ></script>

    <!-- AOS CSS -->
    <link
        href="https://unpkg.com/aos@2.3.4/dist/aos.css"
        rel="stylesheet"
    >

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
            background-color: #3b82f6;
            transition: width 0.3s ease-in-out;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            width: 100%;
            height: 4px;
        }

        .body::-webkit-scrollbar {
            width: 5px;
        }

        .body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .body::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 20px;
        }

        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }

        .article-content p {
            margin-bottom: 1rem;
        }

        .article-content ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .article-content ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .article-content h2,
        .article-content h3,
        .article-content h4 {
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .article-content a {
            color: #2563eb;
            text-decoration: underline;
        }

        .article-content blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 1rem;
            margin: 1.5rem 0;
            color: #4b5563;
            font-style: italic;
        }
    </style>
</head>

<body class="body font-body" x-data="{ open: false }">
    <!-- Start of Navbar -->
    <nav class="fixed top-0 left-0 w-full py-5 shadow-sm bg-white z-40 text-lg">
        <div class="max-w-screen-xl mx-auto px-6 flex items-center justify-between">
            <a
                href="{{ route('home') }}"
                class="flex items-center space-x-3"
                aria-label="{{ __('blog.navigation.brand.name') }}"
            >
                <div class="rounded-full bg-black shadow-lg">
                    <img
                        src="{{ asset('front/image/logo.png') }}"
                        alt="{{ __('blog.navigation.brand.logo_alt') }}"
                        class="h-12 w-auto"
                    >
                </div>

                <div class="text-3xl font-bold">
                    <span class="text-gray-900">RR</span><span class="text-blue-600">CONS</span>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-6 font-medium">
                <a
                    href="{{ route('home') }}"
                    class="relative nav-link {{ Request::routeIs('home') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('blog.navigation.menu.home') }}
                </a>

                <a
                    href="{{ route('rrfuture') }}"
                    class="relative nav-link {{ Request::routeIs('rrfuture') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('blog.navigation.menu.rr_future') }}
                </a>

                <a
                    href="{{ route('service') }}"
                    class="relative nav-link {{ Request::routeIs('service') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('blog.navigation.menu.service') }}
                </a>

                <a
                    href="{{ route('blog') }}"
                    class="relative nav-link {{ Request::routeIs('blog', 'detail_blog') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('blog.navigation.menu.blog') }}
                </a>

                <a
                    href="{{ route('contact') }}"
                    class="relative nav-link {{ Request::routeIs('contact') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }} tracking-[0.5px] px-[10px]"
                >
                    {{ __('blog.navigation.menu.contact') }}
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
                        aria-label="{{ __('blog.navigation.language.open_selector') }}"
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
                                alt="{{ __('blog.navigation.language.indonesian') }}"
                                class="h-5 w-5 rounded-full object-cover"
                            >

                            <span class="ml-3">
                                {{ __('blog.navigation.language.indonesian') }}
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
                                alt="{{ __('blog.navigation.language.english') }}"
                                class="h-5 w-5 rounded-full object-cover"
                            >

                            <span class="ml-3">
                                {{ __('blog.navigation.language.english') }}
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
                    {{ __('blog.navigation.menu.login') }}
                </a>
            </div>

            <!-- Hamburger Menu -->
            <div class="md:hidden">
                <button
                    id="menu-toggle"
                    type="button"
                    class="focus:outline-none"
                    aria-label="{{ __('blog.navigation.mobile.open_menu') }}"
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
    <!-- End of Navbar -->

    <!-- Start of Mobile Menu -->
    <div
        id="mobile-menu"
        class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col text-[20px]"
    >
        <div class="p-6 flex items-center justify-between border-b">
            <div class="text-3xl font-semibold text-gray-900">
                {{ __('blog.navigation.mobile.title') }}
            </div>

            <button
                id="close-menu"
                type="button"
                class="focus:outline-none"
                aria-label="{{ __('blog.navigation.mobile.close_menu') }}"
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
            <a
                href="{{ route('home') }}"
                class="relative nav-link {{ Request::routeIs('home') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}"
            >
                {{ __('blog.navigation.menu.home') }}
            </a>

            <a
                href="{{ route('rrfuture') }}"
                class="relative nav-link {{ Request::routeIs('rrfuture') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}"
            >
                {{ __('blog.navigation.menu.rr_future') }}
            </a>

            <a
                href="{{ route('service') }}"
                class="relative nav-link {{ Request::routeIs('service') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}"
            >
                {{ __('blog.navigation.menu.service') }}
            </a>

            <a
                href="{{ route('blog') }}"
                class="relative nav-link {{ Request::routeIs('blog', 'detail_blog') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}"
            >
                {{ __('blog.navigation.menu.blog') }}
            </a>

            <a
                href="{{ route('contact') }}"
                class="relative nav-link {{ Request::routeIs('contact') ? 'active text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}"
            >
                {{ __('blog.navigation.menu.contact') }}
            </a>

            <a
                href="{{ route('login') }}"
                class="inline-block bg-blue-600 text-white text-lg px-6 py-3 rounded-full font-semibold hover:bg-blue-700 transition self-start"
            >
                {{ __('blog.navigation.menu.login') }}
            </a>

            <!-- Mobile Language Switcher -->
            <div class="border-t border-gray-200 pt-4">
                <div class="relative w-full">
                    <button
                        id="langToggleMobile"
                        type="button"
                        aria-label="{{ __('blog.navigation.accessibility.language_dropdown') }}"
                        aria-expanded="false"
                        aria-controls="langDropdownMobile"
                        class="flex justify-between items-center w-full px-4 py-2 text-sm text-gray-700 hover:text-blue-600 focus:outline-none"
                    >
                        <div class="flex items-center gap-2">
                            <img
                                src="{{ asset($activeLanguageFlag) }}"
                                alt="{{ $activeLanguageName }}"
                                class="w-5 h-5"
                            >

                            <span>
                                {{ __('blog.navigation.language.select') }}
                            </span>
                        </div>

                        <svg
                            class="w-4 h-4 ml-1"
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
                        class="hidden absolute left-0 mt-2 min-w-[160px] bg-white rounded-md shadow-md z-50 overflow-hidden"
                    >
                        <a
                            href="{{ route('set.language', 'id') }}"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        >
                            <img
                                src="{{ asset('front/image/indonesia.png') }}"
                                alt="{{ __('blog.navigation.language.indonesian_flag_alt') }}"
                                class="w-5 h-5"
                            >

                            <span class="ml-5">
                                {{ __('blog.navigation.language.indonesian') }}
                            </span>
                        </a>

                        <a
                            href="{{ route('set.language', 'en') }}"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        >
                            <img
                                src="{{ asset('front/image/united-kingdom.png') }}"
                                alt="{{ __('blog.navigation.language.english_flag_alt') }}"
                                class="w-5 h-5"
                            >

                            <span class="ml-5">
                                {{ __('blog.navigation.language.english') }}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- End of Mobile Menu -->

    <!-- Start of Blog Detail -->
    <main class="max-w-7xl mx-auto px-6 py-20 pt-36">
        <!-- Article Header -->
        <section class="mb-10">
            <h1 class="text-4xl font-bold leading-tight mb-4">
                {{ $article->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600">
                <time datetime="{{ ($article->publish_date ?? $article->created_at)->toIso8601String() }}">
                    {{ ($article->publish_date ?? $article->created_at)
                        ->locale($currentLocale)
                        ->translatedFormat(__('blog.sections.blog.detail.date.format')) }}
                </time>

                <span
                    class="w-1 h-1 bg-gray-400 rounded-full"
                    aria-hidden="true"
                ></span>

                <span class="text-blue-600 font-medium">
                    {{ $article->category->name
                        ?? __('blog.sections.blog.detail.category.uncategorized') }}
                </span>

                <span
                    class="w-1 h-1 bg-gray-400 rounded-full"
                    aria-hidden="true"
                ></span>

                <span>
                    {{ $article->author
                        ?? __('blog.sections.blog.detail.author.default') }}
                </span>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Main Content -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Featured Image -->
                <section>
                    <img
                        src="{{ asset('storage/' . $article->img) }}"
                        alt="{{ $article->title }}"
                        class="w-full rounded-md mb-4"
                    >
                </section>

                <!-- Article Content -->
                <article class="article-content space-y-5 text-md text-gray-800 leading-relaxed text-justify">
                    {!! $article->desc !!}
                </article>

                <!-- Tags -->
                @if (!empty($article->tags))
                    <section class="pt-6 flex flex-wrap gap-2">
                        @foreach (explode(',', $article->tags) as $tag)
                            <span class="bg-blue-100 text-blue-600 text-sm px-3 py-1 rounded-full">
                                {{ trim($tag) }}
                            </span>
                        @endforeach
                    </section>
                @endif

                <!-- Comment Form -->
                <section class="pt-20">
                    <h2 class="text-xl font-semibold mb-4">
                        {{ __('blog.sections.blog.detail.response.heading') }}
                    </h2>

                    @if (session('success'))
                        <div
                            class="text-green-700 bg-green-50 border border-green-200 rounded-md p-4 font-medium mb-4"
                            role="status"
                        >
                            {{ session('success') }}
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('comment.store', $article->slug) }}"
                        class="space-y-4"
                    >
                        @csrf

                        <!-- Name -->
                        <div>
                            <label
                                for="comment-name"
                                class="sr-only"
                            >
                                {{ __('blog.sections.blog.detail.response.name_placeholder') }}
                            </label>

                            <input
                                id="comment-name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="{{ __('blog.sections.blog.detail.response.name_placeholder') }}"
                                autocomplete="name"
                                class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror"
                                @error('name')
                                    aria-invalid="true"
                                    aria-describedby="comment-name-error"
                                @enderror
                            >

                            @error('name')
                                <p
                                    id="comment-name-error"
                                    class="text-sm text-red-500 mt-1"
                                    role="alert"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label
                                for="comment-email"
                                class="sr-only"
                            >
                                {{ __('blog.sections.blog.detail.response.email_placeholder') }}
                            </label>

                            <input
                                id="comment-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="{{ __('blog.sections.blog.detail.response.email_placeholder') }}"
                                autocomplete="email"
                                class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror"
                                @error('email')
                                    aria-invalid="true"
                                    aria-describedby="comment-email-error"
                                @enderror
                            >

                            @error('email')
                                <p
                                    id="comment-email-error"
                                    class="text-sm text-red-500 mt-1"
                                    role="alert"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div>
                            <label
                                for="comment-content"
                                class="sr-only"
                            >
                                {{ __('blog.sections.blog.detail.response.content_placeholder') }}
                            </label>

                            <textarea
                                id="comment-content"
                                name="content"
                                placeholder="{{ __('blog.sections.blog.detail.response.content_placeholder') }}"
                                rows="4"
                                class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('content') border-red-500 @enderror"
                                @error('content')
                                    aria-invalid="true"
                                    aria-describedby="comment-content-error"
                                @enderror
                            >{{ old('content') }}</textarea>

                            @error('content')
                                <p
                                    id="comment-content-error"
                                    class="text-sm text-red-500 mt-1"
                                    role="alert"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-semibold transition"
                        >
                            {{ __('blog.sections.blog.detail.response.submit') }} →
                        </button>
                    </form>
                </section>

                <!-- Comments -->
                <section class="pt-10">
                    <h2 class="text-lg font-semibold mb-6">
                        {{ __('blog.sections.blog.detail.comments.heading', [
                            'count' => $article->comments->count(),
                        ]) }}
                    </h2>

                    @forelse ($article->comments as $comment)
                        <article class="bg-gray-50 p-5 rounded-md shadow-sm mb-4">
                            <div class="flex items-center mb-2">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center font-bold text-white mr-3"
                                    aria-hidden="true"
                                >
                                    {{ mb_strtoupper(mb_substr($comment->name, 0, 1)) }}
                                </div>

                                <div>
                                    <p class="font-semibold">
                                        {{ $comment->name }}
                                    </p>

                                    <time
                                        datetime="{{ $comment->created_at->toIso8601String() }}"
                                        class="text-sm text-gray-500"
                                    >
                                        {{ $comment->created_at
                                            ->locale($currentLocale)
                                            ->translatedFormat(__('blog.sections.blog.detail.date.format')) }}
                                    </time>
                                </div>
                            </div>

                            <p class="text-gray-700 text-sm mt-2 whitespace-pre-line">
                                {{ $comment->content }}
                            </p>
                        </article>
                    @empty
                        <p class="text-gray-500 text-sm">
                            {{ __('blog.sections.blog.detail.comments.empty') }}
                        </p>
                    @endforelse
                </section>
            </div>

            <!-- Sidebar -->
            <aside class="lg:col-span-4 space-y-10">
                <!-- Recent Posts -->
                <section class="border border-gray-200 p-5 rounded-md">
                    <h2 class="text-lg font-semibold mb-4">
                        {{ __('blog.sections.blog.detail.sidebar.recent_posts') }}
                    </h2>

                    <ul class="space-y-4 text-sm text-gray-800">
                        @forelse ($recentPosts as $post)
                            <li class="border-b border-gray-200 pb-3 last:border-b-0">
                                <a
                                    href="{{ route('detail_blog', $post->slug) }}"
                                    class="font-medium hover:text-blue-600 hover:underline"
                                >
                                    {{ $post->title }}
                                </a>

                                <time
                                    datetime="{{ ($post->publish_date ?? $post->created_at)->toIso8601String() }}"
                                    class="block text-xs text-gray-500 mt-1"
                                >
                                    {{ ($post->publish_date ?? $post->created_at)
                                        ->locale($currentLocale)
                                        ->translatedFormat(__('blog.sections.blog.detail.date.format')) }}
                                </time>
                            </li>
                        @empty
                            <li class="text-gray-500">
                                {{ __('articles.empty.general') }}
                            </li>
                        @endforelse
                    </ul>
                </section>

                <!-- Categories -->
                <section class="border border-gray-200 p-5 rounded-md">
                    <h2 class="text-lg font-semibold mb-4">
                        {{ __('blog.sections.blog.detail.sidebar.categories') }}
                    </h2>

                    <ul class="text-sm text-gray-800 space-y-2">
                        @forelse ($categories as $category)
                            <li>
                                <a
                                    href="{{ route('blog', ['category' => $category->slug]) }}"
                                    class="hover:text-blue-600 hover:underline"
                                >
                                    <span class="font-semibold">
                                        {{ $category->name }}
                                    </span>

                                    <span aria-hidden="true">–</span>

                                    {{ $category->articles_count }}
                                    {{ __('blog.sections.blog.detail.sidebar.posts') }}
                                </a>
                            </li>
                        @empty
                            <li class="text-gray-500">
                                {{ __('blog.sections.blog.index.all_categories') }}
                            </li>
                        @endforelse
                    </ul>
                </section>
            </aside>
        </section>
    </main>
    <!-- End of Blog Detail -->

    <!-- Start of WhatsApp -->
    <section
        id="whatsapp-support"
        class="relative"
    >
        <button
            id="waBtn"
            type="button"
            aria-label="{{ __('blog.widgets.whatsapp.open_chat') }}"
            class="fixed bottom-6 right-6 bg-green-500 rounded-full p-3 shadow-lg hover:bg-green-600 transition duration-300 z-50"
        >
            <img
                src="https://img.icons8.com/ios-filled/30/ffffff/whatsapp.png"
                alt="{{ __('blog.widgets.whatsapp.icon_alt') }}"
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
                        alt="{{ __('blog.widgets.whatsapp.logo_alt') }}"
                        class="w-12 h-12 bg-black rounded-full shadow-lg"
                    >

                    <div>
                        <div class="font-bold text-sm">
                            {{ __('blog.widgets.whatsapp.support_name') }}
                        </div>

                        <div class="text-xs text-green-300">
                            {{ __('blog.widgets.whatsapp.online') }}
                        </div>
                    </div>
                </div>

                <button
                    id="closeBtn"
                    type="button"
                    aria-label="{{ __('blog.widgets.whatsapp.close_chat') }}"
                    class="text-white text-xl font-bold"
                >
                    &times;
                </button>
            </div>

            <div class="p-4 bg-white bg-opacity-80">
                <div class="mb-6">
                    <div class="text-sm font-semibold text-gray-700 mb-1">
                        {{ __('blog.widgets.whatsapp.support_name') }}
                    </div>

                    <div class="bg-white text-sm text-gray-800 p-3 rounded-lg shadow">
                        {{ __('blog.widgets.whatsapp.greeting') }}
                        <br>
                        {{ __('blog.widgets.whatsapp.question') }}
                    </div>
                </div>
            </div>

            <div class="bg-white px-4 pt-4 pb-6 text-center">
                <a
                    href="{{ $whatsappUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="block bg-green-500 text-white text-sm font-semibold w-full py-3 rounded-full shadow hover:bg-green-600 transition duration-300"
                >
                    {{ __('blog.widgets.whatsapp.chat_now') }}
                </a>

                <p class="text-xs text-gray-500 mt-2">
                    ⚡ {{ __('blog.widgets.whatsapp.powered_by') }}

                    <span class="text-green-600 font-semibold">
                        Solveit
                    </span>
                </p>
            </div>
        </div>
    </section>
    <!-- End of WhatsApp -->

    <!-- Start of Footer -->
    <footer class="py-10 mt-36 max-w-screen-xl mx-auto bg-white border-t border-gray-200 rounded-lg px-4">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 items-start">
            <div class="space-y-4">
                <div class="flex items-center space-x-2">
                    <img
                        src="{{ asset('front/image/logo.png') }}"
                        alt="{{ __('blog.footer.logo_alt') }}"
                        class="h-8 w-8"
                    >

                    <p class="text-xl font-semibold">
                        {{ __('blog.footer.brand_name') }}
                        <span class="text-blue-600">
                            {{ __('blog.footer.brand_highlight') }}
                        </span>
                    </p>
                </div>

                <p class="text-gray-600 text-sm leading-relaxed">
                    {{ __('blog.footer.description') }}
                </p>
            </div>

            <div class="sm:pl-0 md:pl-24">
                <h3 class="text-lg text-blue-600 font-semibold mb-2">
                    {{ __('blog.footer.address_title') }}
                </h3>

                <p class="text-gray-500">
                    {{ __('blog.footer.address') }}
                </p>
            </div>

            <div class="sm:pl-0 md:pl-32">
                <h3 class="text-lg text-blue-600 font-semibold mb-2">
                    {{ __('blog.footer.contact_title') }}
                </h3>

                <p class="text-gray-500">+62 812-5262-4284</p>
                <p class="text-gray-500">ritarussoconsultant@gmail.com</p>
            </div>

            <div class="ml-0 md:ml-auto">
                <h3 class="text-lg text-blue-600 font-semibold mb-2">
                    {{ __('blog.footer.office_hours_title') }}
                </h3>

                <p class="text-gray-500">
                    {{ __('blog.footer.monday_friday') }}
                </p>

                <p class="text-gray-500">
                    08.00 - 16.00
                </p>

                <p class="text-gray-500">
                    {{ __('blog.footer.saturday') }}
                </p>

                <p class="text-gray-500">
                    08.00 - 12.00
                </p>
            </div>
        </div>

        <div class="border-t border-gray-200 my-6"></div>

        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 space-y-4 md:space-y-0">
            <p>
                <span class="text-blue-600">
                    © Rita Russo Consultant {{ now()->year }}
                </span>,
                {{ __('blog.footer.copyright') }}
            </p>

            <div class="flex space-x-4">
                <a
                    href="https://www.instagram.com/rrcons_/"
                    aria-label="{{ __('blog.footer.instagram') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <svg
                        class="w-6 h-6 text-gray-600 hover:text-pink-500"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5A4.25 4.25 0 0 0 20.5 16.25v-8.5A4.25 4.25 0 0 0 16.25 3.5h-8.5Zm9 2.25a.75.75 0 0 1 1.5 0v1a.75.75 0 0 1-1.5 0v-1ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"/>
                    </svg>
                </a>

                <a
                    href="#"
                    aria-label="{{ __('blog.footer.tiktok') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <svg
                        class="w-6 h-6 text-gray-600 hover:text-black"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path d="M12.75 2h2.5a5.25 5.25 0 0 0 5.25 5.25v2.5a7.75 7.75 0 0 1-5.25-1.85v7.1a6.25 6.25 0 1 1-6.25-6.25h.75v2.5h-.75a3.75 3.75 0 1 0 3.75 3.75V2Z"/>
                    </svg>
                </a>

                <a
                    href="#"
                    aria-label="{{ __('blog.footer.x') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <svg
                        class="w-6 h-6 text-gray-600 hover:text-black"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path d="M14.5 10.5 21 3h-2.25l-5.25 6.25L9 3H3l6.93 10.02L3 21h2.25l5.7-6.77L15 21h6l-6.5-10.5Zm-2.07 2.45-.66-.98L5.1 4.5H8.1l4.17 6.15.66.98 6.93 10.02H16.9l-4.47-6.65Z"/>
                    </svg>
                </a>

                <a
                    href="#"
                    aria-label="{{ __('blog.footer.facebook') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <svg
                        class="w-6 h-6 text-gray-600 hover:text-blue-600"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path d="M14.5 3H10A4 4 0 0 0 6 7v3H3v4h3v7h4v-7h3.5l1-4H10V7a1 1 0 0 1 1-1h3.5V3Z"/>
                    </svg>
                </a>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

    <script>
        feather.replace();
    </script>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

    <script>
        AOS.init({
            once: true,
            duration: 800,
            easing: 'ease-in-out',
        });
    </script>

    <!-- Navbar, Mobile Menu, Language Dropdown, and WhatsApp Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.getElementById('menu-toggle');
            const closeMenu = document.getElementById('close-menu');
            const mobileMenu = document.getElementById('mobile-menu');

            const langToggleDesktop = document.getElementById('langToggleDesktop');
            const langDropdownDesktop = document.getElementById('langDropdownDesktop');

            const langToggleMobile = document.getElementById('langToggleMobile');
            const langDropdownMobile = document.getElementById('langDropdownMobile');

            const whatsappButton = document.getElementById('waBtn');
            const whatsappChatBox = document.getElementById('chatBox');
            const whatsappCloseButton = document.getElementById('closeBtn');

            const openMobileMenu = () => {
                mobileMenu?.classList.remove('-translate-x-full');
            };

            const closeMobileMenu = () => {
                mobileMenu?.classList.add('-translate-x-full');
            };

            menuToggle?.addEventListener('click', openMobileMenu);
            closeMenu?.addEventListener('click', closeMobileMenu);

            langToggleDesktop?.addEventListener('click', (event) => {
                event.stopPropagation();

                const isHidden = langDropdownDesktop?.classList.toggle('hidden');

                langToggleDesktop.setAttribute(
                    'aria-expanded',
                    String(!isHidden),
                );
            });

            langToggleMobile?.addEventListener('click', (event) => {
                event.stopPropagation();

                const isHidden = langDropdownMobile?.classList.toggle('hidden');

                langToggleMobile.setAttribute(
                    'aria-expanded',
                    String(!isHidden),
                );
            });

            whatsappButton?.addEventListener('click', () => {
                whatsappChatBox?.classList.toggle('hidden');
            });

            whatsappCloseButton?.addEventListener('click', () => {
                whatsappChatBox?.classList.add('hidden');
            });

            document.addEventListener('click', (event) => {
                if (
                    langDropdownDesktop
                    && langToggleDesktop
                    && !langDropdownDesktop.contains(event.target)
                    && !langToggleDesktop.contains(event.target)
                ) {
                    langDropdownDesktop.classList.add('hidden');
                    langToggleDesktop.setAttribute('aria-expanded', 'false');
                }

                if (
                    langDropdownMobile
                    && langToggleMobile
                    && !langDropdownMobile.contains(event.target)
                    && !langToggleMobile.contains(event.target)
                ) {
                    langDropdownMobile.classList.add('hidden');
                    langToggleMobile.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') {
                    return;
                }

                closeMobileMenu();

                langDropdownDesktop?.classList.add('hidden');
                langDropdownMobile?.classList.add('hidden');
                whatsappChatBox?.classList.add('hidden');

                langToggleDesktop?.setAttribute('aria-expanded', 'false');
                langToggleMobile?.setAttribute('aria-expanded', 'false');
            });
        });
    </script>
</body>
</html>