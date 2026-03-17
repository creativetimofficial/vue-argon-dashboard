<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $ispId = auth()->user()->isp_id;
        
        // Return all users belonging to this ISP, except customers
        $staff = User::where('isp_id', $ispId)
            ->whereIn('role', ['admin', 'technician', 'isp_admin'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'technician', 'isp_admin'])],
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'isp_id' => auth()->user()->isp_id,
            'phone' => $request->phone,
            'is_active' => true,
            'email_verified_at' => now(), // Assume verified since created by admin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Staff member created successfully.',
            'data' => $user
        ], 201);
    }

    public function show($id)
    {
        $ispId = auth()->user()->isp_id;
        $user = User::where('isp_id', $ispId)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $ispId = auth()->user()->isp_id;
        $user = User::where('isp_id', $ispId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(['admin', 'technician', 'isp_admin'])],
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Security check: Prevent self-deactivation or self-role-change
        if ($user->id === auth()->id()) {
            if ($request->has('is_active') && !$request->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak bisa menonaktifkan akun Anda sendiri.'
                ], 403);
            }
            if ($request->role !== $user->role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak bisa mengubah role Anda sendiri.'
                ], 403);
            }
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active') ? $request->is_active : $user->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Staff member updated successfully.',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $ispId = auth()->user()->isp_id;
        $user = User::where('isp_id', $ispId)->findOrFail($id);
        
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member deleted successfully.'
        ]);
    }
}
