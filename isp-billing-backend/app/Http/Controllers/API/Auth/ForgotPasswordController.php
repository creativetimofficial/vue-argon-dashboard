<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ResetPasswordCodeMail;

class ForgotPasswordController extends Controller
{
    /**
     * Step 1: Send Reset Code (OTP) to Email
     */
    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan.',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $code = strtoupper(Str::random(6)); // Generate 6-char random code (alphanumeric for better security)
        // Or numeric only: rand(100000, 999999); 
        // User requested "Code Verifikasi" usually digits. Let's use digits.
        $code = rand(100000, 999999);

        // Store code in DB
        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $email],
            [
                'code' => $code,
                'created_at' => Carbon::now()
            ]
        );

        // Send Email
        try {
            Mail::to($email)->send(new ResetPasswordCodeMail($code));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email. Silakan coba lagi nanti.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode verifikasi telah dikirim ke email Anda.'
        ]);
    }

    /**
     * Step 2: Verify Code (Optional, usually done with reset)
     * Useful for UI to validate code before showing password fields
     */
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid data'], 422);
        }

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        // Check if code exists and is not expired (15 mins)
        if (!$record || Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi salah atau sudah kadaluwarsa.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode verifikasi valid.'
        ]);
    }

    /**
     * Step 3: Reset Password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 1. Verify Code Again
        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$record || Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi salah atau sudah kadaluwarsa.'
            ], 400);
        }

        // 2. Get user and check if new password is same as old password
        $user = User::where('email', $request->email)->first();
        
        // Check if new password is same as current password
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password baru tidak boleh sama dengan password lama. Silakan gunakan password yang berbeda.'
            ], 400);
        }
        
        // Update Password
        $user->password = Hash::make($request->password);
        $user->save();

        // 3. Delete Code
        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset. Silakan login dengan password baru.'
        ]);
    }
}
