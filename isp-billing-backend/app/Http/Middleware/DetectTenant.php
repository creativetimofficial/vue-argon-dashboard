<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ISP;
use Symfony\Component\HttpFoundation\Response;

class DetectTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Skip tenant detection for main domains (Super Admin / Main App)
        $mainDomains = [
            'localhost',
            '127.0.0.1',
            config('app.url') ? parse_url(config('app.url'), PHP_URL_HOST) : null,
        ];
        
        if (in_array($host, array_filter($mainDomains))) {
            return $next($request);
        }
        
        // Extract subdomain or use full domain
        $parts = explode('.', $host);
        
        // Check if it's a subdomain (e.g., isp1.localhost or isp1.yourdomain.com)
        if (count($parts) >= 3) {
            $subdomain = $parts[0];
            
            // Find ISP by subdomain
            $isp = ISP::where('subdomain', $subdomain)
                ->where('is_active', true)
                ->first();
            
            if ($isp) {
                // Set tenant context
                app()->instance('current_isp', $isp);
                session(['tenant_isp_id' => $isp->id]);
                session(['tenant_isp_name' => $isp->company_name]);
                
                return $next($request);
            }
        }
        
        // Check if it's a custom domain
        // In development/local, allow unverified domains for testing
        $requireVerification = config('app.env') === 'production';
        
        $query = ISP::where('custom_domain', $host)
            ->where('is_active', true);
        
        if ($requireVerification) {
            $query->where('custom_domain_verified', true);
        }
        
        $isp = $query->first();
        
        if ($isp) {
            // Set tenant context
            app()->instance('current_isp', $isp);
            session(['tenant_isp_id' => $isp->id]);
            session(['tenant_isp_name' => $isp->company_name]);
            
            return $next($request);
        }
        
        // No tenant found - return error or redirect
        abort(404, 'ISP not found or inactive');
    }
}
