<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog\Article;

class ArticleController extends Controller
{
    public function index() {
        $keyword = request()->keyword;

        if ($keyword) {
            $articles = Article::with('Category')
                        ->published()
                        ->searchTitle($keyword)
                        ->latest('publish_date')
                        ->paginate(6);
        } else {
            $articles = Article::with('Category')->published()->latest('publish_date')->paginate(6);
        }

        return view('front.article.index', [
            'articles' => $articles,
            'keyword' => $keyword
        ]);
    }
    
    public function show($slug)
    {
        $article = Article::published()->whereSlug($slug)->firstOrFail();
        $article->increment('views');
    
        return view('front.article.show', [
            'article' => $article,
        ]);
    }
}
