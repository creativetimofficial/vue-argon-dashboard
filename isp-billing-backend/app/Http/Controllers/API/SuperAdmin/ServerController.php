<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $servers = Server::orderBy('created_at', 'desc')->get();
            return response()->json($servers);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Server Fetch Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'domain' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'api_port' => 'nullable|integer',
            'vpn_local_address' => 'nullable|ip',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $server = Server::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Server created successfully',
            'data' => $server
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $server = Server::findOrFail($id);
        return response()->json($server);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $server = Server::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'ip_address' => 'sometimes|required|ip',
            'domain' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'api_port' => 'nullable|integer',
            'vpn_local_address' => 'nullable|ip',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $server->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Server updated successfully',
            'data' => $server
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $server = Server::findOrFail($id);
        $server->delete();

        return response()->json([
            'success' => true,
            'message' => 'Server deleted successfully'
        ]);
    }

    /**
     * Test connection to Mikrotik Server
     */
    public function checkConnection($id)
    {
        $server = Server::findOrFail($id);
        $mikrotik = new \App\Services\MikrotikService();
        
        try {
            $apiUser = $server->username ?? env('MIKROTIK_USER', 'admin'); 
            $apiPass = $server->password ?? env('MIKROTIK_PASS', '');
            $apiPort = $server->api_port ?? 8728;
            
            if ($mikrotik->connect($server->ip_address, $apiUser, $apiPass, $apiPort)) {
                $mikrotik->disconnect();
                return response()->json([
                    'success' => true,
                    'message' => 'Connection Successful!',
                ]);
            } else {
                 return response()->json([
                    'success' => false,
                    'message' => 'Connection Failed: Could not authenticate or reach host.',
                ], 400);
            }
        } catch (\Exception $e) {
             return response()->json([
                'success' => false,
                'message' => 'Connection Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manually Create VPN Account (Test/Provisioning)
     */
    public function createVpnAccount(Request $request, $id)
    {
        $server = Server::findOrFail($id);
        
        $validated = $request->validate([
             'username' => 'required|string|min:3',
             'password' => 'required|string|min:3',
             'local_address' => 'nullable|ip',
             'remote_address' => 'nullable|ip',
        ]);
        
        $mikrotik = new \App\Services\MikrotikService();
        
        try {
            $apiUser = $server->username ?? env('MIKROTIK_USER', 'admin'); 
            $apiPass = $server->password ?? env('MIKROTIK_PASS', '');
            $apiPort = $server->api_port ?? 8728;
            
            if ($mikrotik->connect($server->ip_address, $apiUser, $apiPass, $apiPort)) {
                
                // Add Secret
                $success = $mikrotik->addPppSecret(
                    $validated['username'], 
                    $validated['password'], 
                    'any', 
                    'default', 
                    $validated['local_address'], 
                    $validated['remote_address']
                );
                
                $mikrotik->disconnect();
                
                if ($success) {
                    return response()->json([
                        'success' => true,
                        'message' => 'VPN Account Created Successfully on MikroTik!',
                    ]); 
                } else {
                     return response()->json([
                        'success' => false,
                        'message' => 'Failed to create PPP Secret (API returned trap/error).',
                    ], 400); 
                }
            } else {
                 return response()->json([
                    'success' => false,
                    'message' => 'Connection Failed: Could not reach host.',
                ], 400);
            }
        } catch (\Exception $e) {
             return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
