<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ISPOrder;
use App\Models\ISP;
use Illuminate\Support\Facades\Config;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Skip for main domains or localhost (Super Admin access)
        // You can configure these in .env
        $mainDomains = [
            'localhost',
            '127.0.0.1',
            'paynet.id',
            'www.paynet.id',
            env('APP_URL') ? parse_url(env('APP_URL'), PHP_URL_HOST) : null
        ];

        if (in_array($host, array_filter($mainDomains))) {
            return $next($request);
        }

        // Try to find ISP by domain or subdomain in active orders
        // We look for ACTIVE or PAID orders that have this domain/subdomain
        $order = ISPOrder::where(function ($query) use ($host) {
                $query->where('domain', $host)
                      ->orWhereRaw("CONCAT(subdomain, '.', ?) = ?", [request()->getHost(), $host]); // Simplified check
            })
            ->whereIn('status', ['active', 'paid'])
            ->with('isp')
            ->first();
            
        // Alternative: simpler check if we assume 'domain' column stores the full FQDN
        if (!$order) {
             $order = ISPOrder::where('domain', $host)
                ->whereIn('status', ['active', 'paid'])
                ->with('isp')
                ->first();
        }

        if ($order && $order->isp) {
            // Bind the current ISP to the container for easy access
            app()->instance('current_isp', $order->isp);
            
            // Optional: config runtime changes if needed
            // Config::set('app.name', $order->isp->company_name);
        }

        return $next($request);
    }
}
