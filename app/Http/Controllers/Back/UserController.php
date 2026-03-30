<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $users = User::all(); // akses penuh
        } else {
            $users = User::whereId($user->id)->get(); // user biasa hanya lihat dirinya
        }

        return view('back.user.index', compact('users'));
    }

    public function store(UserRequest $request) {
        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);
        User::create($data);

        return back()->with('success', 'User has been created');
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $data = $request->validated();

        // Jika password tidak diisi, hapus dari array agar tidak di-update
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        // Update semua field yang tersisa (termasuk role)
        User::findOrFail($id)->update($data);

        return back()->with('success', 'User has been updated');
    }

    public function destroy(string $id) {
        User::find($id)->delete();

        return back()->with('success', 'User has been deleted');

    }
}
