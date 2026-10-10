<?php

namespace App\Http\Controllers\Back\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Blog\Article;
use App\Models\Blog\Category;

use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $article = Article::with(['Category', 'Creator'])->latest()->get();

            return DataTables::of($article)
                // custom column
                ->addIndexColumn() // untuk id
                ->addColumn('category_id', function($article){
                    return $article->Category->name;
                })

                ->addColumn('created_by', function($article){
                    return optional($article->creator)->name;
                })

                ->editColumn('publish_date', function($row) {
                    return optional($row->publish_date)->translatedFormat('d F Y H:i') ?? '-';
                })

                ->addColumn('status', function($article){
                    switch ($article->publication_status) {
                        case Article::PUBLICATION_PRIVATE:
                            return '<span class="badge bg-danger px-3 py-2">Private</span>';
                        case Article::PUBLICATION_SCHEDULED:
                            return '<span class="badge bg-warning text-dark px-3 py-2">Scheduled</span>';
                        default:
                            return '<span class="badge bg-primary px-3 py-2">Published</span>';
                    }
                })

                ->addColumn('button', function ($article) {
                    $showUrl = route('blog.articles.show', $article->id);
                    $editUrl = route('blog.articles.edit', $article->id);

                    return '
                        <div class="d-flex justify-content-center">
                            <a href="' . $showUrl . '" class="btn btn-secondary btn-sm me-1 d-flex align-items-center px-3">
                                <i data-feather="link" class="icon-sm me-1"></i> Detail
                            </a>
                            <a href="' . $editUrl . '" class="btn btn-primary btn-sm me-1 d-flex align-items-center px-3">
                                <i data-feather="edit" class="icon-sm me-1"></i> Edit
                            </a>
                            <a href="#" onclick="deleteArticle(this)" data-id="' . $article->id . '" class="btn btn-danger btn-sm d-flex align-items-center px-3">
                                <i data-feather="trash-2" class="icon-sm me-1"></i> Delete
                            </a>
                        </div>';
                })

                // panggil custom column
                ->rawColumns(['category_id', 'status', 'button'])
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
        $data = $request->validated();

        // Pastikan user login
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'User not authenticated');
        }

        // Simpan gambar ke storage/app/public/back/blog
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('back/blog', $fileName, 'public');

            $data['img'] = $path;
        }

        $data['user_id'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['slug'] = Article::uniqueSlug($data['title']);
        // Pakai tanggal terbit pilihan user (bisa di masa depan = terjadwal)
        $data['publish_date'] = $data['publish_date'] ?? now();

        Article::create($data);

        return redirect()->route('blog.articles.index')->with('success', 'Data article has been created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('back.article.show', [
            'article' => Article::with(['User', 'Creator', 'Category'])->findOrFail($id)
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
        $data = $request->validated();

        $article = Article::findOrFail($id);

        if ($request->hasFile('img')) {

            // Hapus gambar lama
            if ($article->img) {
                Storage::disk('public')->delete($article->img);
            }

            $file = $request->file('img');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('back/blog', $fileName, 'public');

            $data['img'] = $path; // simpan path lengkap
        }

        $data['user_id'] = auth()->id();
        $data['slug'] = Article::uniqueSlug($data['title'], $article->id);
        // Pertahankan tanggal terbit lama bila field dikosongkan
        $data['publish_date'] = $data['publish_date'] ?? $article->publish_date ?? now();

        $article->update($data);

        return redirect()->route('blog.articles.index')->with('success', 'Data article has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);
    
        if ($article->img) {
            Storage::disk('public')->delete($article->img);
        }
    
        $article->delete();
    
        return response()->json([
            'message' => 'Data article has been deleted'
        ]);
    }

}
