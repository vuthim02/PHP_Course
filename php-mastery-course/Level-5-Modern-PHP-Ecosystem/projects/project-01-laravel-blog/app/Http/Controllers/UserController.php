<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount('posts', 'comments')
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isAdmin() && User::where('is_admin', true)->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin user.');
        }

        $user->posts()->delete();
        $user->comments()->delete();
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
