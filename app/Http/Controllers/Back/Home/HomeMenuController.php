<?php

namespace App\Http\Controllers\Back\Home;

use App\Http\Controllers\Controller;
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
    /** ============================== HOME MENU CONTROLLERS ============================== **/
    public function homeMenu()
    {
        $lang = App::getLocale();
        $titleCol = 'title_' . $lang;
        $subTitleCol = 'sub_title_' . $lang;

        $homeMenus = HomeMenu::select('id', "$titleCol as title", "$subTitleCol as sub_title", 'updated_at')->get();
        $lastUpdated = HomeMenu::latest('updated_at')->value('updated_at');

        return view('back.home_menu.index', compact('homeMenus', 'lastUpdated', 'lang'));
    }
    public function updateHomeMenu(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
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
            $file->storeAs('public/back/home/poster/', $filename);
            $newPoster->img = $filename;
        }

        $newPoster->save();

        return redirect()->route('home.poster')->with('success', 'Poster section created successfully');
    }
    public function updatePosterSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
        ]);

        $poster = PosterHome::findOrFail($id);

        // Pastikan name langsung dimasukkan ke dalam data yang akan diupdate
        $dataToUpdate = [
            'name' => $validatedData['name'],
        ];

        // Handle upload image jika ada
        if ($request->hasFile('img')) {
            if ($poster->img && Storage::exists('public/back/home/poster/' . $poster->img)) {
                Storage::delete('public/back/home/poster/' . $poster->img);
            }

            $file = $validatedData['img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/home/poster/', $filename);

            $dataToUpdate['img'] = $filename;
        }

        // Update data
        $poster->update($dataToUpdate);

        return redirect()->route('home.poster')->with('success', 'Poster section updated successfully');
    }
    public function deletePosterSection($id)
    {
        $poster = PosterHome::findOrFail($id);

        // Hapus file gambar jika ada
        if ($poster->img && Storage::exists('public/back/home/poster/' . $poster->img)) {
            Storage::delete('public/back/home/poster/' . $poster->img);
        }

        $poster->delete();

        return redirect()->route('home.poster')->with('success', 'Poster section deleted successfully');
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
            if ($hero->bg_img && Storage::exists('public/back/home/hero/' . $hero->bg_img)) {
                Storage::delete('public/back/home/hero/' . $hero->bg_img);
            }

            $file = $validatedData['bg_img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/home/hero/', $filename);

            $dataToUpdate['bg_img'] = $filename;
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
            'text' => 'nullable|string',
        ]);

        $about = AboutHome::with('homeMenu')->findOrFail($id);

        $dataToUpdate = [];

        // Update text hanya kalau ada input text baru (tidak null)
        if (isset($validatedData['text'])) {
            $dataToUpdate['text'] = $validatedData['text'];
        }

        if (isset($validatedData['img'])) {
            if ($about->img && Storage::exists('public/back/home/about/' . $about->img)) {
                Storage::delete('public/back/home/about/' . $about->img);
            }

            $file = $validatedData['img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/home/about/', $filename);

            $dataToUpdate['img'] = $filename;
        }

        if (!empty($dataToUpdate)) {
            $about->update($dataToUpdate);
        }

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
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string'
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
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string'
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
            if ($profile->img && Storage::exists('public/back/home/profile/' . $profile->img)) {
                Storage::delete('public/back/home/profile/' . $profile->img);
            }

            $file = $validatedData['img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/home/profile/', $filename);

            $dataToUpdate['img'] = $filename;
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
            'title' => 'required|string',
            'desc' => 'nullable|string',
            'form_link' => 'nullable|url',
        ]);

        $business = BusinessHome::with('homeMenu')->findOrFail($id);

        $dataToUpdate = [];

        // Update text hanya kalau ada input text baru (tidak null)
        foreach (['title', 'desc', 'form_link'] as $field) {
            if (isset($validatedData[$field])) {
                $dataToUpdate[$field] = $validatedData[$field];
            }
        }

        if (isset($validatedData['img'])) {
            if ($business->img && Storage::exists('public/back/home/business/' . $business->img)) {
                Storage::delete('public/back/home/business/' . $business->img);
            }

            $file = $validatedData['img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/home/business/', $filename);

            $dataToUpdate['img'] = $filename;
        }

        if (!empty($dataToUpdate)) {
            $business->update($dataToUpdate);
        }

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
            if ($sosmed->video && Storage::exists('public/back/home/sosmed/' . $sosmed->video)) {
                Storage::delete('public/back/home/sosmed/' . $sosmed->video);
            }

            $file = $validatedData['video'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/home/sosmed/', $filename);

            $dataToUpdate['video'] = $filename;
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
            $file = $validatedData['img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/home/gallery/', $filename);
            $newGallery->img = $filename;
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
            if ($gallery->img && Storage::exists('public/back/home/gallery/' . $gallery->img)) {
                Storage::delete('public/back/home/gallery/' . $gallery->img);
            }

            $file = $validatedData['img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/home/gallery/', $filename);

            $dataToUpdate['img'] = $filename;
        }

        $gallery->update($dataToUpdate);

        return redirect()->route('home.gallery')->with('success', 'Gallery section updated successfully');
    }
    public function deleteGallerySection($id)
    {
        $gallery = GalleryHome::findOrFail($id);

        // Hapus file gambar jika ada
        if ($gallery->img && Storage::exists('public/back/home/gallery/' . $gallery->img)) {
            Storage::delete('public/back/home/gallery/' . $gallery->img);
        }

        $gallery->delete();

        return redirect()->route('home.gallery')->with('success', 'Gallery section deleted successfully');
    }

}
