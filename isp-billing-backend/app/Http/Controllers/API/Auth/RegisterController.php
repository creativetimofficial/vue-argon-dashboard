<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ISPVerificationEmail;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'whatsapp' => 'required|string|min:10|max:15',
            'isp_name' => 'nullable|string|max:255', // Add ISP Name field
            'recaptcha_token' => 'required|string', 
        ]);

        try {
            // Verify reCAPTCHA
            $recaptchaSecret = config('services.recaptcha.secret_key');
            
            // Skip reCAPTCHA in development if using test keys
            $isTestKey = $recaptchaSecret === '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
            
            if (!$isTestKey) {
                try {
                    $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
                    $recaptchaData = [
                        'secret' => $recaptchaSecret,
                        'response' => $validated['recaptcha_token']
                    ];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $recaptchaUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($recaptchaData));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
                    
                    $recaptchaResponse = curl_exec($ch);
                    $curlError = curl_error($ch);
                    curl_close($ch);
                    
                    if ($curlError) {
                        \Log::error('reCAPTCHA cURL error: ' . $curlError);
                    } else {
                        $recaptchaResult = json_decode($recaptchaResponse);
                        
                        if (!$recaptchaResult->success) {
                            \Log::warning('reCAPTCHA verification failed', [
                                'errors' => $recaptchaResult->{'error-codes'} ?? []
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('reCAPTCHA verification exception: ' . $e->getMessage());
                }
            }

            // Check for tenant to determine registration type
            $host = $request->getHost();
            $isp = ISP::where('subdomain', explode('.', $host)[0])
                      ->orWhere('custom_domain', $host)
                      ->first();

            DB::beginTransaction();

            // Normalize WhatsApp number
            $whatsapp = $validated['whatsapp'];
            if (substr($whatsapp, 0, 1) === '0') {
                $whatsapp = '+62' . substr($whatsapp, 1);
            } elseif (substr($whatsapp, 0, 2) === '62') {
                $whatsapp = '+' . $whatsapp;
            } elseif (substr($whatsapp, 0, 3) !== '+62') {
                $whatsapp = '+62' . $whatsapp;
            }

            if ($isp) {
                // Customer Registration
                $role = 'customer';
                $packageId = $request->input('package_id');
                
                // Create User
                $user = User::create([
                    'name' => explode('@', $validated['email'])[0],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => $role,
                    'isp_id' => $isp->id,
                    'is_active' => false,
                ]);

                // Create Customer Record
                \App\Models\Customer::create([
                    'isp_id' => $isp->id,
                    'customer_code' => 'CST-' . strtoupper(Str::random(8)),
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $whatsapp,
                    'address' => '-',
                    'service_plan_id' => $packageId,
                    'status' => 'active',
                ]);
            } else {
                // ISP Admin Registration - Only create the User, DO NOT create an ISP unit yet.
                $role = 'isp_admin';

                // Create User
                $user = User::create([
                    'name' => explode('@', $validated['email'])[0],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => $role,
                    'phone' => $whatsapp,
                    'company_name' => $validated['isp_name'] ?? null, // Save ISP name entered at registration
                    'is_active' => false,
                    'isp_id' => null, // No primary ISP yet
                ]);
            }

            // Generate verification token
            $token = Str::random(64);
            DB::table('email_verifications')->insert([
                'email' => $user->email,
                'token' => $token,
                'user_id' => $user->id,
                'expires_at' => now()->addHours(24),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send verification email
            try {
                Mail::to($user->email)->send(new ISPVerificationEmail($user, $token));
            } catch (\Exception $e) {
                \Log::error('Failed to send verification email: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'message' => 'Registration successful! Please check your email to verify your account.',
                'user_id' => $user->id,
                'role' => $role,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration error: ' . $e->getMessage());
            \Log::error('Registration error trace: ' . $e->getTraceAsString());
            
            $response = response()->json([
                'message' => 'Registration failed: ' . $e->getMessage(),
                'error' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ] : null
            ], 500);
            
            // Add CORS headers manually if middleware didn't catch it
            $allowedOrigins = [
                'http://localhost:8080',
                'http://127.0.0.1:8080',
                config('app.frontend_url', 'http://localhost:8080'),
            ];
            $origin = request()->headers->get('Origin');
            $allowedOrigin = in_array($origin, $allowedOrigins) ? $origin : ($allowedOrigins[0] ?? '*');
            
            return $response
                ->header('Access-Control-Allow-Origin', $allowedOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
                ->header('Access-Control-Allow-Credentials', 'true');
        }
    }

    public function verifyEmail($token)
    {
        try {
            \Log::info('Verification attempt for token: ' . $token);
            
            $verification = DB::table('email_verifications')
                ->where('token', $token)
                ->where('expires_at', '>', now())
                ->whereNull('verified_at')
                ->first();
            
            \Log::info('Verification result: ' . ($verification ? 'Found' : 'Not found'));

            if (!$verification) {
                // Check if already verified
                $alreadyVerified = DB::table('email_verifications')
                    ->where('token', $token)
                    ->whereNotNull('verified_at')
                    ->first();

                if ($alreadyVerified) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email sudah pernah diverifikasi sebelumnya.'
                    ], 400);
                }

                // Check if expired
                $expired = DB::table('email_verifications')
                    ->where('token', $token)
                    ->where('expires_at', '<=', now())
                    ->first();

                if ($expired) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token verifikasi sudah kadaluarsa. Silakan request verifikasi ulang.'
                    ], 400);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Token verifikasi tidak valid.'
                ], 400);
            }

            // Verify the email and activate user
            $user = User::find($verification->user_id);
            $user->update([
                'email_verified_at' => now(),
                'is_active' => true, // Activate user after email verification
            ]);

            // Also activate ISP if exists and user is an isp_admin
            if ($user->isp_id && $user->role === 'isp_admin') {
                $isp = ISP::find($user->isp_id);
                if ($isp && $isp->approval_status === 'approved') {
                    $isp->update([
                        'is_active' => true, 
                    ]);
                }
            }

            DB::table('email_verifications')
                ->where('token', $token)
                ->update(['verified_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi.'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Email verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi email: ' . $e->getMessage()
            ], 500);
        }
    }
}
