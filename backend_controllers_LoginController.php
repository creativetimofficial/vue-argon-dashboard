<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Universal login endpoint.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Please verify your email address first.',
                'email_verified' => false,
            ], 403);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $this->getUserData($user),
            'token' => $token,
        ]);
    }

    /**
     * Super Admin login.
     */
    public function superAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'super_admin')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid super admin credentials.'],
            ]);
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('super-admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Super admin login successful',
            'user' => $this->getUserData($user),
            'token' => $token,
        ]);
    }

    /**
     * ISP Admin login.
     */
    public function ispAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'isp_admin')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid ISP admin credentials.'],
            ]);
        }

        // Check if ISP is active
        $ispAdmin = $user->userable;
        if (!$ispAdmin->isp->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your ISP account has been suspended.'],
            ]);
        }

        // Check if ISP has active subscription
        if (!$ispAdmin->isp->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Your ISP subscription has expired. Please renew to continue.',
                'subscription_expired' => true,
            ], 403);
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('isp-admin-token')->plainTextToken;

        return response()->json([
            'message' => 'ISP admin login successful',
            'user' => $this->getUserData($user),
            'isp' => $ispAdmin->isp,
            'token' => $token,
        ]);
    }

    /**
     * Technician login.
     */
    public function technicianLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'technician')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid technician credentials.'],
            ]);
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('technician-token')->plainTextToken;

        return response()->json([
            'message' => 'Technician login successful',
            'user' => $this->getUserData($user),
            'token' => $token,
        ]);
    }

    /**
     * Customer login.
     */
    public function customerLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'customer')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid customer credentials.'],
            ]);
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Customer login successful',
            'user' => $this->getUserData($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current authenticated user.
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $this->getUserData($request->user()),
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $userable = $user->userable;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'phone']);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $userable->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $this->getUserData($user->fresh()),
        ]);
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Revoke all tokens except current
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Forgot password - send reset link.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Don't reveal if email exists
            return response()->json([
                'message' => 'If the email exists, a password reset link has been sent.',
            ]);
        }

        // Generate password reset token
        $token = app('auth.password.broker')->createToken($user);

        // Send email (implement email service)
        // EmailService::sendPasswordResetEmail($user, $token);

        return response()->json([
            'message' => 'Password reset link has been sent to your email.',
        ]);
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email address.'],
            ]);
        }

        // Verify token
        if (!app('auth.password.broker')->tokenExists($user, $request->token)) {
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired reset token.'],
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete all tokens
        $user->tokens()->delete();

        // Delete reset token
        app('auth.password.broker')->deleteToken($user);

        return response()->json([
            'message' => 'Password has been reset successfully.',
        ]);
    }

    /**
     * Get formatted user data.
     */
    protected function getUserData(User $user)
    {
        $data = [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
            'email_verified_at' => $user->email_verified_at,
            'last_login_at' => $user->last_login_at,
        ];

        // Load userable data
        if ($user->userable) {
            $data = array_merge($data, $user->userable->toArray());
        }

        // Add ISP data for non-super-admin users
        if (!$user->isSuperAdmin()) {
            $isp = $user->getISP();
            if ($isp) {
                $data['isp'] = [
                    'id' => $isp->id,
                    'name' => $isp->name,
                    'logo' => $isp->logo,
                    'subscription_status' => $isp->subscription_status,
                ];
            }
        }

        return $data;
    }
}
