<?php

namespace App\Http\Controllers\Back\Blog;

use App\Http\Controllers\Concerns\ManagesPublicFiles;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Support\HtmlSanitizer;
use Yajra\DataTables\Facades\DataTables;

class ArticleController extends Controller
{
    use ManagesPublicFiles;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $article = Article::with(['Category', 'Creator'])->latest()->get();

            return DataTables::of($article)
                ->addIndexColumn()
                ->addColumn('title', function ($article) {
                    $html = e($article->title_id);

                    // tandai artikel yang belum punya terjemahan English
                    if (blank($article->title_en)) {
                        $html .= ' <span class="badge bg-light text-muted border ms-1" title="' . e(__('English version is empty, the Indonesian version will be shown.')) . '">' . e(__('ID only')) . '</span>';
                    }

                    return $html;
                })
                ->addColumn('category_id', fn ($article) => e(optional($article->Category)->name))
                ->addColumn('created_by', fn ($article) => e(optional($article->Creator)->name))
                ->editColumn('publish_date', fn ($article) => view('back.article.partials.publish_date', compact('article'))->render())
                ->addColumn('status', fn ($article) => view('back.article.partials.status_badge', compact('article'))->render())
                ->addColumn('button', function ($article) {
                    $showUrl = route('blog.articles.show', $article->id);
                    $editUrl = route('blog.articles.edit', $article->id);

                    return '
                        <div class="d-flex justify-content-center">
                            <a href="' . $showUrl . '" class="btn btn-secondary btn-sm me-1 d-flex align-items-center px-3">
                                <i data-feather="link" class="icon-sm me-1"></i> ' . e(__('Detail')) . '
                            </a>
                            <a href="' . $editUrl . '" class="btn btn-primary btn-sm me-1 d-flex align-items-center px-3">
                                <i data-feather="edit" class="icon-sm me-1"></i> ' . e(__('Edit')) . '
                            </a>
                            <a href="#" onclick="deleteArticle(this)" data-id="' . $article->id . '" data-title="' . e($article->title_id) . '" class="btn btn-danger btn-sm d-flex align-items-center px-3">
                                <i data-feather="trash-2" class="icon-sm me-1"></i> ' . e(__('Delete')) . '
                            </a>
                        </div>';
                })
                ->rawColumns(['title', 'category_id', 'created_by', 'publish_date', 'status', 'button'])
                ->make();
        }

        return view('back.article.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('back.article.create', [
            'categories' => Category::get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleRequest $request)
    {
        $data = $this->articleData($request->validated());

        $data['img'] = $this->storePublicFile($request->file('img'), 'back/blog');
        $data['user_id'] = $request->user()->id;
        $data['created_by'] = $request->user()->id;
        $data['slug'] = Article::uniqueSlug($data['title_id']);

        $article = Article::create($data);

        return redirect()->route('blog.articles.index')->with('success', $this->savedMessage($article, __('Article has been created.')));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('back.article.show', [
            'article' => Article::with(['User', 'Creator', 'Category', 'slugRedirects'])->findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('back.article.update', [
            'article' => Article::findOrFail($id),
            'categories' => Category::get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, string $id)
    {
        $article = Article::findOrFail($id);

        $data = $this->articleData($request->validated(), $article);

        if ($request->hasFile('img')) {
            $data['img'] = $this->replacePublicFile($article->img, $request->file('img'), 'back/blog');
        }

        // user_id = editor terakhir, created_by = penulis asli (tidak berubah)
        $data['user_id'] = $request->user()->id;
        $data['slug'] = Article::uniqueSlug($data['title_id'], $article->id);
        $previousSlug = $article->slug;

        $article->update($data);

        $message = $this->savedMessage($article, __('Article has been updated.'));

        // slug lama otomatis dialihkan (lihat Article::booted)
        if ($article->slug !== $previousSlug) {
            $message .= ' ' . __('The article URL changed to /blog/:slug; the old URL /blog/:old redirects there automatically.', [
                'slug' => $article->slug,
                'old' => $previousSlug,
            ]);
        }

        return redirect()->route('blog.articles.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);

        $this->deletePublicFile($article->img, 'back/blog');

        $article->delete();

        return response()->json([
            'message' => __('Article has been deleted.')
        ]);
    }

    /**
     * Data siap simpan: konten disanitasi dan pilihan publikasi dipetakan ke status & publish_date.
     */
    private function articleData(array $validated, ?Article $current = null): array
    {
        $data = [
            'category_id' => $validated['category_id'],
            'title_id' => $validated['title_id'],
            'title_en' => $validated['title_en'] ?? null,
            'desc_id' => HtmlSanitizer::clean($validated['desc_id']),
            'desc_en' => filled($validated['desc_en'] ?? null) ? HtmlSanitizer::clean($validated['desc_en']) : null,
            'meta_title_id' => $validated['meta_title_id'] ?? null,
            'meta_title_en' => $validated['meta_title_en'] ?? null,
            'meta_description_id' => $validated['meta_description_id'] ?? null,
            'meta_description_en' => $validated['meta_description_en'] ?? null,
        ];

        return $data + Article::publicationAttributes($validated['publication'], $validated['publish_date'] ?? null, $current);
    }

    /**
     * Pesan sukses yang menegaskan status artikel setelah disimpan.
     */
    private function savedMessage(Article $article, string $prefix): string
    {
        $article->refresh();

        return $prefix . ' ' . match ($article->publication_status) {
            Article::PUBLICATION_PRIVATE => __('Status: Private — not visible on the website.'),
            Article::PUBLICATION_SCHEDULED => __('Status: Scheduled — will be published automatically on :date.', [
                'date' => $article->publish_date->translatedFormat('d F Y H:i'),
            ]),
            default => __('Status: Published — visible on the website now.'),
        };
    }
}
