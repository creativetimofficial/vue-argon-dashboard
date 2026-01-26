<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        try {
            $gateways = PaymentGateway::orderBy('gateway_name')->get();
            return response()->json($gateways);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching gateways: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'gateway_name' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:payment_gateways,slug',
                'gateway_type' => 'required|string',
                'logo_url' => 'nullable|url',
                'api_key' => 'nullable|string',
                'secret_key' => 'nullable|string',
                'merchant_id' => 'nullable|string',
                'client_id' => 'nullable|string',
                'supported_countries' => 'nullable|array',
                'transaction_fee' => 'nullable|numeric|min:0',
                'fixed_fee' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'webhook_secret' => 'nullable|string',
                'webhook_url' => 'nullable|url',
                'is_active' => 'boolean',
                'sandbox_mode' => 'boolean',
                'settings' => 'nullable|array',
            ]);

            if (empty($validated['slug'])) {
                $validated['slug'] = \Illuminate\Support\Str::slug($validated['gateway_name']);
            }

            $gateway = PaymentGateway::create($validated);
            return response()->json($gateway, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error saving gateway: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function show($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        return response()->json($gateway);
    }

    public function update(Request $request, $id)
    {
        try {
            $gateway = PaymentGateway::findOrFail($id);
            
            $validated = $request->validate([
                'gateway_name' => 'sometimes|string|max:255',
                'slug' => 'sometimes|string|unique:payment_gateways,slug,' . $id,
                'gateway_type' => 'sometimes|string',
                'logo_url' => 'nullable|url',
                'api_key' => 'nullable|string',
                'secret_key' => 'nullable|string',
                'merchant_id' => 'nullable|string',
                'client_id' => 'nullable|string',
                'supported_countries' => 'nullable|array',
                'transaction_fee' => 'nullable|numeric|min:0',
                'fixed_fee' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'webhook_secret' => 'nullable|string',
                'webhook_url' => 'nullable|url',
                'is_active' => 'boolean',
                'sandbox_mode' => 'boolean',
                'settings' => 'nullable|array',
            ]);

            // Only update secret_key if a new value is provided
            // This prevents overwriting with empty string during edits
            if (isset($validated['secret_key']) && empty($validated['secret_key'])) {
                unset($validated['secret_key']);
            }
            
            // Same for other sensitive fields
            if (isset($validated['webhook_secret']) && empty($validated['webhook_secret'])) {
                unset($validated['webhook_secret']);
            }

            $gateway->update($validated);
            
            // Return gateway with secret_key visible for this response only
            $gatewayData = $gateway->toArray();
            $gatewayData['secret_key'] = $gateway->secret_key ? '••••••••' . substr($gateway->secret_key, -4) : null;
            
            return response()->json($gatewayData);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating gateway: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->delete();
        return response()->json(['message' => 'Payment gateway deleted successfully']);
    }
}
