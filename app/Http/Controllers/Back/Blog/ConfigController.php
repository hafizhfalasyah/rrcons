<?php

namespace App\Http\Controllers\Back\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Config;

class ConfigController extends Controller
{
    public function index() {
        return view('back.config.index', [
            'config' => Config::paginate(10)
        ]);
    }

    public function update(Request $request, String $id) {
        $data = $request->validate([
            'name' => 'required|min:3',
            'value' => 'required|min:3',
        ]);

        Config::findOrFail($id)->update($data);

        return back()->with('success', __('Config has been updated.'));
    }
}
