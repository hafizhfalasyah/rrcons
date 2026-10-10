<?php

namespace App\Http\Controllers\Back\RRfuture;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesPublicFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\RRfuture\RRfutureMenu;
use App\Models\RRfuture\HeroRRfuture;
use App\Models\RRfuture\ProductRRfuture;

class RRfutureMenuController extends Controller
{
    use ManagesPublicFiles;

    /** ============================== RRFUTURE MENU CONTROLLERS ============================== **/
    public function rrfutureMenu()
    {
        $rrfutureMenus = RRfutureMenu::all();
        $lastUpdated = RRfutureMenu::latest('updated_at')->value('updated_at');

        return view('back.rrfuture_menu.index', [
            'rrfutureMenus' => $rrfutureMenus,
            'lastUpdated' => $lastUpdated,
        ]);
    }
    public function updateRRfutureMenu(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
        ]);

        $rrfutureMenu = RRfutureMenu::findOrFail($id);

        $rrfutureMenu->update($validatedData);

        return redirect()->route('rrfuture.menu')->with('success', 'RRFuture Menu updated successfully.');
    }

    /** ============================== HERO SECTION CONTROLLERS ============================== **/
    public function heroSection()
    {
        $heroes = HeroRRfuture::with('rrfutureMenu')->get();
        $lastUpdated = HeroRRfuture::latest('updated_at')->value('updated_at');

        return view('back.rrfuture_menu.hero_section.index', [
            'heroes' => $heroes,
            'lastUpdated' => $lastUpdated,
        ]);
    }
    public function updateHeroSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'bg_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hero = HeroRRfuture::with('rrfutureMenu')->findOrFail($id);

        $dataToUpdate = [];

        if (isset($validatedData['bg_img'])) {
            $dataToUpdate['bg_img'] = $this->replacePublicFile($hero->bg_img, $validatedData['bg_img'], 'back/rrfuture/hero');
        }

        // Jika ada data untuk diupdate, panggil update()
        if (!empty($dataToUpdate)) {
            $hero->update($dataToUpdate);
        }

        return redirect()->route('rrfuture.hero')->with('success', 'Hero section updated successfully');
    }

    /** ============================== PRODUCT CONTROLLERS ============================== **/
    public function productSection()
    {
        $products = ProductRRfuture::with('rrfutureMenu')->get();
        $lastUpdated = ProductRRfuture::latest('updated_at')->value('updated_at');

        return view('back.rrfuture_menu.product_section.index', [
            'products' => $products,
            'lastUpdated' => $lastUpdated,
        ]);
    }
    public function updateProductSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string'
        ]);

        $products = ProductRRfuture::with('rrfutureMenu')->findOrFail($id);

        $products->update($validatedData);

        return redirect()->route('rrfuture.product')->with('success', 'Service section updated successfully.');
    }
}
