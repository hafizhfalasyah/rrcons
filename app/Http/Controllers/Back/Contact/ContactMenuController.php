<?php

namespace App\Http\Controllers\Back\Contact;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesPublicFiles;
use App\Http\Controllers\Concerns\ValidatesTranslations;
use Illuminate\Http\Request;

use App\Models\Contact\ContactMenu;
use App\Models\Contact\HeroContact;
use App\Models\Contact\UsContact;

class ContactMenuController extends Controller
{
    use ManagesPublicFiles, ValidatesTranslations;

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
        $validatedData = $this->validateWithTranslations($request, [
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
        ]);

        $contactMenu = ContactMenu::findOrFail($id);

        $contactMenu->update($validatedData);

        return redirect()->route('contact.menu')->with('success', __('Contact Menu updated successfully.'));
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
            'bg_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $hero = HeroContact::findOrFail($id);

        if ($request->hasFile('bg_img')) {
            $hero->update([
                'bg_img' => $this->replacePublicFile($hero->bg_img, $request->file('bg_img'), 'back/contact/hero'),
            ]);
        }

        return redirect()
            ->route('contact.hero')
            ->with('success', __('Hero section updated successfully.'));
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
        // "title" (mis. Alamat) dipakai sebagai kunci di halaman Contact, jadi tidak diubah di sini
        $validatedData = $this->validateWithTranslations($request, [
            'desc' => 'required|string|max:255',
        ]);

        $us = UsContact::with('contactMenu')->findOrFail($id);

        $us->update($validatedData);

        return redirect()->route('contact.usContact')->with('success', __('Contact Us section updated successfully.'));
    }
}
