@forelse ($articles as $article)
    <div
        class="relative"
        data-aos="fade-up"
        data-aos-delay="200"
    >
        <a
            href="{{ route('detail_blog', $article->slug) }}"
            class="block group"
        >
            <img
                loading="lazy"
                decoding="async"
                src="{{ asset('storage/' . $article->img) }}"
                alt="{{ $article->title }}"
                class="w-full h-48 object-cover rounded transition duration-300 group-hover:scale-[1.02]"
            >

            <div class="mt-3">
                <p class="text-sm text-gray-500">
                    {{
                        $article->category->name
                            ?? __('article.sections.article.category.uncategorized')
                    }}
                </p>

                <h3 class="font-semibold transition duration-300 group-hover:text-blue-600">
                    {{ $article->title }}
                </h3>

                <p class="text-sm text-gray-600">
                    {{ Str::limit(strip_tags($article->desc), 100) }}
                </p>
            </div>
        </a>
    </div>
@empty
    <p class="text-gray-500 col-span-3">
        {{ __('article.sections.article.empty.general') }}
    </p>
@endforelse