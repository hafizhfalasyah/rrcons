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
        // Hanya admin yang boleh membuat akun
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);
        User::create($data);

        return back()->with('success', 'User has been created');
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $authUser = $request->user();
        $user = User::findOrFail($id);

        // Non-admin hanya boleh mengubah akunnya sendiri
        abort_unless($authUser->isAdmin() || $authUser->is($user), 403);

        $data = $request->validated();

        // Role hanya bisa diubah admin, dan admin tidak bisa mengubah role dirinya sendiri
        // (mencegah user menaikkan hak aksesnya atau admin terkunci dari panel)
        if (!$authUser->isAdmin() || $authUser->is($user)) {
            unset($data['role']);
        }

        // Jika password tidak diisi, hapus dari array agar tidak di-update
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        unset($data['password_confirmation']);

        $user->update($data);

        return back()->with('success', 'User has been updated');
    }

    public function destroy(Request $request, string $id) {
        $authUser = $request->user();
        $user = User::findOrFail($id);

        // Hanya admin yang boleh menghapus, dan tidak boleh menghapus dirinya sendiri
        abort_unless($authUser->isAdmin() && !$authUser->is($user), 403);

        $user->delete();

        return back()->with('success', 'User has been deleted');
    }
}
