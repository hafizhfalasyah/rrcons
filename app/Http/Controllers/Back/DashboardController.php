<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Blog\Comment;
use App\Models\Home\GalleryHome;
use App\Models\Home\PosterHome;
use App\Models\User;
use App\Support\Analytics\AnalyticsReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $report = new AnalyticsReport((string) $request->query('range', '7d'));

        return view('back.dashboard.index', [
            'total_articles' => Article::count(),
            'published_articles' => Article::published()->count(),
            'pending_comments' => Comment::pending()->count(),
            'total_posters' => PosterHome::whereNotNull('img')->where('img', '<>', '')->count(),
            'total_categories' => Category::count(),
            'total_users' => User::count(),
            'total_galleries' => GalleryHome::count(),
            'latest_article' => Article::with('Category')->published()->latest('publish_date')->take(10)->get(),
            'popular_article' => Article::with('Category')->published()->orderBy('views', 'desc')->take(5)->get(),
            'report' => $report,
            'summary' => $report->summary(),
            'daily' => $report->daily(),
            'topPages' => $report->topPages(),
            'topArticles' => $report->topArticles(),
            'sources' => $report->sources(),
            'referrers' => $report->referrers(),
            'devices' => $report->breakdown('device'),
            'browsers' => $report->breakdown('browser'),
            'systems' => $report->breakdown('os'),
            'languages' => $report->breakdown('locale'),
            'countries' => $report->breakdown('country'),
            'legacyShare' => $report->legacyShare(),
            'realtime' => AnalyticsReport::realtime(),
        ]);
    }

    /**
     * Data real-time untuk panel "sedang online" (di-poll oleh dashboard).
     */
    public function realtime()
    {
        return response()->json(AnalyticsReport::realtime())->header('Cache-Control', 'no-store');
    }
}
