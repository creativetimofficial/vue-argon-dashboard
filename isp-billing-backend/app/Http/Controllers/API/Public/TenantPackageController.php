<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use App\Models\ISPService;
use Illuminate\Http\Request;

class TenantPackageController extends Controller
{
    /**
     * Get public packages for a specific ISP based on subdomain or domain.
     */
    public function getPackages(Request $request)
    {
        $host = $request->getHost();
        
        // Find ISP by subdomain or custom domain
        $isp = ISP::where('subdomain', explode('.', $host)[0])
                  ->orWhere('custom_domain', $host)
                  ->first();

        if (!$isp) {
             // Fallback if no tenant detected, or maybe return empty
            return response()->json([
                'success' => false,
                'message' => 'ISP tidak ditemukan untuk domain ini.'
            ], 404);
        }

        // Return packages (ISPService) associated with this ISP
        $packages = ISPService::where('isp_id', $isp->id)
                             ->where('is_active', true)
                             ->orderBy('sort_order')
                             ->get();

        return response()->json([
            'success' => true,
            'isp' => [
                'id' => $isp->id,
                'name' => $isp->company_name,
                'logo' => $isp->logo,
            ],
            'packages' => $packages
        ]);
    }
}
