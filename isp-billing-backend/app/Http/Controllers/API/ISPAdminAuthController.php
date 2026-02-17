<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ISPAdmin;
use App\Models\ISPOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ISPAdminAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'subdomain' => 'required|string|max:100', // Should be passed from frontend detection
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if subdomain exists in orders and is active (optional, but good for security)
        $orderExists = ISPOrder::where('subdomain', $request->subdomain)->exists();
        if (!$orderExists) {
             return response()->json([
                'success' => false,
                'message' => 'Subdomain tidak valid atau belum terdaftar di sistem order.'
            ], 404);
        }

        // Check if subdomain is already registered in isp_admins
        if (ISPAdmin::where('subdomain', $request->subdomain)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Admin untuk subdomain ini sudah terdaftar.'
            ], 409);
        }

        // Create ISP Admin
        $admin = ISPAdmin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'subdomain' => $request->subdomain,
        ]);

        // TODO: Send verification email here

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil! Silakan login.',
            'user' => $admin
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'subdomain' => 'required|string',
        ]);

        // Find admin by email AND subdomain
        $admin = ISPAdmin::where('email', $request->email)
                        ->where('subdomain', $request->subdomain)
                        ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial tidak valid untuk subdomain ini.'
            ], 401);
        }

        // Create Token
        $token = $admin->createToken('isp_admin_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $admin
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }
}
