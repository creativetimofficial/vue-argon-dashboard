<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISPService; // Ensure the model class name matches the migration 'isp_services'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of services for the ISP.
     */
    public function index()
    {
        $ispId = auth()->user()->isp_id;
        $services = \DB::table('isp_services')->where('isp_id', $ispId)->get();
        return response()->json($services);
    }

    /**
     * Store a new service.
     */
    public function store(Request $request)
    {
        $ispId = auth()->user()->isp_id;
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'billing_cycle' => 'required|in:daily,weekly,monthly,quarterly,semi_annual,annual',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $id = \DB::table('isp_services')->insertGetId([
            'isp_id' => $ispId,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Service created successfully', 'id' => $id]);
    }

    public function show($id)
    {
        $ispId = auth()->user()->isp_id;
        $service = \DB::table('isp_services')->where('isp_id', $ispId)->where('id', $id)->first();
        if (!$service) return response()->json(['message' => 'Not found'], 404);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $ispId = auth()->user()->isp_id;
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'billing_cycle' => 'required|in:daily,weekly,monthly,quarterly,semi_annual,annual',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        \DB::table('isp_services')->where('isp_id', $ispId)->where('id', $id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Service updated successfully']);
    }

    public function destroy($id)
    {
        $ispId = auth()->user()->isp_id;
        \DB::table('isp_services')->where('isp_id', $ispId)->where('id', $id)->delete();
        return response()->json(['message' => 'Service deleted successfully']);
    }
}
