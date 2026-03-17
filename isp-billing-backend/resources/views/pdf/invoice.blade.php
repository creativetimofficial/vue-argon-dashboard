<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .company-info h2 { margin: 0; color: #696cff; }
        .invoice-details { text-align: right; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .details-table th { background: #f8f9fa; text-align: left; padding: 10px; border-bottom: 2px solid #eee; }
        .details-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .totals { margin-top: 30px; text-align: right; }
        .total-row { display: flex; justify-content: flex-end; margin-bottom: 5px; }
        .total-label { font-weight: bold; width: 120px; display: inline-block; }
        .grand-total { font-size: 1.2em; color: #696cff; margin-top: 10px; border-top: 2px solid #eee; padding-top: 10px; }
        .footer { margin-top: 50px; text-align: center; font-size: 0.8em; color: #777; border-top: 1px solid #eee; padding-top: 20px; }
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 0.8em; text-transform: uppercase; }
        .badge-paid { background: #e8fadf; color: #71dd37; }
        .badge-unpaid { background: #ffe5e5; color: #ff3e1d; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table width="100%">
            <tr>
                <td style="vertical-align: top;">
                    <div class="company-info">
                        <h2>{{ $invoice->isp->company_name }}</h2>
                        <p>
                            {{ $invoice->isp->address }}<br>
                            {{ $invoice->isp->city }}, {{ $isp->province }}<br>
                            Telp: {{ $invoice->isp->phone }}<br>
                            Email: {{ $invoice->isp->email }}
                        </p>
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div class="invoice-details">
                        <h1>INVOICE</h1>
                        <p>
                            <strong>No:</strong> {{ $invoice->invoice_number }}<br>
                            <strong>Tanggal:</strong> {{ $invoice->issue_date->format('d M Y') }}<br>
                            <strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d M Y') }}<br>
                            <span class="badge {{ $invoice->payment_status === 'paid' ? 'badge-paid' : 'badge-unpaid' }}">
                                {{ $invoice->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
                            </span>
                        </p>
                    </div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 40px;">
            <p><strong>Ditujukan Kepada:</strong></p>
            <p>
                {{ $invoice->billable->name }}<br>
                {{ $invoice->billable->address }}<br>
                Telp: {{ $invoice->billable->phone }}
            </p>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th style="text-align: center; width: 80px;">Qty</th>
                    <th style="text-align: right; width: 150px;">Harga Satuan</th>
                    <th style="text-align: right; width: 150px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($invoice->tax > 0)
            <div class="total-row">
                <span class="total-label">PPN (11%):</span>
                <span>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span class="total-label">Total Tagihan:</span>
                <span style="font-weight: bold;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda menggunakan layanan kami.</p>
            <p>Harap melakukan pembayaran sebelum tanggal jatuh tempo untuk menghindari gangguan layanan.</p>
        </div>
    </div>
</body>
</html>
