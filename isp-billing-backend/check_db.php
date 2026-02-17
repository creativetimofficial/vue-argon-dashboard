$latestPayment = \App\Models\Payment::where('transaction_id', 'like', 'TOP-%')->latest()->first();

$output = "";
if ($latestPayment) {
    $output .= "LATEST_ID:" . $latestPayment->transaction_id . "\n";
    $output .= "STATUS:" . $latestPayment->status . "\n";
    $output .= "AMOUNT:" . $latestPayment->amount . "\n";
} else {
    $output .= "NO_PAYMENTS_FOUND\n";
}

file_put_contents('latest_transaction.txt', $output);

