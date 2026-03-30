<?php

use Illuminate\Support\Facades\Route;

/** IMPORT GENERAL BACK CONTROLLERS **/
use App\Http\Controllers\Back\DashboardController;
use App\Http\Controllers\Back\UserController;

/** IMPORT HOME BACK CONTROLLERS **/
use App\Http\Controllers\Back\Home\HomeMenuController;

/** IMPORT RRFUTURE BACK CONTROLLERS **/
use App\Http\Controllers\Back\RRfuture\RRfutureMenuController;

/** IMPORT SERVICE BACK CONTROLLERS **/
use App\Http\Controllers\Back\Service\ServiceMenuController;

/** IMPORT BLOG BACK CONTROLLERS **/
use App\Http\Controllers\Back\Blog\ArticleController as BackArticleController;
use App\Http\Controllers\Back\Blog\CategoryController as BackCategoryController;
use App\Http\Controllers\Back\Blog\ConfigController as BackConfigController;

/** IMPORT CONTACT BACK CONTROLLERS **/
use App\Http\Controllers\Back\Contact\ContactMenuController;


/** IMPORT GENERAL FRONT CONTROLLERS **/
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ArticleController as FrontArticleController;
use App\Http\Controllers\Front\CategoryController as FrontCategoryController;


use App\Http\Controllers\TwoFactorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/b', function () {
//     return view('front.detail-blog');
// });

// Route::get('/p/{slug}', [FrontArticleController::class, 'show']);
// Route::get('/articles', [FrontArticleController::class, 'index']);
// Route::post('/articles/search', [FrontArticleController::class, 'index'])->name('search');

// Route::get('category/{slug}', [FrontCategoryController::class, 'index']);

Route::get('/set-language/{lang}', function ($lang) {
    if (!in_array($lang, ['id', 'en'])) {
        $lang = 'id';
    }
    session(['app_locale' => $lang]);
    return back();
})->name('set.language');

