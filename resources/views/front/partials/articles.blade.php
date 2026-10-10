@forelse ($articles as $article)
    <div>
        <a href="{{ route('detail_blog', $article->slug) }}">
            <img
                src="{{ asset('storage/' . $article->img) }}"
                alt="{{ $article->title }}"
                class="w-full h-64 object-cover rounded-lg shadow"
            >
        </a>

        <p class="text-sm text-gray-500 mt-3 italic">
            {{
                $article->category->name
                    ?? __('article.sections.article.category.uncategorized')
            }}
        </p>

        <h3 class="text-lg font-bold mt-1">
            <a href="{{ route('detail_blog', $article->slug) }}">
                {{ $article->title }}
            </a>
        </h3>
    </div>
@empty
    <p class="text-gray-500 col-span-3">
        {{ __('article.sections.article.empty.category') }}
    </p>
@endforelse