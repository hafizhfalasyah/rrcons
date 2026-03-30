<?php

namespace App\Http\Controllers\Back\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Blog\Article;
use App\Models\Blog\Category;

use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

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
                    return \Carbon\Carbon::parse($row->publish_date)->translatedFormat('d F Y');
                })

                ->addColumn('status', function($article){
                    if ($article->status == 0){
                        return '<span class="badge bg-danger px-3 py-2">Private</span>';
                    } else {
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
            $file->storeAs('public/back/blog/', $fileName); // Simpan ke direktori storage
            $data['img'] = $fileName;
        }

        $data['user_id'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['slug'] = Str::slug($data['title']);
        $data['publish_date'] = now();

        Article::create($data);

        return redirect()->route('blog.articles.index')->with('success', 'Data article has been created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('back.article.show', [
            'article' => Article::with(['User', 'Category'])->find($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('back.article.update', [
            'article' => Article::find($id),
            'categories' => Category::get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, string $id)
    {
        $data = $request->validated();

        $article = Article::findOrFail($id); // Pastikan artikel ada

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/blog/', $fileName);

            // Hapus gambar lama jika ada
            if ($article->img && Storage::exists('public/back/blog/' . $article->img)) {
                Storage::delete('public/back/blog/' . $article->img);
            }

            $data['img'] = $fileName;
        } else {
            // Jika gambar tidak diupload ulang, gunakan gambar lama
            $data['img'] = $article->img;
        }

        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']);
        $data['publish_date'] = now();

        $article->update($data);

        return redirect()->route('blog.articles.index')->with('success', 'Data article has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Article::find($id);
        Storage::delete('public/back/blog/'.$data->img); // hapus di direktori baru
        $data->delete();

        return response()->json([
            'message' => 'Data article has been deleted'
        ]);
    }
}
