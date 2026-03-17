<?php
$client = new \GuzzleHttp\Client();
$response = $client->post('http://127.0.0.1:8000/api/callback/xendit/disbursement', [
    'json' => [
        'id' => 'disb_1234567890',
        'status' => 'COMPLETED',
        'external_id' => 'WD-1-1234567890',
        'amount' => 1000000
    ]
]);
echo $response->getStatusCode() . "\n";
echo $response->getBody() . "\n";
