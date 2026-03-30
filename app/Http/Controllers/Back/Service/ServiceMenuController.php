<?php

namespace App\Http\Controllers\Back\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Service\ServiceMenu;
use App\Models\Service\HeroService;
use App\Models\Service\OurService;

class ServiceMenuController extends Controller
{
    /** ============================== SERVICE MENU CONTROLLERS ============================== **/
    public function serviceMenu()
    {
        $serviceMenus = ServiceMenu::all();
        $lastUpdated = ServiceMenu::latest('updated_at')->value('updated_at');

        return view('back.service_menu.index', [
            'serviceMenus' => $serviceMenus,
            'lastUpdated' => $lastUpdated,
        ]);
    }
    public function updateServiceMenu(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
        ]);

        $serviceMenu = ServiceMenu::findOrFail($id);

        $serviceMenu->update($validatedData);

        return redirect()->route('service.menu')->with('success', 'Service Menu updated successfully.');
    }

    /** ============================== HERO SECTION CONTROLLERS ============================== **/
    public function heroSection()
    {
        $heroes = HeroService::with('serviceMenu')->get();
        $lastUpdated = HeroService::latest('updated_at')->value('updated_at');

        return view('back.service_menu.hero_section.index', [
            'heroes' => $heroes,
            'lastUpdated' => $lastUpdated,
        ]);
    }
    public function updateHeroSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'bg_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hero = HeroService::with('serviceMenu')->findOrFail($id);

        $dataToUpdate = [];

        if (isset($validatedData['bg_img'])) {
            if ($hero->bg_img && Storage::exists('public/back/service/hero/' . $hero->bg_img)) {
                Storage::delete('public/back/service/hero/' . $hero->bg_img);
            }

            $file = $validatedData['bg_img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/service/hero/', $filename);

            $dataToUpdate['bg_img'] = $filename;
        }

        // Jika ada data untuk diupdate, panggil update()
        if (!empty($dataToUpdate)) {
            $hero->update($dataToUpdate);
        }

        return redirect()->route('service.hero')->with('success', 'Hero section updated successfully');
    }

    /** ============================== OUR SERVICE CONTROLLERS ============================== **/
    public function ourSection()
    {
        $ours = OurService::with('serviceMenu')->get();
        $lastUpdated = OurService::latest('updated_at')->value('updated_at');

        return view('back.service_menu.ourService_section.index', [
            'ours' => $ours,
            'lastUpdated' => $lastUpdated,
        ]);
    }
    public function updateOurSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string'
        ]);

        $ours = OurService::with('serviceMenu')->findOrFail($id);

        $ours->update($validatedData);

        return redirect()->route('service.ourService')->with('success', 'Our Service section updated successfully.');
    }
}
