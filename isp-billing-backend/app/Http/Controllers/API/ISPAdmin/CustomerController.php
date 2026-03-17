<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ISP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers for the current ISP.
     */
    public function index(Request $request)
    {
        $isp = auth()->user()->isp;
        if (!$isp) {
            return response()->json(['message' => 'ISP not found for this user.'], 404);
        }

        $query = Customer::where('isp_id', $isp->id);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 10);

        return response()->json($customers);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $isp = auth()->user()->isp;
        if (!$isp) {
            return response()->json(['message' => 'ISP not found.'], 404);
        }

        // Check package limits
        $package = $isp->subscriptionPackage;
        if ($package) {
            $currentCount = Customer::where('isp_id', $isp->id)->count();
            if ($currentCount >= $package->max_customers) {
                return response()->json([
                    'message' => "Batas jumlah pelanggan untuk paket {$package->name} telah tercapai ({$package->max_customers}). Silakan upgrade paket Anda."
                ], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'customer_code' => 'nullable|string|max:50|unique:customers,customer_code',
            'service_plan_id' => 'nullable|exists:isp_services,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'ont_serial_number' => 'nullable|string|max:100',
            'ip_address' => 'nullable|string|max:45',
            'mac_address' => 'nullable|string|max:100',
            'mikrotik_username' => 'nullable|string|max:100',
            'mikrotik_password' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:50',
            'id_type' => 'nullable|string|max:20',
            'installation_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'installation_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['isp_id'] = $isp->id;
        
        // Generate customer code if not provided
        if (empty($data['customer_code'])) {
            $data['customer_code'] = 'CUST-' . strtoupper(Str::random(8));
        }

        $customer = Customer::create($data);

        return response()->json([
            'message' => 'Pelanggan berhasil ditambahkan',
            'customer' => $customer
        ], 21);
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $isp = auth()->user()->isp;
        $customer = Customer::where('isp_id', $isp->id)->findOrFail($id);
        
        return response()->json($customer);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        $isp = auth()->user()->isp;
        $customer = Customer::where('isp_id', $isp->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'customer_code' => 'required|string|max:50|unique:customers,customer_code,' . $id,
            'service_plan_id' => 'nullable|exists:isp_services,id',
            'status' => 'required|in:active,suspended,terminated',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'ont_serial_number' => 'nullable|string|max:100',
            'ip_address' => 'nullable|string|max:45',
            'mac_address' => 'nullable|string|max:100',
            'mikrotik_username' => 'nullable|string|max:100',
            'mikrotik_password' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:50',
            'id_type' => 'nullable|string|max:20',
            'installation_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'installation_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer->update($validator->validated());

        return response()->json([
            'message' => 'Data pelanggan berhasil diperbarui',
            'customer' => $customer
        ]);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy($id)
    {
        $isp = auth()->user()->isp;
        $customer = Customer::where('isp_id', $isp->id)->findOrFail($id);
        
        $customer->delete();

        return response()->json([
            'message' => 'Pelanggan berhasil dihapus'
        ]);
    }

    /**
     * Get geographical data for all customers for the map view.
     */
    public function mapData()
    {
        $isp = auth()->user()->isp;
        if (!$isp) {
            return response()->json(['message' => 'ISP not found.'], 404);
        }

        $customers = Customer::where('isp_id', $isp->id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'name', 'customer_code', 'status', 'latitude', 'longitude', 'service_plan_id')
            ->with(['servicePlan:id,name'])
            ->get();

        return response()->json($customers);
    }
}
