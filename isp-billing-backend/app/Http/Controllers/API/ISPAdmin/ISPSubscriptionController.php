<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use App\Models\SubscriptionPackage;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Factories\PaymentGatewayFactory;
use Illuminate\Support\Facades\Mail;
use App\Mail\PackageActive;

class ISPSubscriptionController extends Controller
{
    /**
     * Subscribe to a package
     */
    public function subscribe(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'package_id' => 'required|exists:subscription_packages,id',
            'payment_gateway_id' => 'nullable|exists:payment_gateways,id',
            'is_new_isp' => 'nullable|boolean',
            'company_name' => 'required_if:is_new_isp,true|string|max:255',
            'subdomain' => 'required_if:is_new_isp,true|string|unique:isps,subdomain|max:50',
            'target_isp_id' => 'nullable|exists:isps,id',
        ]);

        // Check Company Name Uniqueness for current owner
        if ($request->is_new_isp) {
            $existingName = ISP::where('owner_id', $user->id)
                ->where('company_name', $validated['company_name'])
                ->exists();
            if ($existingName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki unit ISP dengan nama "' . $validated['company_name'] . '". Silakan gunakan nama lain untuk unit baru ini.'
                ], 422);
            }
        }

        // Determine which ISP we are subscribing for
        if ($request->is_new_isp) {
            $isp = ISP::create([
                'owner_id' => $user->id,
                'company_name' => $validated['company_name'],
                'subdomain' => $validated['subdomain'],
                'email' => $user->email,
                'subscription_status' => 'trial',
                'approval_status' => 'pending',
                'is_active' => false,
            ]);
            // If user has no primary ISP, set this one
            if (!$user->isp_id) {
                $user->update(['isp_id' => $isp->id]);
            }
        } else {
            // Renewing/Updating existing
            $ispId = $validated['target_isp_id'] ?? $user->isp_id;
            $isp = ISP::find($ispId);

            if (!$isp || $isp->owner_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ISP not found or you do not have permission to manage this ISP.'
                ], 403);
            }
        }

        $package = SubscriptionPackage::findOrFail($validated['package_id']);

        if (!$package->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Package is not available'
            ], 400);
        }

        // Check if package is trial (price is 0 or has trial_days)
        $isTrialRequested = $package->price == 0 || ($package->trial_days ?? 0) > 0;

        if ($isTrialRequested) {
            // Check if this ISP has ever used a trial package before
            $hasUsedTrial = \App\Models\ISPOrder::where('isp_id', $isp->id)
                ->where(function($query) {
                    $query->whereHas('subscriptionPackage', function($q) {
                        $q->where('price', 0)->orWhere('trial_days', '>', 0);
                    });
                })->exists();

            if ($hasUsedTrial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit ISP ini sudah pernah menggunakan paket trial. Silakan pilih paket berbayar untuk melanjutkan.'
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            // Check if package is trial
            $isTrial = $isTrialRequested;

            if ($isTrial) {
                if ($package->requires_manual_approval) {
                    // For trial packages that need approval, set approval status to pending
                    $isp->update([
                        'subscription_package_id' => $package->id,
                        'subscription_status' => 'trial',
                        'approval_status' => 'pending',
                        'subscription_start_date' => null,
                        'subscription_end_date' => null,
                    ]);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Trial package request submitted. Waiting for super admin approval.',
                        'requires_approval' => true,
                    ]);
                } else {
                    // Auto-approve trial if not manual
                    $startDate = now();
                    $activeDays = $package->active_days ?? 30;
                    $endDate = $activeDays ? $startDate->copy()->addDays($activeDays) : null;

                    $isp->update([
                        'subscription_package_id' => $package->id,
                        'subscription_status' => 'active',
                        'approval_status' => 'approved',
                        'subscription_start_date' => $startDate,
                        'subscription_end_date' => $endDate,
                        'is_active' => true,
                    ]);

                    // Activate user
                    $isp->users()->update(['is_active' => true]);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Trial package activated successfully.',
                        'requires_approval' => false,
                    ]);
                }
            } else {
                // For paid packages, create payment
                $price = $package->price;

                // Get active payment gateway
                if ($request->has('payment_gateway_id')) {
                    $paymentGateway = PaymentGateway::where('id', $request->payment_gateway_id)
                        ->where('is_active', true)
                        ->first();
                } else {
                    $activeGateways = PaymentGateway::where('is_active', true)->orderBy('sort_order')->get();
                    if ($activeGateways->count() === 1) {
                         $paymentGateway = $activeGateways->first();
                    } else {
                        // If multiple gateways are active and none selected, we can either default or require selection.
                        // For now, let's default to the one marked as 'is_default' or the first one.
                        $paymentGateway = PaymentGateway::where('is_active', true)
                            ->orderBy('is_default', 'desc')
                            ->orderBy('sort_order')
                            ->first();
                    }
                }

                if (!$paymentGateway) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected payment gateway is not available or no payment gateway configured.'
                    ], 400);
                }

                // Create payment transaction
                $paymentUrl = $this->createPayment($isp, $package, $price, 'Once', $paymentGateway);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to payment gateway...',
                    'payment_url' => $paymentUrl,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create payment transaction
     */
    private function createPayment($isp, $package, $amount, $billingCycle, $paymentGateway)
    {
        // Configure Midtrans
        \Midtrans\Config::$serverKey = $paymentGateway->secret_key ?? ($paymentGateway->settings['server_key'] ?? '');
        \Midtrans\Config::$isProduction = $paymentGateway->sandbox_mode ? false : true;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = 'SUB-' . $isp->id . '-' . time();
        
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $isp->name,
                'email' => $isp->email ?? auth()->user()->email,
            ],
            'item_details' => [
                [
                    'id' => $package->id,
                    'price' => (int) $amount,
                    'quantity' => 1,
                    'name' => 'Subscription: ' . $package->name,
                ]
            ],
            'callbacks' => [
                'finish' => config('app.frontend_url', 'http://localhost:8080') . '/payment-callback?status=success&isp_id=' . $isp->id . '&package_id=' . $package->id . '&amount=' . $amount,
                'error' => config('app.frontend_url', 'http://localhost:8080') . '/payment-callback?status=failed',
            ]
        ];

        try {
            $snapResponse = \Midtrans\Snap::createTransaction($params);
            return $snapResponse->redirect_url;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            throw new \Exception('Failed to generate payment URL: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment callback
     */
    /**
     * Handle payment callback (Midtrans)
     */
    public function paymentCallback(Request $request)
    {
        Log::info('Payment Callback Received (Midtrans):', $request->all());

        $validated = $request->validate([
            'order_id' => 'nullable', 
            'status' => 'nullable',
            'transaction_status' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $rawOrderId = $request->order_id;
            $status = $this->normalizeStatus($validated['status'] ?? ($request->status ?? ($request->transaction_status ?? 'pending')));

            // Midtrans Verification Logic
            if ($request->filled('order_id')) {
                 // Default to pending when verifying to prevent spoofing
                 $status = 'pending';
                 
                 try {
                     $activeGateway = \App\Models\PaymentGateway::where('is_active', true)
                        ->where('gateway_name', 'Midtrans')
                        ->first();
                     
                     if ($activeGateway) {
                         $serverKey = $activeGateway->secret_key ?? ($activeGateway->settings['server_key'] ?? '');
                         $isProduction = $activeGateway->sandbox_mode ? false : true;
                         
                         if ($serverKey) {
                             \Midtrans\Config::$serverKey = $serverKey;
                             \Midtrans\Config::$isProduction = $isProduction;
                             
                             $midtransStatus = \Midtrans\Transaction::status($rawOrderId);
                             $realStatus = $midtransStatus->transaction_status;
                             
                             Log::info("Verified Midtrans Status for $rawOrderId: $realStatus");

                             if (in_array($realStatus, ['capture', 'settlement'])) {
                                 $status = 'success';
                             } elseif (in_array($realStatus, ['deny', 'expire', 'cancel', 'failure'])) {
                                 $status = 'failed';
                             } else {
                                 // pending, challenge, etc.
                                 $status = 'pending';
                             }
                         } else {
                             Log::warning("Server Key missing for Midtrans verification. Order: $rawOrderId");
                         }
                     } else {
                         Log::warning("Midtrans Gateway inactive or not found during callback. Order: $rawOrderId");
                     }
                 } catch (\Exception $e) {
                      Log::error("Failed to verify Midtrans status for $rawOrderId: " . $e->getMessage());
                      // Keep status as pending on error, DO NOT trust input
                 }
            }

            if (str_starts_with($rawOrderId, 'TOP-')) {
                if ($status === 'success') {
                    $result = $this->processTopupSuccess($rawOrderId, $request->payment_type ?? 'bank_transfer', $request->gross_amount ?? 0, $request->all());
                    DB::commit();
                    return $result;
                } else {
                     // Update Payment status
                     $payment = \App\Models\Payment::where('transaction_id', $rawOrderId)->first();
                     if ($payment) {
                         $payment->update(['status' => $status === 'failed' ? 'failed' : 'pending']);
                     }
                     DB::commit();
                     return response()->json(['success' => false, 'message' => 'Payment status: ' . $status]);
                }
            }

            $order = $this->findOrderByExternalId($rawOrderId);

            if ($order) {
                if ($status === 'success') {
                    $result = $this->processPaymentSuccess($order, $request->payment_type ?? 'bank_transfer', $rawOrderId, $request->all());
                    DB::commit();
                    return $result;
                } else {
                    $order->update(['payment_status' => $status === 'failed' ? 'failed' : 'pending']);
                    DB::commit();
                    return response()->json(['success' => false, 'message' => 'Payment status: ' . $status]);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Order/Invoice not found for ID: ' . $rawOrderId], 404);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment callback error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle Xendit Callback
     */
    public function callbackXendit(Request $request)
    {
        Log::info('Xendit Callback:', $request->all());
        return $this->handleGenericCallback('Xendit', $request);
    }

    /**
     * Handle Tripay Callback
     */
    public function callbackTripay(Request $request)
    {
        Log::info('Tripay Callback:', $request->all());
        return $this->handleGenericCallback('Tripay', $request);
    }

    /**
     * Handle Duitku Callback
     */
    public function callbackDuitku(Request $request)
    {
        Log::info('Duitku Callback:', $request->all());
        return $this->handleGenericCallback('Duitku', $request);
    }

    /**
     * Generic Handler for Factory-based Gateways
     */
    private function handleGenericCallback($gatewayName, Request $request)
    {
        try {
            DB::beginTransaction();
            
            $gateway = PaymentGatewayFactory::create($gatewayName);
            $result = $gateway->handleCallback($request); 
            // result: ['external_id', 'status', 'payment_method', 'amount', 'raw_response']

            // Check if it's a Topup
            if (str_starts_with($result['external_id'], 'TOP-')) {
                if ($result['status'] === 'paid' || $result['status'] === 'success') {
                    $response = $this->processTopupSuccess(
                        $result['external_id'],
                        $result['payment_method'],
                        $result['amount'],
                        json_decode($result['raw_response'], true) ?? []
                    );
                    DB::commit();
                    return $response;
                } else {
                    // Update Payment status only
                    $payment = \App\Models\Payment::where('transaction_id', $result['external_id'])->first();
                    if ($payment) {
                        $payment->update(['status' => $result['status'] === 'failed' ? 'failed' : 'pending']);
                    }
                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'Updated status to ' . $result['status']]);
                }
            }

            $order = $this->findOrderByExternalId($result['external_id']);

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            if ($result['status'] === 'paid' || $result['status'] === 'success') {
                $response = $this->processPaymentSuccess(
                    $order, 
                    $result['payment_method'], 
                    $result['external_id'], 
                    json_decode($result['raw_response'], true) ?? []
                );
                DB::commit();
                return $response;
            } else {
                $order->update(['payment_status' => $result['status'] === 'failed' ? 'failed' : 'pending']);
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Updated status to ' . $result['status']]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("$gatewayName Callback Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function findOrderByExternalId($rawOrderId)
    {
        // Skip Topups
        if (str_starts_with($rawOrderId, 'TOP-')) return null;

        $order = null;
        if (str_contains($rawOrderId, 'ORD-INV-')) {
            $parts = explode('-', $rawOrderId);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $invoice = \App\Models\Invoice::find($parts[2]);
                if ($invoice) $order = \App\Models\ISPOrder::with(['isp', 'subscriptionPackage', 'service'])->find($invoice->subscription_id);
            }
        } elseif (str_contains($rawOrderId, 'ORD-')) {
            $parts = explode('-', $rawOrderId);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $order = \App\Models\ISPOrder::with(['isp', 'subscriptionPackage', 'service'])->find($parts[1]);
            }
        } elseif (is_numeric($rawOrderId)) {
             $order = \App\Models\ISPOrder::with(['isp', 'subscriptionPackage', 'service'])->find($rawOrderId);
        }
        return $order;
    }

    private function processPaymentSuccess($order, $paymentMethod, $transactionId, $paymentDetails)
    {
        // 1. Update Order Status
        $updateData = [
            'payment_status' => 'paid',
            'status' => 'active', 
            'approved_at' => now(),
        ];

        // Generate Credentials if needed
        $this->generateCredentialsAndProvision($order, $updateData);
        
        $order->update($updateData);

        // 2. Update Invoice
        $invoice = \App\Models\Invoice::where('subscription_id', $order->id)
            ->where('payment_status', '!=', 'paid')
            ->first();
        
        if ($invoice) {
            $invoice->update([
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod,
                'paid_amount' => $order->price,
                'paid_at' => now(),
            ]);

            // Create or Update Payment Record
            $payment = \App\Models\Payment::where('transaction_id', $transactionId)->first();
            
            if ($payment) {
                $payment->update([
                    'status' => 'success',
                    'payment_method' => $paymentMethod,
                    'paid_at' => now(),
                    'payment_details' => json_encode($paymentDetails)
                ]);
            } else {
                \App\Models\Payment::create([
                    'invoice_id' => $invoice->id,
                    'transaction_id' => $transactionId,
                    'payment_gateway' => 'Multi',
                    'payment_method' => $paymentMethod,
                    'amount' => $invoice->total,
                    'status' => 'success',
                    'paid_at' => now(),
                    'payment_details' => json_encode($paymentDetails)
                ]);
            }
        }

        // 3. Subscription/Package Logic
        $order->activateSubscription();

        // 4. Referral Commission Logic
        if ($order->referrer_id && $order->price > 0) {
            $referrer = \App\Models\ISP::find($order->referrer_id);
            if ($referrer) {
                // Commission 20%
                $commission = $order->price * 0.20;
                
                // Credit Balance
                $referrer->increment('balance', $commission);

                // Create Referral Bonus Payment Record
                 \App\Models\Payment::create([
                    'isp_id' => $referrer->id,
                    'invoice_id' => null,
                    'transaction_id' => 'REF-BONUS-' . $order->id . '-' . time(),
                    'payment_reference' => 'Referral Bonus: ' . $order->reference,
                    'amount' => $commission,
                    'payment_method' => 'System',
                    'payment_gateway' => 'Referral',
                    'status' => 'success',
                    'payment_date' => now(),
                    'paid_at' => now(),
                    'notes' => 'Commission from Order #' . $order->id
                ]);
            }
        }

        // Send Email
        try {
            $isp = \App\Models\ISP::find($order->isp_id);
            if ($isp) {
                Mail::to($isp->email)->send(new PackageActive($order));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send active package email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment successful and verified.',
        ]);
    }

    private function processTopupSuccess($transactionId, $paymentMethod, $amount, $paymentDetails)
    {
        Log::info("Processing Topup Success for Transaction: $transactionId");

        $payment = \App\Models\Payment::where('transaction_id', $transactionId)->first();
        
        if ($payment) {
            Log::info("Payment Record Found: " . $payment->id . " Current Status: " . $payment->status);

            if ($payment->status === 'success') {
                 Log::warning("Topup already processed for Transaction: $transactionId");
                 return response()->json(['success' => true, 'message' => 'Topup already processed']);
            }
            
            $payment->update([
                'status' => 'success',
                'payment_method' => $paymentMethod,
                'paid_at' => now(),
                'payment_details' => json_encode($paymentDetails)
            ]);
            
            // Credit Balance
            $isp = \App\Models\ISP::find($payment->isp_id);
            if ($isp) {
                // FORCE cast to float/double before incrementing to be safe
                $creditAmount = (float) $payment->amount;
                Log::info("Crediting Balance for ISP: " . $isp->id . " Amount: " . $creditAmount . " Old Balance: " . $isp->balance);
                
                $isp->increment('balance', $creditAmount);
                
                // Refresh to log new balance
                $isp->refresh(); 
                Log::info("New Balance: " . $isp->balance);
            } else {
                Log::error("ISP Not Found for Payment: " . $payment->id);
            }
            
            return response()->json(['success' => true, 'message' => 'Topup successful, balance credited.']);
        } else {
            Log::error("Payment Record Not Found for Transaction: $transactionId");
        }
        
        return response()->json(['success' => false, 'message' => 'Payment record not found'], 404);
    }

    private function generateCredentialsAndProvision($order, &$updateData)
    {
        if (!$order->username) {
             $username = date('YmdHis') . rand(100, 999);
             $password = \Illuminate\Support\Str::random(10);
             
             $server = null;
             if ($order->server_id) {
                 $server = \App\Models\Server::find($order->server_id);
             }
             if (!$server) {
                 $server = \App\Models\Server::where('is_active', true)
                     ->whereRaw('current_users < capacity')
                     ->inRandomOrder()->first();
             }
             
             $serverIp = $server ? $server->ip_address : null;
             $serverAddress = ($server ? $server->domain : null) ?? $serverIp ?? '127.0.0.1';

             $clientIp = null;
             
             if ($server) {
                  $server->increment('current_users');
                   // Use server's configured VPN local address (consistent with ISPOrderObserver)
                   $localAddress = $server->vpn_local_address ?? '10.10.10.1';
                   
                   // Connect to Mikrotik early so we can read actual IPs from it
                   $mikrotik = null;
                   $mikrotikConnected = false;
                   if ($serverIp) {
                       try {
                           $mikrotik = new \App\Services\MikrotikService();
                           $apiUser = $server->username ?? env('MIKROTIK_USER', 'admin');
                           $apiPass = $server->password ?? env('MIKROTIK_PASS', '');
                           $apiPort = $server->api_port ?? 8728;
                           $mikrotikConnected = $mikrotik->connect($serverIp, $apiUser, $apiPass, $apiPort);
                       } catch (\Exception $e) {
                           Log::error("Mikrotik early connect error: " . $e->getMessage());
                       }
                   }

                   // Collect used IPs from DB
                   $usedIpsDb = \App\Models\ISPOrder::where('server_id', $server->id)
                       ->whereNotNull('ip_address')
                       ->pluck('ip_address')
                       ->toArray();

                   // Also collect directly from Mikrotik (source of truth)
                   $usedIpsMikrotik = ($mikrotikConnected && $mikrotik)
                       ? $mikrotik->getUsedRemoteAddresses()
                       : [];

                   // Merge both sources — eliminate duplicates
                   $usedIps = array_unique(array_merge($usedIpsDb, $usedIpsMikrotik));

                   Log::info("Server #{$server->id} — DB IPs: " . count($usedIpsDb) . ", Mikrotik IPs: " . count($usedIpsMikrotik) . ", Total excluded: " . count($usedIps));

                   // Dynamic IP Generation based on Local IP segment
                   $ipParts = explode('.', $localAddress);
                   if (count($ipParts) === 4) {
                       $baseOctet1 = $ipParts[0];
                       $baseOctet2 = $ipParts[1];
                       $baseOctet3 = (int)$ipParts[2];
                       $baseOctet4 = (int)$ipParts[3];

                       $found = false;
                       $capacity = $server->capacity ?? 1000;
                       $checkedCount = 0;

                       // Search sequentially across subnets starting from local IP's base
                       for ($subnetOffset = 0; $subnetOffset < 20 && !$found; $subnetOffset++) {
                           $currentSubnet = $baseOctet3 + $subnetOffset;
                           if ($currentSubnet > 254) break;

                           // Start host from .1, but skip the local IP itself
                           for ($h = 1; $h <= 254; $h++) {
                               $candidate = "{$baseOctet1}.{$baseOctet2}.{$currentSubnet}.{$h}";
                               
                               // Skip if it's the gateway (local IP)
                               if ($candidate === $localAddress) continue;

                               if (!in_array($candidate, $usedIps)) {
                                   $clientIp = $candidate;
                                   $found = true;
                                   break;
                               }

                               $checkedCount++;
                               if ($checkedCount >= $capacity + 500) break; // Safety limit
                           }
                       }
                   }

                   if (!$clientIp) {
                       // Absolute fallback if everything else fails
                       $clientIp = "10.254.254.254"; 
                       Log::warning("Server {$server->id} IP pool exhaustion or invalid local IP config.");
                   }

             // Mikrotik Provisioning — reuse existing connection
             if ($mikrotikConnected && $mikrotik) {
                 try {
                     $mikrotik->addPppSecret($username, $password, 'any', 'default', $localAddress, $clientIp);
                     $mikrotik->disconnect();
                 } catch (\Exception $e) {
                     Log::error("Mikrotik Provisioning Error: " . $e->getMessage());
                 }
             } elseif ($serverIp) {
                 // Fallback: try fresh connection if early connect failed
                 try {
                     $mkFallback = new \App\Services\MikrotikService();
                     $apiUser = $server->username ?? env('MIKROTIK_USER', 'admin');
                     $apiPass = $server->password ?? env('MIKROTIK_PASS', '');
                     $apiPort = $server->api_port ?? 8728;
                     if ($mkFallback->connect($serverIp, $apiUser, $apiPass, $apiPort)) {
                         $mkFallback->addPppSecret($username, $password, 'any', 'default', $localAddress, $clientIp);
                         $mkFallback->disconnect();
                     }
                 } catch (\Exception $e) {
                     Log::error("Mikrotik Fallback Provisioning Error: " . $e->getMessage());
                 }
             }
        }

             $updateData['username'] = $username;
             $updateData['password'] = $password;
             $updateData['server_address'] = $serverAddress;
             $updateData['ip_address'] = $clientIp;
             $updateData['l2tp_config'] = "/interface l2tp-client add name={$username} user={$username} password={$password} connect-to={$serverAddress} disabled=no";
             $updateData['sstp_config'] = "/interface sstp-client add name={$username} user={$username} password={$password} connect-to={$serverAddress} disabled=no";
        }
    }

    private function normalizeStatus($status) {
        if ($status == 'capture' || $status == 'settlement' || $status == 'success' || $status == 'paid') return 'success';
        if ($status == 'deny' || $status == 'expire' || $status == 'cancel' || $status == 'failed') return 'failed';
        return 'pending';
    }

    /**
     * Get available payment gateways
     */
    public function getPaymentGateways()
    {
        $gateways = PaymentGateway::where('is_active', true)
            ->orderBy('sort_order')
            ->select(['id', 'gateway_name', 'logo_url', 'is_default', 'fixed_fee', 'transaction_fee'])
            ->get();
            
        return response()->json($gateways);
    }

    /**
     * APP-SIDE Verification for Xendit (Manual Check)
     */
    public function verifyXendit(Request $request)
    {
        $externalId = $request->external_id;
        
        if (!$externalId) {
             return response()->json(['success' => false, 'message' => 'External ID required']);
        }
        
        try {
             $gateway = PaymentGatewayFactory::create('Xendit');
             
             // Use new method to find invoice by external ID
             $invoice = $gateway->getInvoiceByExternalId($externalId);
             
             if (!$invoice) {
                 return response()->json(['success' => false, 'message' => 'Invoice not found on Xendit']);
             }
             
             $status = $invoice['status']; // PENDING, PAID, SETTLED, EXPIRED
             Log::info("Manual Verification Xendit for $externalId: $status");

             if ($status === 'PAID' || $status === 'SETTLED') {
                 // Process Success
                 // Extract Payment Details
                 $method = $invoice['payment_method'] ?? 'Xendit';
                 $channel = $invoice['payment_channel'] ?? null;
                 $detailedMethod = $channel ? "$method - $channel" : $method;

                 DB::beginTransaction();
                 
                 // Handle Topup
                 if (str_starts_with($externalId, 'TOP-')) {
                      $result = $this->processTopupSuccess($externalId, $detailedMethod, $invoice['amount'], $invoice);
                      DB::commit();
                      return $result;
                 }
                 
                 // Handle Order
                 $order = $this->findOrderByExternalId($externalId);
                 if ($order) {
                      $result = $this->processPaymentSuccess($order, $detailedMethod, $externalId, $invoice);
                      DB::commit();
                      return $result;
                 } else {
                      DB::rollBack();
                      return response()->json(['success' => false, 'message' => 'Order not found locally'], 404);
                 }
             } else {
                 return response()->json(['success' => false, 'message' => 'Payment status is ' . $status]);
             }

        } catch (\Exception $e) {
             Log::error("Verify Xendit Error: " . $e->getMessage());
             return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
