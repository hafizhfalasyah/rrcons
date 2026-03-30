@forelse ($articles as $article)
    <div class="relative" data-aos="fade-up" data-aos-delay="200">
        <a href="{{ route('detail_blog', $article->slug) }}">
            <img src="{{ asset('storage/back/blog/' . $article->img) }}" class="w-full h-48 object-cover rounded" />
        </a>
        <div class="mt-3">
            <p class="text-sm text-gray-500">{{ $article->category->name ?? 'Tanpa Kategori' }}</p>
            <h3 class="font-semibold">{{ $article->title }}</h3>
            <p class="text-sm text-gray-600">{{ Str::limit(strip_tags($article->desc), 100) }}</p>
        </div>
    </div>
@empty
    <p class="text-gray-500 col-span-3">Artikel tidak ditemukan.</p>
@endforelse
