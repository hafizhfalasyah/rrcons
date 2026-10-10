<?php

namespace App\Http\Controllers\Back\Home;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesPublicFiles;
use App\Http\Controllers\Concerns\ValidatesTranslations;
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

class HomeMenuController extends Controller
{
    use ManagesPublicFiles, ValidatesTranslations;

    /** ============================== HOME MENU CONTROLLERS ============================== **/

    public function homeMenu()
    {
        $lang = App::getLocale();
        $homeMenus = HomeMenu::all();
        $lastUpdated = HomeMenu::latest('updated_at')->value('updated_at');

        return view('back.home_menu.index', compact('homeMenus', 'lastUpdated', 'lang'));
    }

    public function updateHomeMenu(Request $request, $id)
    {
        $validatedData = $this->validateWithTranslations($request, [
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
        ]);

        HomeMenu::findOrFail($id)->update($validatedData);

        return redirect()->route('home.menu')->with('success', __('Home Menu updated successfully.'));
    }

    /** ============================== POSTER SECTION CONTROLLERS ============================== **/

    public function posterSection()
    {
        $posters = PosterHome::all();
        $lastUpdated = PosterHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.poster_section.index', [
            'posters' => $posters,
            'lastUpdated' => $lastUpdated,
        ]);
    }

    public function createPosterSection(Request $request)
    {
        $validatedData = $this->validateWithTranslations($request, [
            'name' => 'required|string|max:255',
        ], [
            'img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $poster = new PosterHome([
            'name_id' => $validatedData['name_id'],
            'name_en' => $validatedData['name_en'],
        ]);

        if ($request->hasFile('img')) {
            $poster->img = $this->storePublicFile($validatedData['img'], 'back/home/poster');
        }

        $poster->save();

        return redirect()->route('home.poster')->with('success', __('Poster section created successfully.'));
    }

    public function updatePosterSection(Request $request, $id)
    {
        $validatedData = $this->validateWithTranslations($request, [
            'name' => 'required|string|max:255',
        ], [
            'img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $poster = PosterHome::findOrFail($id);

        $dataToUpdate = [
            'name_id' => $validatedData['name_id'],
            'name_en' => $validatedData['name_en'],
        ];

        if ($request->hasFile('img')) {
            $dataToUpdate['img'] = $this->replacePublicFile($poster->img, $validatedData['img'], 'back/home/poster');
        }

        $poster->update($dataToUpdate);

        return redirect()->route('home.poster')->with('success', __('Poster section updated successfully.'));
    }

    public function deletePosterSection($id)
    {
        $poster = PosterHome::findOrFail($id);

        $this->deletePublicFile($poster->img, 'back/home/poster');

        $poster->delete();

        return redirect()->route('home.poster')->with('success', __('Poster section deleted successfully.'));
    }

    /** ============================== HERO SECTION CONTROLLERS ============================== **/

    public function heroSection()
    {
        $heroes = HeroHome::with('homeMenu')->get();
        $lastUpdated = HeroHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.hero_section.index', [
            'heroes' => $heroes,
            'lastUpdated' => $lastUpdated,
        ]);
    }

    public function updateHeroSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'bg_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $hero = HeroHome::findOrFail($id);

        if (isset($validatedData['bg_img'])) {
            $hero->update([
                'bg_img' => $this->replacePublicFile($hero->bg_img, $validatedData['bg_img'], 'back/home/hero'),
            ]);
        }

        return redirect()->route('home.hero')->with('success', __('Hero section updated successfully.'));
    }

    /** ============================== ABOUT CONTROLLERS ============================== **/

    public function aboutSection()
    {
        $lang = App::getLocale();

        $abouts = AboutHome::all();
        $lastUpdated = AboutHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.about_section.index', [
            'abouts' => $abouts,
            'lastUpdated' => $lastUpdated,
            'lang' => $lang,
        ]);
    }

    public function updateAboutSection(Request $request, $id)
    {
        $validatedData = $this->validateWithTranslations($request, [
            'text' => 'required|string',
        ], [
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $about = AboutHome::findOrFail($id);

        $dataToUpdate = [
            'text_id' => $validatedData['text_id'],
            'text_en' => $validatedData['text_en'],
        ];

        if (isset($validatedData['img'])) {
            $dataToUpdate['img'] = $this->replacePublicFile($about->img, $validatedData['img'], 'back/home/about');
        }

        $about->update($dataToUpdate);

        return redirect()->route('home.about')->with('success', __('About section updated successfully.'));
    }

    /** ============================== SERVICE CONTROLLERS ============================== **/

    public function serviceSection()
    {
        $lang = App::getLocale();

        $services = ServiceHome::all();
        $lastUpdated = ServiceHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.service_section.index', [
            'services' => $services,
            'lastUpdated' => $lastUpdated,
            'lang' => $lang,
        ]);
    }

    public function updateServiceSection(Request $request, $id)
    {
        $validatedData = $this->validateWithTranslations($request, [
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
        ]);

        ServiceHome::findOrFail($id)->update($validatedData);

        return redirect()->route('home.service')->with('success', __('Service section updated successfully.'));
    }

    /** ============================== ADVANTAGE CONTROLLERS ============================== **/

    public function advantageSection()
    {
        $lang = App::getLocale();

        $advantages = AdvantageHome::all();
        $lastUpdated = AdvantageHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.advantage_section.index', [
            'advantages' => $advantages,
            'lastUpdated' => $lastUpdated,
            'lang' => $lang,
        ]);
    }

    public function updateAdvantageSection(Request $request, $id)
    {
        $validatedData = $this->validateWithTranslations($request, [
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
        ]);

        AdvantageHome::findOrFail($id)->update($validatedData);

        return redirect()->route('home.advantage')->with('success', __('Advantage section updated successfully.'));
    }

    /** ============================== PROFILE CONTROLLERS ============================== **/

    public function profileSection()
    {
        $profiles = ProfileHome::with('homeMenu')->get();
        $lastUpdated = ProfileHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.profile_section.index', [
            'profiles' => $profiles,
            'lastUpdated' => $lastUpdated,
        ]);
    }

    public function updateProfileSection(Request $request, $id)
    {
        // Nama orang tidak diterjemahkan, jabatan (position) diterjemahkan
        $validatedData = $this->validateWithTranslations($request, [
            'position' => 'nullable|string|max:255',
        ], [
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'name' => 'required|string|max:255',
        ]);

        $profile = ProfileHome::findOrFail($id);

        $dataToUpdate = [
            'name' => $validatedData['name'],
            'position_id' => $validatedData['position_id'] ?? null,
            'position_en' => $validatedData['position_en'],
        ];

        if (isset($validatedData['img'])) {
            $dataToUpdate['img'] = $this->replacePublicFile($profile->img, $validatedData['img'], 'back/home/profile');
        }

        $profile->update($dataToUpdate);

        return redirect()->route('home.profile')->with('success', __('Profile section updated successfully.'));
    }

    /** ============================== BUSINESS CONTROLLERS ============================== **/

    public function businessSection()
    {
        $lang = App::getLocale();

        $business = BusinessHome::all();
        $lastUpdated = BusinessHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.business_section.index', [
            'business' => $business,
            'lastUpdated' => $lastUpdated,
            'lang' => $lang,
        ]);
    }

    public function updateBusinessSection(Request $request, $id)
    {
        $validatedData = $this->validateWithTranslations($request, [
            'title' => 'required|string|max:255',
            'desc' => 'required|string|max:255',
        ], [
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'form_link' => 'required|url|max:255',
        ]);

        $business = BusinessHome::findOrFail($id);

        if (isset($validatedData['img'])) {
            $validatedData['img'] = $this->replacePublicFile($business->img, $validatedData['img'], 'back/home/business');
        }

        $business->update($validatedData);

        return redirect()->route('home.business')->with('success', __('Business section updated successfully.'));
    }

    /** ============================== SOSMED CONTROLLERS ============================== **/

    public function sosmedSection()
    {
        $sosmed = SosmedHome::with('homeMenu')->get();
        $lastUpdated = SosmedHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.sosmed_section.index', [
            'sosmed' => $sosmed,
            'lastUpdated' => $lastUpdated,
        ]);
    }

    public function updateSosmedSection(Request $request, $id)
    {
        $validatedData = $this->validateWithTranslations($request, [
            'title' => 'required|string|max:255',
        ], [
            'video' => 'nullable|mimetypes:video/mp4,video/mpeg,video/x-matroska,video/quicktime|max:51200', // 50MB
            'sosmed_link' => 'nullable|url|max:255',
        ]);

        $sosmed = SosmedHome::findOrFail($id);

        $dataToUpdate = [
            'title_id' => $validatedData['title_id'],
            'title_en' => $validatedData['title_en'],
            'sosmed_link' => $validatedData['sosmed_link'] ?? null,
        ];

        if (isset($validatedData['video'])) {
            $dataToUpdate['video'] = $this->replacePublicFile($sosmed->video, $validatedData['video'], 'back/home/sosmed');
        }

        $sosmed->update($dataToUpdate);

        return redirect()->route('home.sosmed')->with('success', __('Social media section updated successfully.'));
    }

    /** ============================== GALLERY CONTROLLERS ============================== **/

    public function gallerySection()
    {
        $gallery = GalleryHome::all();
        $lastUpdated = GalleryHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.gallery_section.index', [
            'gallery' => $gallery,
            'lastUpdated' => $lastUpdated,
        ]);
    }

    private function galleryRules(bool $imageRequired): array
    {
        return [
            'img'   => ($imageRequired ? 'required' : 'nullable') . '|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|digits:4',
        ];
    }

    public function createGallerySection(Request $request)
    {
        // kolom img wajib (NOT NULL), jadi gambar harus diunggah saat membuat item baru
        $validatedData = $this->validateWithTranslations($request, [
            'title' => 'required|string|max:255',
            'desc'  => 'nullable|string|max:255',
        ], $this->galleryRules(true));

        $gallery = new GalleryHome([
            'title_id' => $validatedData['title_id'],
            'title_en' => $validatedData['title_en'],
            'desc_id' => $validatedData['desc_id'] ?? null,
            'desc_en' => $validatedData['desc_en'],
            'month' => $validatedData['month'],
            'year' => $validatedData['year'],
        ]);

        // Ikut menu yang dipakai item gallery lain (menu Gallery di tabel home_menus)
        $gallery->home_menu_id = GalleryHome::value('home_menu_id')
            ?? HomeMenu::where('title_id', 'like', '%galeri%')->orWhere('title_en', 'like', '%gallery%')->value('id')
            ?? 8;
        $gallery->img = $this->storePublicFile($validatedData['img'], 'back/home/gallery');

        $gallery->save();

        return redirect()->route('home.gallery')->with('success', __('Gallery item created successfully.'));
    }

    public function updateGallerySection(Request $request, $id)
    {
        $validatedData = $this->validateWithTranslations($request, [
            'title' => 'required|string|max:255',
            'desc'  => 'nullable|string|max:255',
        ], $this->galleryRules(false));

        $gallery = GalleryHome::findOrFail($id);

        $dataToUpdate = [
            'title_id' => $validatedData['title_id'],
            'title_en' => $validatedData['title_en'],
            'desc_id' => $validatedData['desc_id'] ?? null,
            'desc_en' => $validatedData['desc_en'],
            'month' => $validatedData['month'],
            'year' => $validatedData['year'],
        ];

        if ($request->hasFile('img')) {
            $dataToUpdate['img'] = $this->replacePublicFile($gallery->img, $validatedData['img'], 'back/home/gallery');
        }

        $gallery->update($dataToUpdate);

        return redirect()->route('home.gallery')->with('success', __('Gallery item updated successfully.'));
    }

    public function deleteGallerySection($id)
    {
        $gallery = GalleryHome::findOrFail($id);

        $this->deletePublicFile($gallery->img, 'back/home/gallery');

        $gallery->delete();

        return redirect()->route('home.gallery')->with('success', __('Gallery item deleted successfully.'));
    }
}
