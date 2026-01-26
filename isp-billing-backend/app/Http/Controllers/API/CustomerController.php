<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $ispAdmin = $request->user()->userable;
            $isp = $ispAdmin->isp;

            $query = Customer::with(['user', 'activeSubscription'])
                ->where('isp_id', $isp->id);

            if ($request->has('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $perPage = $request->get('per_page', 15);
            $customers = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $customers,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $ispAdmin = $request->user()->userable;
            $isp = $ispAdmin->isp;

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'pppoe_username' => 'nullable|string',
                'pppoe_password' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'customer',
                'is_active' => true,
            ]);

            $customer = Customer::create([
                'user_id' => $user->id,
                'isp_id' => $isp->id,
                'address' => $request->address,
                'installation_address' => $request->installation_address ?? $request->address,
                'pppoe_username' => $request->pppoe_username,
                'pppoe_password' => $request->pppoe_password,
                'status' => 'pending',
                'balance' => 0,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer->load('user'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $ispAdmin = $request->user()->userable;
            $isp = $ispAdmin->isp;

            $customer = Customer::with(['user', 'activeSubscription'])
                ->where('isp_id', $isp->id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $customer,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ispAdmin = $request->user()->userable;
            $isp = $ispAdmin->isp;

            $customer = Customer::where('isp_id', $isp->id)->findOrFail($id);

            DB::beginTransaction();

            if ($request->has('name') || $request->has('email') || $request->has('phone')) {
                $customer->user->update($request->only(['name', 'email', 'phone']));
            }

            $customer->update($request->only([
                'address',
                'installation_address',
                'pppoe_username',
                'pppoe_password',
                'status',
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer->load('user'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $ispAdmin = $request->user()->userable;
            $isp = $ispAdmin->isp;

            $customer = Customer::where('isp_id', $isp->id)->findOrFail($id);

            DB::beginTransaction();
            $customer->user->update(['is_active' => false]);
            $customer->update(['status' => 'inactive']);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer deactivated successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
