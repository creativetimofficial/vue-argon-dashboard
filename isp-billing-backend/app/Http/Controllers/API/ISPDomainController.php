<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ISPDomainController extends Controller
{
    /**
     * Update custom domain for ISP
     */
    public function updateCustomDomain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_domain' => 'required|string|max:255|unique:isps,custom_domain',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $ispId = session('tenant_isp_id');
        
        if (!$ispId) {
            return response()->json([
                'success' => false,
                'message' => 'ISP tidak ditemukan'
            ], 404);
        }

        $isp = ISP::find($ispId);

        if (!$isp) {
            return response()->json([
                'success' => false,
                'message' => 'ISP tidak ditemukan'
            ], 404);
        }

        // Update custom domain (needs verification)
        $isp->update([
            'custom_domain' => $request->custom_domain,
            'custom_domain_verified' => false, // Needs DNS verification
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom domain berhasil ditambahkan. Silakan verifikasi DNS Anda.',
            'data' => [
                'custom_domain' => $isp->custom_domain,
                'verified' => $isp->custom_domain_verified,
                'dns_instructions' => [
                    'type' => 'A',
                    'name' => '@',
                    'value' => 'YOUR_SERVER_IP', // Replace with actual server IP
                    'ttl' => 3600
                ]
            ]
        ]);
    }

    /**
     * Verify custom domain
     */
    public function verifyCustomDomain(Request $request)
    {
        $ispId = session('tenant_isp_id');
        
        if (!$ispId) {
            return response()->json([
                'success' => false,
                'message' => 'ISP tidak ditemukan'
            ], 404);
        }

        $isp = ISP::find($ispId);

        if (!$isp || !$isp->custom_domain) {
            return response()->json([
                'success' => false,
                'message' => 'Custom domain tidak ditemukan'
            ], 404);
        }

        // Simple DNS verification (check if domain points to server)
        $serverIp = $request->server('SERVER_ADDR');
        $domainIp = gethostbyname($isp->custom_domain);

        if ($domainIp === $serverIp) {
            $isp->update(['custom_domain_verified' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Custom domain berhasil diverifikasi!',
                'data' => [
                    'custom_domain' => $isp->custom_domain,
                    'verified' => true
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Verifikasi gagal. Pastikan DNS sudah dikonfigurasi dengan benar.',
            'data' => [
                'expected_ip' => $serverIp,
                'current_ip' => $domainIp
            ]
        ], 400);
    }

    /**
     * Get ISP domain info
     */
    public function getDomainInfo()
    {
        $ispId = session('tenant_isp_id');
        
        if (!$ispId) {
            return response()->json([
                'success' => false,
                'message' => 'ISP tidak ditemukan'
            ], 404);
        }

        $isp = ISP::find($ispId);

        if (!$isp) {
            return response()->json([
                'success' => false,
                'message' => 'ISP tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'subdomain' => $isp->subdomain,
                'custom_domain' => $isp->custom_domain,
                'custom_domain_verified' => $isp->custom_domain_verified,
                'full_subdomain_url' => $isp->subdomain ? "https://{$isp->subdomain}.yourdomain.com" : null,
                'full_custom_url' => $isp->custom_domain ? "https://{$isp->custom_domain}" : null,
            ]
        ]);
    }
}
