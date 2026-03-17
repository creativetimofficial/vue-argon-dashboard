<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if account is locked due to too many failed attempts
        $lockCheck = $this->checkLoginLock($request->email, $request->ip());
        if ($lockCheck) {
            return $lockCheck; // Return error response
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Record failed attempt
            $this->recordFailedAttempt($request->email, $request->ip());
            
            return response()->json([
                'message' => 'Email atau password salah. Silakan coba lagi.',
                'errors' => ['email' => ['Email atau password salah']]
            ], 422);
        }

        // Clear login attempts on successful credential check
        $this->clearLoginAttempts($request->email, $request->ip());

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

        // CRITICAL: Check if email is verified
        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Akun Anda belum diverifikasi. Silakan cek email Anda dan klik link verifikasi yang telah kami kirimkan.',
                'errors' => ['email' => ['Email belum diverifikasi']]
            ], 403);
        }
        
        // Check if account is still inactive after auto-activation
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account is inactive. Please verify your email first or contact administrator.'
            ], 403);
        }

        // --- MULTI-TENANCY CHECK ---
        // If we are on a tenant domain (identified by IdentifyTenant middleware),
        // ensure the user belongs to this tenant (ISP).
        // Super Admins are allowed to login everywhere.
        if (app()->bound('current_isp') && $user->role !== 'super_admin') {
            $currentIsp = app('current_isp');
            
            // For ISP Admins / Staff: must match isp_id
            if ($user->role === 'isp_admin' && $user->isp_id !== $currentIsp->id) {
                 return response()->json([
                    'message' => 'Invalid credentials for this ISP domain.',
                ], 403);
            }
            
            // For Customers: (Future implementation) must belong to the ISP
            // if ($user->role === 'customer' ... check relationship ...)
        }
        // ---------------------------

        $token = $user->createToken('auth-token')->plainTextToken;

        // Load ISP data if user is ISP admin
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        if ($user->role === 'isp_admin' && $user->isp) {
            $user->isp->load('subscriptionPackage');
            $userData['isp'] = [
                'id' => $user->isp->id,
                'company_name' => $user->isp->company_name,
                'subscription_package_id' => $user->isp->subscription_package_id,
                'subscription_status' => $user->isp->subscription_status,
                'subscription_end_date' => $user->isp->subscription_end_date,
                'approval_status' => $user->isp->approval_status,
                'is_active' => $user->isp->is_active,
                'package' => $user->isp->subscriptionPackage,
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

    public function user(Request $request)
    {
        \Illuminate\Support\Facades\Log::info("LoginController::user called for user: " . $request->user()->id);
        
        try {
            return response()->json([
                'user' => $request->user()->load('isp.subscriptionPackage')
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("LoginController::user error: " . $e->getMessage());
            return response()->json(['message' => 'Error loading user data'], 500);
        }
    }

    public function getTenantInfo(Request $request) 
    {
        if (app()->bound('current_isp')) {
            $isp = app('current_isp');
            return response()->json([
                'is_tenant' => true,
                'name' => $isp->company_name ?? $isp->name,
                'logo' => $isp->logo, // Ensure this is a full URL or handle in frontend
            ]);
        }
        
        return response()->json([
            'is_tenant' => false,
            'name' => 'PayNet', // Default branding
        ]);
    }

    /**
     * Check if login is locked due to too many failed attempts
     */
    protected function checkLoginLock($email, $ip)
    {
        $attempt = DB::table('login_attempts')
            ->where('email', $email)
            ->orWhere('ip_address', $ip)
            ->first();

        if ($attempt && $attempt->locked_until) {
            $lockedUntil = Carbon::parse($attempt->locked_until);
            
            if ($lockedUntil->isFuture()) {
                $minutesLeft = max(1, (int) ceil(now()->floatDiffInMinutes($lockedUntil)));
                
                return response()->json([
                    'message' => "Akun Anda terkunci karena terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$minutesLeft} menit.",
                    'locked_until' => $lockedUntil->toIso8601String(),
                    'errors' => ['email' => ['Akun terkunci']]
                ], 429);
            } else {
                // Lock expired, clear it
                DB::table('login_attempts')
                    ->where('email', $email)
                    ->orWhere('ip_address', $ip)
                    ->delete();
            }
        }

        return null;
    }

    /**
     * Record failed login attempt
     */
    protected function recordFailedAttempt($email, $ip)
    {
        $attempt = DB::table('login_attempts')
            ->where('email', $email)
            ->orWhere('ip_address', $ip)
            ->first();

        if ($attempt) {
            $newAttempts = $attempt->attempts + 1;
            $lockedUntil = null;

            // Lock account after 5 failed attempts
            if ($newAttempts >= 5) {
                $lockedUntil = now()->addMinutes(15);
            }

            DB::table('login_attempts')
                ->where('email', $email)
                ->orWhere('ip_address', $ip)
                ->update([
                    'attempts' => $newAttempts,
                    'locked_until' => $lockedUntil,
                    'updated_at' => now()
                ]);
        } else {
            DB::table('login_attempts')->insert([
                'email' => $email,
                'ip_address' => $ip,
                'attempts' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Clear login attempts on successful login
     */
    protected function clearLoginAttempts($email, $ip)
    {
        DB::table('login_attempts')
            ->where('email', $email)
            ->orWhere('ip_address', $ip)
            ->delete();
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()
        ]);
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
}