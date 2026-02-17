<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 40px;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #007bff;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .invoice-info {
            margin-bottom: 30px;
        }
        .invoice-info table {
            width: 100%;
        }
        .invoice-info td {
            padding: 5px 0;
        }
        .invoice-info .label {
            font-weight: bold;
            width: 150px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.items th,
        table.items td {
            border: 1px solid #ddd;
            padding: 12px 8px;
            text-align: left;
        }
        table.items th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        table.items tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            text-align: right;
            font-weight: bold;
        }
        .grand-total {
            background-color: #007bff !important;
            color: white !important;
            font-size: 16px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-paid {
            background-color: #28a745;
            color: white;
        }
        .status-unpaid {
            background-color: #ffc107;
            color: #000;
        }
        .status-cancelled {
            background-color: #dc3545;
            color: white;
        }
        .status-partial {
            background-color: #17a2b8;
            color: white;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $isp->name }}</h1>
        <p>{{ $isp->email ?? '' }}</p>
        <p>{{ $isp->phone ?? '' }}</p>
    </div>
    
    <div class="invoice-info">
        <table>
            <tr>
                <td class="label">Invoice Number:</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td class="label">Status:</td>
                <td>
                    <span class="status status-{{ $invoice->payment_status }}">
                        {{ strtoupper($invoice->payment_status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label">Invoice Date:</td>
                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date ?? $invoice->created_at)->format('d M Y') }}</td>
                <td class="label">Due Date:</td>
                <td>{{ \Carbon\Carbon::parse($invoice->due_date ?? $invoice->created_at)->format('d M Y') }}</td>
            </tr>
            @if($invoice->payment_status === 'paid' && $invoice->paid_at)
            <tr>
                <td class="label">Paid Date:</td>
                <td>{{ \Carbon\Carbon::parse($invoice->paid_at)->format('d M Y H:i') }}</td>
                <td class="label">Payment Method:</td>
                <td>{{ $invoice->payment_method ?? '-' }}</td>
            </tr>
            @endif
        </table>
    </div>
    
    <table class="items">
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th style="width: 15%; text-align: center;">Quantity</th>
                <th style="width: 17.5%; text-align: right;">Unit Price</th>
                <th style="width: 17.5%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($invoice->items && count($invoice->items) > 0)
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td>Subscription Payment</td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: right;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total-row">Subtotal:</td>
                <td class="text-right">Rp {{ number_format($invoice->subtotal ?? $invoice->total, 0, ',', '.') }}</td>
            </tr>
            @if(($invoice->tax ?? 0) > 0)
            <tr>
                <td colspan="3" class="total-row">Tax:</td>
                <td class="text-right">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if(($invoice->discount ?? 0) > 0)
            <tr>
                <td colspan="3" class="total-row">Discount:</td>
                <td class="text-right">-Rp {{ number_format($invoice->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td colspan="3" class="total-row">TOTAL:</td>
                <td class="text-right"><strong>Rp {{ number_format($invoice->total, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p><em>Thank you for your business!</em></p>
        <p>This is a computer-generated invoice and does not require a signature.</p>
        <p>Generated on {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
    </div>
</body>
</html>
