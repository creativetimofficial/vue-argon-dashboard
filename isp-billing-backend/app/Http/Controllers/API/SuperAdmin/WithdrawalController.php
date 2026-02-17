<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * List withdrawals
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with('isp');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($withdrawals);
    }

    /**
     * Approve withdrawal
     */
    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return response()->json(['message' => 'Withdrawal status is not pending'], 400);
        }

        $withdrawal->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Note: Balance was already deducted at request time, so we just confirm it here.
        
        return response()->json(['message' => 'Withdrawal approved successfully']);
    }

    /**
     * Reject withdrawal
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string'
        ]);

        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return response()->json(['message' => 'Withdrawal status is not pending'], 400);
        }

        try {
            DB::beginTransaction();

            // Refund balance
            $withdrawal->isp->increment('balance', $withdrawal->amount);

            $withdrawal->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'notes' => $withdrawal->notes . "\nRejected Reason: " . $validated['reason']
            ]);

            DB::commit();
            return response()->json(['message' => 'Withdrawal rejected and balance refunded']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to reject: ' . $e->getMessage()], 500);
        }
    }
}
