<?php

namespace Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalRegularUsers = User::where('role', 'user')->count();
        $users = User::latest()->paginate(10);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'totalRegularUsers',
            'users'
        ));
    }

    public function updateRole(Request $request, $user)
    {
        $targetUser = $user instanceof User ? $user : User::findOrFail($user);

        $validated = $request->validate([
            'role' => ['required', 'in:admin,user'],
        ]);

        $targetUser->update([
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', "Role user {$targetUser->name} berhasil diperbarui.");
    }

    public function destroy($user)
    {
        $targetUser = $user instanceof User ? $user : User::findOrFail($user);

        if (auth()->id() === (int) $targetUser->id) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $targetUser->delete();

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil dihapus.');
    }
}
