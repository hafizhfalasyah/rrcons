@forelse ($articles as $article)
    <div>
        <a href="{{ route('detail_blog', $article->slug) }}">
            <img src="{{ asset('storage/back/blog/' . $article->img) }}" alt="{{ $article->title }}" class="w-full h-64 object-cover rounded-lg shadow">
        </a>
        <p class="text-sm text-gray-500 mt-3 italic">
            {{ $article->category->name ?? 'Tanpa Kategori' }}
        </p>
        <h3 class="text-lg font-bold mt-1">
            <a href="{{ route('detail_blog', $article->slug) }}">{{ $article->title }}</a>
        </h3>
    </div>
@empty
    <p class="text-gray-500 col-span-3">Tidak ada artikel yang ditemukan untuk kategori ini.</p>
@endforelse
