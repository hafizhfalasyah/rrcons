<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog\Article;

class CategoryController extends Controller
{
    public function index($slugCategory) {
        return view('front.category.index', [
            'articles' => Article::published()->with('Category')->whereHas('Category', function($q) use ($slugCategory){
                $q->where('slug', $slugCategory);
            })->latest('publish_date')->paginate(9),
            'category' => $slugCategory
        ]);
    }
}
