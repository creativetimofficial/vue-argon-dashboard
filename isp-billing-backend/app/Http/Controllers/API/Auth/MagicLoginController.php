<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLoginController extends Controller
{
    /**
     * Handle the magic link login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request, $userId)
    {
        // 1. Verify the signature
        if (!$request->hasValidSignature()) {
            \Illuminate\Support\Facades\Log::warning("MagicLogin: Invalid signature for user ID: {$userId}");
            return response()->json(['message' => 'Invalid or expired magic link.'], 403);
        }

        // 2. Find the user
        $user = User::findOrFail($userId);
        \Illuminate\Support\Facades\Log::info("MagicLogin: User found: {$user->email} (ID: {$user->id})");

        // 3. Check if user is active/allowed
        if (!$user->is_active) {
            \Illuminate\Support\Facades\Log::warning("MagicLogin: Inactive user tried to access: {$user->email}");
            return response()->json(['message' => 'Account is inactive.'], 403);
        }

        // 4. Generate a fresh API Token
        // Revoke old tokens to prevent bloat and potential conflicts
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        \Illuminate\Support\Facades\Log::info("MagicLogin: Token generated for {$user->email}");

        // 5. Determine Redirect Path
        $redirectPath = $request->query('redirect', '/client-area/dashboard');
        
        // 6. Construct Frontend Redirect URL
        $frontendUrl = config('app.frontend_url', 'http://localhost:8080');
        
        $frontendUrl = rtrim($frontendUrl, '/');
        $redirectPath = ltrim($redirectPath, '/');
        
        $targetUrl = "{$frontendUrl}/magic-login?token={$token}&redirect=/{$redirectPath}";
        
        \Illuminate\Support\Facades\Log::info("MagicLogin: Redirecting to {$targetUrl}");

        return redirect($targetUrl);
    }
}
