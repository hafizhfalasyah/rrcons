<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $allowedPaths = ['/', '/rrfuture', '/service', '/blog', '/blog_detail', '/contact'];

        $labelMap = [
            '/' => 'Home',
            '/rrfuture' => 'RR Future',
            '/service' => 'Service',
            '/blog' => 'Blog',
            '/blog_detail' => 'Blog Detail',
            '/contact' => 'Contact',
        ];

        $labels = collect(range(6, 0))->map(fn($daysAgo) => [
            'query' => Carbon::now()->subDays($daysAgo)->toDateString(),
            'label' => Carbon::now()->subDays($daysAgo)->translatedFormat('d F Y'),
        ]);

        $labelList = $labels->pluck('label');

        $visitorStats = [];

        foreach ($allowedPaths as $path) {
            $data = $labels->map(function ($dateObj) use ($path) {
                return Visitor::query()
                    // halaman detail tercatat sebagai /blog/{slug}
                    ->when($path === '/blog_detail',
                        fn ($q) => $q->where('path', 'like', '/blog/%'),
                        fn ($q) => $q->where('path', $path)
                    )
                    ->whereDate('visit_date', $dateObj['query'])
                    ->count();
            });

            $visitorStats[] = [
                'label' => __($labelMap[$path]),
                'data' => $data,
            ];
        }

        return view('back.dashboard.index', [
            'total_articles' => Article::count(),
            'total_categories' => Category::count(),
            'total_users' => User::count(),
            'latest_article' => Article::with('Category')->published()->latest('publish_date')->take(10)->get(),
            'popular_article' => Article::with('Category')->published()->orderBy('views', 'desc')->take(5)->get(),
            'labels' => $labelList,
            'visitorStats' => $visitorStats,
        ]);
    }

}
