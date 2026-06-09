<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
{
    /**
     * Show admin creation form (Protected - Admin only)
     */
    public function create()
    {
        return view('admin.create-admin');
    }

    /**
     * Store new admin (Protected - Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string',
        ]);

        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'status' => 'active',
            'phone' => $validated['phone'] ?? null,
            'batch' => 'Admin',
        ]);

        // Log the action
        \Log::info('Admin created by ' . auth()->user()->name, [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.users.list')
            ->with('success', 'Admin account created successfully!');
    }

    /**
     * List all admins
     */
    public function listAdmins()
    {
        $admins = User::where('role', 'admin')->paginate(15);
        return view('admin.list-admins', compact('admins'));
    }

    /**
     * Delete admin
     */
    public function destroyAdmin(User $admin)
    {
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own admin account!');
        }

        \Log::info('Admin deleted by ' . auth()->user()->name, [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
        ]);

        $admin->delete();

        return back()->with('success', 'Admin account deleted successfully!');
    }
}