Route::middleware('auth')->group(function() {
    /** DASHBOARD MANAGEMENT **/
    Route::get('/dashboard', [DashboardController::class, 'index']);

    /** USER MANAGEMENT **/
    Route::resource('/users', UserController::class);

    /** HOME MENU MANAGEMENT **/
    Route::prefix('home')->name('home.')->group(function () {
        Route::get('/menu', [HomeMenuController::class, 'homeMenu'])->name('menu');
        Route::post('/menu/{id}/update', [HomeMenuController::class, 'updateHomeMenu'])->name('menu.update');

        Route::get('/poster', [HomeMenuController::class, 'posterSection'])->name('poster');
        Route::post('/poster/create', [HomeMenuController::class, 'createPosterSection'])->name('poster.create');
        Route::post('/poster/{id}/update', [HomeMenuController::class, 'updatePosterSection'])->name('poster.update');
        Route::delete('/poster/{id}/delete', [HomeMenuController::class, 'deletePosterSection'])->name('poster.delete');

        Route::get('/hero', [HomeMenuController::class, 'heroSection'])->name('hero');
        Route::post('/hero/{id}/update', [HomeMenuController::class, 'updateHeroSection'])->name('hero.update');

        Route::get('/about', [HomeMenuController::class, 'aboutSection'])->name('about');
        Route::post('/about/{id}/update', [HomeMenuController::class, 'updateAboutSection'])->name('about.update');

        Route::get('/service', [HomeMenuController::class, 'serviceSection'])->name('service');
        Route::post('/service/{id}/update', [HomeMenuController::class, 'updateServiceSection'])->name('service.update');

        Route::get('/advantage', [HomeMenuController::class, 'advantageSection'])->name('advantage');
        Route::post('/advantage/{id}/update', [HomeMenuController::class, 'updateAdvantageSection'])->name('advantage.update');

        Route::get('/profile', [HomeMenuController::class, 'profileSection'])->name('profile');
        Route::post('/profile/{id}/update', [HomeMenuController::class, 'updateProfileSection'])->name('profile.update');

        Route::get('/business', [HomeMenuController::class, 'businessSection'])->name('business');
        Route::post('/business/{id}/update', [HomeMenuController::class, 'updateBusinessSection'])->name('business.update');

        Route::get('/sosmed', [HomeMenuController::class, 'sosmedSection'])->name('sosmed');
        Route::post('/sosmed/{id}/update', [HomeMenuController::class, 'updateSosmedSection'])->name('sosmed.update');

        Route::get('/gallery', [HomeMenuController::class, 'gallerySection'])->name('gallery');
        Route::post('/gallery/create', [HomeMenuController::class, 'createGallerySection'])->name('gallery.create');
        Route::post('/gallery/{id}/update', [HomeMenuController::class, 'updateGallerySection'])->name('gallery.update');
        Route::delete('/gallery/{id}/delete', [HomeMenuController::class, 'deleteGallerySection'])->name('gallery.delete');
    });

    /** RRFUTURE MENU MANAGEMENT **/
    Route::prefix('rrfuture')->name('rrfuture.')->group(function () {
        Route::get('/menu', [RRfutureMenuController::class, 'rrfutureMenu'])->name('menu');
        Route::post('/menu/{id}/update', [RRfutureMenuController::class, 'updateRRfutureMenu'])->name('menu.update');

        Route::get('/hero', [RRfutureMenuController::class, 'heroSection'])->name('hero');
        Route::post('/hero/{id}/update', [RRfutureMenuController::class, 'updateHeroSection'])->name('hero.update');

        Route::get('/product', [RRfutureMenuController::class, 'productSection'])->name('product');
        Route::post('/product/{id}/update', [RRfutureMenuController::class, 'updateProductSection'])->name('product.update');
    });

    /** SERVICE MENU MANAGEMENT **/
    Route::prefix('service')->name('service.')->group(function () {
        Route::get('/menu', [ServiceMenuController::class, 'serviceMenu'])->name('menu');
        Route::post('/menu/{id}/update', [ServiceMenuController::class, 'updateServiceMenu'])->name('menu.update');

        Route::get('/hero', [ServiceMenuController::class, 'heroSection'])->name('hero');
        Route::post('/hero/{id}/update', [ServiceMenuController::class, 'updateHeroSection'])->name('hero.update');

        Route::get('/ourService', [ServiceMenuController::class, 'ourSection'])->name('ourService');
        Route::post('/ourService/{id}/update', [ServiceMenuController::class, 'updateOurSection'])->name('ourService.update');
    });

    /** CONTACT MENU MANAGEMENT **/
    Route::prefix('contact')->name('contact.')->group(function () {
        Route::get('/menu', [ContactMenuController::class, 'contactMenu'])->name('menu');
        Route::post('/menu/{id}/update', [ContactMenuController::class, 'updateContactMenu'])->name('menu.update');

        Route::get('/hero', [ContactMenuController::class, 'heroSection'])->name('hero');
        Route::post('/hero/{id}/update', [ContactMenuController::class, 'updateHeroSection'])->name('hero.update');

        Route::get('/usContact', [ContactMenuController::class, 'usSection'])->name('usContact');
        Route::post('/usContact/{id}/update', [ContactMenuController::class, 'updateUsSection'])->name('usContact.update');
    });

    /** BLOG MANAGEMENT **/
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/articles', [BackArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/create', [BackArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [BackArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/{article}', [BackArticleController::class, 'show'])->name('articles.show');
        Route::get('/articles/{article}/edit', [BackArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/articles/{article}', [BackArticleController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{article}', [BackArticleController::class, 'destroy'])->name('articles.destroy');

        Route::get('/categories', [BackCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [BackCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [BackCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [BackCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/configs', [BackConfigController::class, 'index'])->name('configs.index');
        Route::put('/configs/{config}', [BackConfigController::class, 'update'])->name('configs.update');
    });

    /** MANAGEMENT **/
    Route::group(['prefix' => 'laravel-filemanager'], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
});

Route::middleware('countPageVisitor')->controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/rrfuture', 'rrfuture')->name('rrfuture');
    Route::get('/service', 'service')->name('service');
    Route::get('/blog', 'blog')->name('blog');
    Route::get('/blog/{slug}', 'show')->name('detail_blog');
    Route::post('/blog/{slug}/comment', 'storeComment')->name('comment.store');
    Route::get('/get-articles', 'getArticles')->name('get.articles');
    Route::get('/search-articles',  'searchArticles')->name('search.articles');
    Route::get('/contact', 'contact')->name('contact');
});

Auth::routes();
