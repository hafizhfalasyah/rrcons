<?php

namespace App\Http\Controllers\Back\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('back.category.index', [
            'categories' => Category::latest()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:3'
        ]);

        $data['slug'] = Category::uniqueSlug($data['name']);

        Category::create($data);

        return back()->with('success', 'Categories has been created');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|min:3'
        ]);

        $category = Category::findOrFail($id);

        $data['slug'] = Category::uniqueSlug($data['name'], $category->id);

        $category->update($data);

        return back()->with('success', 'Categories has been update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::withCount('articles')->findOrFail($id);

        // articles.category_id adalah foreign key tanpa cascade
        if ($category->articles_count > 0) {
            return back()->withErrors([
                'category' => "Category \"{$category->name}\" still has {$category->articles_count} article(s). Move or delete them first.",
            ]);
        }

        $category->delete();

        return back()->with('success', 'Categories has been deleted');
    }
}
