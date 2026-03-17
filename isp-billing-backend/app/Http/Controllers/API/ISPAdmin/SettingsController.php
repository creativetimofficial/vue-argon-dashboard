<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'logo' => 'nullable|string', // Base64 or URL
            'favicon' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update User info
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        // Update ISP info
        if ($isp) {
            $ispData = [
                'company_name' => $request->company_name,
                'address' => $request->address,
            ];

            // Only allow branding updates if whitelabel feature is active
            $package = $isp->subscriptionPackage;
            if ($package && $package->feature_whitelabel) {
                if ($request->has('logo')) $ispData['logo'] = $request->logo;
                if ($request->has('favicon')) $ispData['favicon'] = $request->favicon;
            }

            $isp->update($ispData);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
            'isp' => $isp->fresh()
        ]);
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    /**
     * Get the ISP's notification settings.
     */
    public function getNotifications()
    {
        $isp = auth()->user()->isp;
        return response()->json([
            'settings' => $isp->notification_settings ?? [
                'new_order' => true,
                'payment' => true
            ],
            'wa_gateway_url' => $isp->wa_gateway_url,
            // Don't return the full API key for security, just a placeholder if it exists
            'has_wa_key' => !empty($isp->wa_api_key)
        ]);
    }

    /**
     * Update the ISP's notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $isp = auth()->user()->isp;
        $package = $isp->subscriptionPackage;

        $validator = Validator::make($request->all(), [
            'new_order' => 'boolean',
            'payment' => 'boolean',
            'wa_gateway_url' => 'nullable|url',
            'wa_api_key' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $settings = $isp->notification_settings ?? [];
        if ($request->has('new_order')) $settings['new_order'] = $request->new_order;
        if ($request->has('payment')) $settings['payment'] = $request->payment;

        $ispData = ['notification_settings' => $settings];

        // Only allow WA settings if feature is active
        if ($package && $package->feature_whatsapp_gateway) {
            if ($request->has('wa_gateway_url')) $ispData['wa_gateway_url'] = $request->wa_gateway_url;
            if ($request->has('wa_api_key')) $ispData['wa_api_key'] = $request->wa_api_key;
        }

        $isp->update($ispData);

        return response()->json(['message' => 'Notification settings updated successfully']);
    }
}
