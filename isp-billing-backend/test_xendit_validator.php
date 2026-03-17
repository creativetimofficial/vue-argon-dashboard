<?php
// Test Xendit Bank Validator

$apiKey = 'xnd_development_Gfx5qNObPoyHkO5pU5GjX8iM6rC2D8zSMyKz9xH54RIVnCWeLp7YpWJZZoTR3n';
$bankCode = 'BRI';
$accountNumber = '1234567890'; // Dummy BRI number

$ch = curl_init('https://api.xendit.co/bank_account_data_requests');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($apiKey . ':')
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'bank_account_number' => $accountNumber,
    'bank_code' => $bankCode
]));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";
