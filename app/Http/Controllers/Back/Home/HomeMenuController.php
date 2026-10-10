<?php

namespace App\Http\Controllers\Back\Home;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesPublicFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
    use ManagesPublicFiles;

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
        $validatedData = $request->validate([
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'sub_title_id' => 'nullable|string|max:255',
            'sub_title_en' => 'nullable|string|max:255',
        ]);

        $homeMenu = HomeMenu::findOrFail($id);
        $homeMenu->update($validatedData);

        return redirect()->route('home.menu')->with('success', 'Home Menu updated successfully.');
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
        $validatedData = $request->validate([
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
        ]);

        $newPoster = new PosterHome();
        $newPoster->name = $validatedData['name'];

        if ($request->hasFile('img')) {
            $file = $validatedData['img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $file->storeAs(
                'back/home/poster',
                $filename,
                'public'
            );

            $newPoster->img = 'back/home/poster/' . $filename;
        }

        // FIX: sebelumnya method ini tidak pernah memanggil save(),
        // sehingga data poster tidak pernah tersimpan ke database
        // meskipun file gambar sudah ter-upload ke storage.
        $newPoster->save();

        return redirect()->route('home.poster')->with('success', 'Poster section created successfully');
    }

    public function updatePosterSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'name' => 'required|string|max:255',
        ]);

        $poster = PosterHome::findOrFail($id);

        $dataToUpdate = [
            'name' => $validatedData['name'],
        ];

        if ($request->hasFile('img')) {
            // Hapus gambar lama
            if ($poster->img && Storage::disk('public')->exists($poster->img)) {
                Storage::disk('public')->delete($poster->img);
            }

            // Upload gambar baru
            $file = $validatedData['img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $file->storeAs(
                'back/home/poster',
                $filename,
                'public'
            );

            // Simpan relative path ke database
            $dataToUpdate['img'] = 'back/home/poster/' . $filename;
        }

        $poster->update($dataToUpdate);

        return redirect()
            ->route('home.poster')
            ->with('success', 'Poster section updated successfully');
    }

    public function deletePosterSection($id)
    {
        $poster = PosterHome::findOrFail($id);

        if ($poster->img && Storage::disk('public')->exists($poster->img)) {
            Storage::disk('public')->delete($poster->img);
        }

        $poster->delete();

        return redirect()
            ->route('home.poster')
            ->with('success', 'Poster section deleted successfully');
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
            'bg_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hero = HeroHome::with('homeMenu')->findOrFail($id);

        $dataToUpdate = [];

        if (isset($validatedData['bg_img'])) {
            $dataToUpdate['bg_img'] = $this->replacePublicFile($hero->bg_img, $validatedData['bg_img'], 'back/home/hero');
        }

        // Jika ada data untuk diupdate, panggil update()
        if (!empty($dataToUpdate)) {
            $hero->update($dataToUpdate);
        }

        return redirect()->route('home.hero')->with('success', 'Hero section updated successfully');
    }

    /** ============================== ABOUT CONTROLLERS ============================== **/

    public function aboutSection()
    {
        $lang = App::getLocale();

        $abouts = AboutHome::select('id', 'img', 'text_id', 'text_en', 'updated_at')->get();
        $lastUpdated = AboutHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.about_section.index', [
            'abouts' => $abouts,
            'lastUpdated' => $lastUpdated,
            'lang' => $lang,
        ]);
    }

    public function updateAboutSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_id' => 'required|string',
            'text_en' => 'nullable|string',
        ]);

        $about = AboutHome::with('homeMenu')->findOrFail($id);

        $dataToUpdate = [
            'text_id' => $validatedData['text_id'],
            'text_en' => $validatedData['text_en'] ?? null,
        ];

        if (isset($validatedData['img'])) {
            $dataToUpdate['img'] = $this->replacePublicFile($about->img, $validatedData['img'], 'back/home/about');
        }

        $about->update($dataToUpdate);

        return redirect()->route('home.about')->with('success', 'About section updated successfully');
    }

    /** ============================== SERVICE CONTROLLERS ============================== **/

    public function serviceSection()
    {
        $lang = App::getLocale();

        $services = ServiceHome::select('id', 'name_id', 'name_en', 'desc_id', 'desc_en', 'updated_at')->get();
        $lastUpdated = ServiceHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.service_section.index', [
            'services' => $services,
            'lastUpdated' => $lastUpdated,
            'lang' => $lang,
        ]);
    }

    public function updateServiceSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name_id' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'desc_id' => 'required|string',
            'desc_en' => 'nullable|string',
        ]);

        $services = ServiceHome::with('homeMenu')->findOrFail($id);
        $services->update($validatedData);

        return redirect()->route('home.service')->with('success', 'Service section updated successfully.');
    }

    /** ============================== ADVANTAGE CONTROLLERS ============================== **/

    public function advantageSection()
    {
        $lang = App::getLocale();

        $advantages = AdvantageHome::select('id', 'title_id', 'title_en', 'desc_id', 'desc_en', 'updated_at')->get();
        $lastUpdated = AdvantageHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.advantage_section.index', [
            'advantages' => $advantages,
            'lastUpdated' => $lastUpdated,
            'lang' => $lang,
        ]);
    }

    public function updateAdvantageSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'desc_id' => 'required|string',
            'desc_en' => 'nullable|string',
        ]);

        $advantages = AdvantageHome::with('homeMenu')->findOrFail($id);
        $advantages->update($validatedData);

        return redirect()->route('home.advantage')->with('success', 'Advantage section updated successfully.');
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
        $validatedData = $request->validate([
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        $profile = ProfileHome::with('homeMenu')->findOrFail($id);

        $dataToUpdate = [];

        // Update text hanya kalau ada input text baru (tidak null)
        foreach (['name', 'position'] as $field) {
            if (isset($validatedData[$field])) {
                $dataToUpdate[$field] = $validatedData[$field];
            }
        }

        if (isset($validatedData['img'])) {
            $dataToUpdate['img'] = $this->replacePublicFile($profile->img, $validatedData['img'], 'back/home/profile');
        }

        if (!empty($dataToUpdate)) {
            $profile->update($dataToUpdate);
        }

        return redirect()->route('home.profile')->with('success', 'Profile section updated successfully');
    }

    /** ============================== BUSINESS CONTROLLERS ============================== **/

    public function businessSection()
    {
        $lang = App::getLocale();

        $business = BusinessHome::select('id', 'img', 'title_id', 'title_en', 'desc_id', 'desc_en', 'form_link', 'updated_at')->get();
        $lastUpdated = BusinessHome::latest('updated_at')->value('updated_at');

        return view('back.home_menu.business_section.index', [
            'business' => $business,
            'lastUpdated' => $lastUpdated,
            'lang' => $lang,
        ]);
    }

    public function updateBusinessSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'desc_id' => 'required|string|max:255',
            'desc_en' => 'nullable|string|max:255',
            'form_link' => 'required|url|max:255',
        ]);

        $business = BusinessHome::with('homeMenu')->findOrFail($id);

        $dataToUpdate = [];

        foreach (['title_id', 'title_en', 'desc_id', 'desc_en', 'form_link'] as $field) {
            $dataToUpdate[$field] = $validatedData[$field] ?? null;
        }

        if (isset($validatedData['img'])) {
            $dataToUpdate['img'] = $this->replacePublicFile($business->img, $validatedData['img'], 'back/home/business');
        }

        $business->update($dataToUpdate);

        return redirect()->route('home.business')->with('success', 'Business section updated successfully');
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
        $validatedData = $request->validate([
            'video' => 'nullable|mimetypes:video/mp4,video/mpeg,video/x-matroska,video/quicktime|max:51200', // 50MB
            'title' => 'required|string',
            'sosmed_link' => 'nullable|url',
        ]);

        $sosmed = SosmedHome::with('homeMenu')->findOrFail($id);

        $dataToUpdate = [];

        // Update field teks
        foreach (['title', 'sosmed_link'] as $field) {
            if (isset($validatedData[$field])) {
                $dataToUpdate[$field] = $validatedData[$field];
            }
        }

        // Update file video
        if (isset($validatedData['video'])) {
            $dataToUpdate['video'] = $this->replacePublicFile($sosmed->video, $validatedData['video'], 'back/home/sosmed');
        }

        if (!empty($dataToUpdate)) {
            $sosmed->update($dataToUpdate);
        }

        return redirect()->route('home.sosmed')->with('success', 'Sosmed section updated successfully');
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

    public function createGallerySection(Request $request)
    {
        $validatedData = $request->validate([
            'img'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'required|string',
            'desc'  => 'nullable|string',
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|digits:4',
        ]);

        $newGallery = new GalleryHome();
        $newGallery->home_menu_id = 8;
        $newGallery->title = $validatedData['title'];
        $newGallery->desc = $validatedData['desc'] ?? null;
        $newGallery->month = $validatedData['month'];
        $newGallery->year = $validatedData['year'];

        if ($request->hasFile('img')) {
            $newGallery->img = $this->storePublicFile($validatedData['img'], 'back/home/gallery');
        }

        $newGallery->save();

        return redirect()->route('home.gallery')->with('success', 'Gallery section created successfully');
    }

    public function updateGallerySection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'img'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'required|string',
            'desc'  => 'nullable|string',
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|digits:4',
        ]);

        $gallery = GalleryHome::findOrFail($id);

        $dataToUpdate = [
            'title' => $validatedData['title'],
            'desc'  => $validatedData['desc'] ?? null,
            'month' => $validatedData['month'],
            'year'  => $validatedData['year'],
        ];

        // Handle upload image jika ada
        if ($request->hasFile('img')) {
            $dataToUpdate['img'] = $this->replacePublicFile($gallery->img, $validatedData['img'], 'back/home/gallery');
        }

        $gallery->update($dataToUpdate);

        return redirect()->route('home.gallery')->with('success', 'Gallery section updated successfully');
    }

    public function deleteGallerySection($id)
    {
        $gallery = GalleryHome::findOrFail($id);

        // Hapus file gambar jika ada
        $this->deletePublicFile($gallery->img, 'back/home/gallery');

        $gallery->delete();

        return redirect()->route('home.gallery')->with('success', 'Gallery section deleted successfully');
    }
}