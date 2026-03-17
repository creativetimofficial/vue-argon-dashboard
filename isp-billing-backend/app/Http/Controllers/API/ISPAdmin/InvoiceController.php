<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices for the ISP.
     */
    public function index(Request $request)
    {
        $ispId = Auth::user()->isp_id;
        
        $query = Invoice::where('isp_id', $ispId)
            ->with(['billable'])
            ->orderBy('issue_date', 'desc');

        if ($request->search) {
            $query->where('invoice_number', 'like', "%{$request->search}%")
                  ->orWhereHas('billable', function($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%");
                  });
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        return response()->json($query->paginate(10));
    }

    /**
     * Display the specified invoice.
     */
    public function show($id)
    {
        $ispId = Auth::user()->isp_id;
        $invoice = Invoice::where('isp_id', $ispId)
            ->with(['items', 'billable', 'isp'])
            ->findOrFail($id);

        return response()->json($invoice);
    }

    /**
     * Generate and download PDF invoice.
     */
    public function downloadPDF($id)
    {
        $ispId = Auth::user()->isp_id;
        $invoice = Invoice::where('isp_id', $ispId)
            ->with(['items', 'billable', 'isp'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'isp' => $invoice->isp
        ]);

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Mark invoice as paid (Manual Payment).
     */
    public function markAsPaid($id)
    {
        $ispId = Auth::user()->isp_id;
        $invoice = Invoice::where('isp_id', $ispId)->findOrFail($id);

        if ($invoice->payment_status === 'paid') {
            return response()->json(['message' => 'Invoice already paid.'], 400);
        }

        $invoice->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'paid_amount' => $invoice->total,
            'status' => 'completed'
        ]);

        return response()->json([
            'message' => 'Invoice marked as paid successfully.',
            'invoice' => $invoice
        ]);
    }
}
