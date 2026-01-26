<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'errors' => ['email' => ['The provided credentials are incorrect.']]
            ], 422);
        }

        // If email is verified but account is inactive, activate it
        if ($user->email_verified_at && !$user->is_active) {
            $user->update(['is_active' => true]);
            
            // Also activate ISP if exists
            if ($user->isp_id) {
                $isp = $user->isp;
                if ($isp && !$isp->is_active) {
                    $isp->update(['is_active' => true]);
                }
            }
        }
        
        // Check if account is still inactive after auto-activation
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account is inactive. Please verify your email first or contact administrator.'
            ], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        // Load ISP data if user is ISP admin
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        if ($user->role === 'isp_admin' && $user->isp) {
            $userData['isp'] = [
                'id' => $user->isp->id,
                'subscription_package_id' => $user->isp->subscription_package_id,
                'subscription_status' => $user->isp->subscription_status,
                'approval_status' => $user->isp->approval_status,
            ];
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $userData,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('isp.subscriptionPackage')
        ]);
    }
}