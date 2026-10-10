<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use App\Models\Home\HomeMenu;
use App\Models\Home\PosterHome;
use App\Models\Home\HeroHome;
use App\Models\Home\AboutHome;
use App\Models\Home\ServiceHome;
use App\Models\Home\AdvantageHome;
use App\Models\Home\ProfileHome;
use App\Models\Home\BusinessHome;
use App\Models\Home\SosmedHome;
use App\Models\Home\GalleryHome;

use App\Models\RRfuture\HeroRRfuture;
use App\Models\RRfuture\ProductRRfuture;

use App\Models\Service\HeroService;
use App\Models\Service\OurService;

use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Blog\Config;
use App\Models\Blog\Comment;

use App\Models\Contact\HeroContact;
use App\Models\Contact\UsContact;

use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $posters = PosterHome::whereNotNull('img')
            ->where('img', '<>', '')
            ->get()
            ->filter(function ($poster) {
                return Storage::disk('public')->exists($poster->img);
        });

        $heroes = HeroHome::with(['homeMenu:id,title_'.$locale.' as title,sub_title_'.$locale.' as sub_title'])->get();
        $abouts = AboutHome::with(['homeMenu:id,title_'.$locale.' as title,sub_title_'.$locale.' as sub_title'])->get();
        $services = ServiceHome::with(['homeMenu:id,title_'.$locale.' as title,sub_title_'.$locale.' as sub_title'])->get();
        $advantages = AdvantageHome::with(['homeMenu:id,title_'.$locale.' as title,sub_title_'.$locale.' as sub_title'])->get();
        $profiles = ProfileHome::with(['homeMenu:id,title_'.$locale.' as title,sub_title_'.$locale.' as sub_title'])->get();
        $business = BusinessHome::with(['homeMenu:id,title_'.$locale.' as title,sub_title_'.$locale.' as sub_title'])->get();
        $sosmed = SosmedHome::with(['homeMenu:id,title_'.$locale.' as title,sub_title_'.$locale.' as sub_title'])->get();
        $gallery = GalleryHome::with(['homeMenu:id,title_'.$locale.' as title,sub_title_'.$locale.' as sub_title'])->get();

        $months = DB::table('gallery_homes')->distinct()->orderBy('month')->pluck('month');
        $years  = DB::table('gallery_homes')->distinct()->orderByDesc('year')->pluck('year');

        // Blog & Filtering by Category
        $categories = Category::all();
        $categorySlug = $request->query('category');

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();

            if ($category) {
                $articles = Article::published()
                    ->where('category_id', $category->id)
                    ->with('category')
                    ->latest('publish_date')
                    ->take(6)
                    ->get();
            } else {
                $articles = collect(); // kategori tidak ditemukan
            }
        } else {
            $articles = Article::published()->with('category')->latest('publish_date')->take(6)->get();
        }

        return view('front.index', compact(
            'posters', 'heroes', 'abouts', 'services', 'advantages',
            'profiles', 'business', 'sosmed', 'gallery', 'months', 'years',
            'articles', 'categories', 'categorySlug'
        ));
    }

    public function getArticles(Request $request) {
        $categorySlug = $request->query('category');
        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            $articles = $category
                ? Article::published()->where('category_id', $category->id)->with('category')->latest('publish_date')->take(6)->get()
                : collect();
        } else {
            $articles = Article::published()->with('category')->latest('publish_date')->take(6)->get();
        }

        return response()->json([
            'html' => view('front.partials.articles', compact('articles'))->render()
        ]);
    }

    public function searchArticles(Request $request)
    {
        $query = $request->query('q');
        $categorySlug = $request->query('category');

        $articles = Article::published()
            ->with('category')
            ->when($query, function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->when($categorySlug, function($q) use ($categorySlug) {
                $q->whereHas('category', fn($c) => $c->where('slug', $categorySlug));
            })
            ->latest('publish_date')
            ->get();

        $html = view('front.partials.article_list', compact('articles'))->render();

        return response()->json(compact('html'));
    }

    public function show($slug)
    {
        // Artikel Private / terjadwal (belum waktunya) tidak boleh bisa diakses publik
        $article = Article::published()
            ->with(['category', 'comments', 'Creator'])
            ->where('slug', $slug)
            ->firstOrFail();

        $article->increment('views');

        $recentPosts = Article::published()
            ->latest('publish_date')
            ->where('id', '!=', $article->id)
            ->take(4)
            ->get();

        $categories = Category::withCount(['articles' => fn($q) => $q->published()])->get();
    
        return view('front.detail-blog', compact(
            'article',
            'recentPosts',
            'categories'
        ));
    }

    public function storeComment(Request $request, $slug)
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'content' => 'required|string|max:1000',
        ]);

        $article->comments()->create($validated);

        return back()->with('success', 'Komentar berhasil dikirim!');
    }

    public function rrfuture() {
        $heroes = HeroRRfuture::with('rrfutureMenu')->get();
        $products = ProductRRfuture::with('rrfutureMenu')->get();

        return view('front.rrfuture', [
            'heroes' => $heroes,
            'products' => $products,
        ]);
    }

    public function service() {
        $heroes = HeroService::with('serviceMenu')->get();
        $ours = OurService::with('serviceMenu')->get();

        return view('front.service', [
            'heroes' => $heroes,
            'ours' => $ours,
        ]);
    }

    public function blog(Request $request)
    {
        $categories = Category::all();
        $categorySlug = $request->query('category');

        $articles = Article::published()
            ->with('category')
            ->when($categorySlug, function ($query) use ($categorySlug) {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->latest('publish_date')
            ->paginate(9); // bisa diganti get() jika tidak ingin pagination

        return view('front.blog', compact('categories', 'articles'));
    }

    public function detail_blog() {
        return view('front.detail-blog');
    }

    public function contact() {
        $heroes = HeroContact::with('contactMenu')->get();
        $usCollection = UsContact::with('contactMenu')->get();

        $usData = $usCollection->pluck('desc', 'title')->toArray();

        return view('front.contact', [
            'heroes' => $heroes,
            'usCollection' => $usCollection,
            'us' => $usData,
        ]);
    }

}