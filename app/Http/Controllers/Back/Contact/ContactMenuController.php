<?php

namespace App\Http\Controllers\Back\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Contact\ContactMenu;
use App\Models\Contact\HeroContact;
use App\Models\Contact\UsContact;

class ContactMenuController extends Controller
{
    /** ============================== CONTACT MENU CONTROLLERS ============================== **/
    public function contactMenu()
    {
        $contactMenus = ContactMenu::all();
        $lastUpdated = ContactMenu::latest('updated_at')->value('updated_at');

        return view('back.contact_menu.index', [
            'contactMenus' => $contactMenus,
            'lastUpdated' => $lastUpdated,
        ]);
    }
    public function updateContactMenu(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
        ]);

        $contactMenu = ContactMenu::findOrFail($id);

        $contactMenu->update($validatedData);

        return redirect()->route('contact.menu')->with('success', 'Contact Menu updated successfully.');
    }

    /** ============================== HERO SECTION CONTROLLERS ============================== **/
    public function heroSection()
    {
        $heroes = HeroContact::with('contactMenu')->get();
        $lastUpdated = HeroContact::latest('updated_at')->value('updated_at');

        return view('back.contact_menu.hero_section.index', [
            'heroes' => $heroes,
            'lastUpdated' => $lastUpdated,
        ]);
    }
    public function updateHeroSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'bg_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hero = HeroContact::with('contactMenu')->findOrFail($id);

        $dataToUpdate = [];

        if (isset($validatedData['bg_img'])) {
            if ($hero->bg_img && Storage::exists('public/back/contact/hero/' . $hero->bg_img)) {
                Storage::delete('public/back/contact/hero/' . $hero->bg_img);
            }

            $file = $validatedData['bg_img'];
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/back/contact/hero/', $filename);

            $dataToUpdate['bg_img'] = $filename;
        }

        // Jika ada data untuk diupdate, panggil update()
        if (!empty($dataToUpdate)) {
            $hero->update($dataToUpdate);
        }

        return redirect()->route('contact.hero')->with('success', 'Hero section updated successfully');
    }

    /** ============================== CONTACT US CONTROLLERS ============================== **/
    public function usSection()
    {
        $us = UsContact::with('contactMenu')->get();
        $lastUpdated = UsContact::latest('updated_at')->value('updated_at');

        return view('back.contact_menu.usContact_section.index', [
            'us' => $us,
            'lastUpdated' => $lastUpdated,
        ]);
    }
    public function updateUsSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string'
        ]);

        $us = UsContact::with('contactMenu')->findOrFail($id);

        $us->update($validatedData);

        return redirect()->route('contact.usContact')->with('success', 'Contact Us section updated successfully.');
    }
}
