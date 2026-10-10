<?php

namespace App\Http\Controllers\Back\Blog;

use App\Http\Controllers\Concerns\ValidatesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Blog\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ValidatesTranslations;

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
        $data = $this->validateWithTranslations($request, [
            'name' => 'required|string|min:3|max:255',
        ]);

        $data['slug'] = Category::uniqueSlug($data['name_id']);

        Category::create($data);

        return back()->with('success', __('Category has been created.'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validateWithTranslations($request, [
            'name' => 'required|string|min:3|max:255',
        ]);

        $category = Category::findOrFail($id);

        $data['slug'] = Category::uniqueSlug($data['name_id'], $category->id);

        $category->update($data);

        return back()->with('success', __('Category has been updated.'));
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
                'category' => __('Category ":name" still has :count article(s). Move or delete them first.', ['name' => $category->name, 'count' => $category->articles_count]),
            ]);
        }

        $category->delete();

        return back()->with('success', __('Category has been deleted.'));
    }
}
