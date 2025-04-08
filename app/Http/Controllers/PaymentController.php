<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);

            $order = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('payments.success'),
                    'cancel_url' => route('payments.cancel'),
                ],
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'EUR',
                            'value' => $request->amount,
                        ],
                        'description' => 'Padel Game Payment',
                    ],
                ],
            ]);

            return response()->json([
                'paypalUrl' => $order['links'][1]['href'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function capture(Request $request)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);

            $result = $provider->capturePaymentOrder($request->token);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function success(Request $request)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);

            $result = $provider->capturePaymentOrder($request->token);

            // Here you would typically:
            // 1. Update your database to mark the payment as completed
            // 2. Associate the payment with the game
            // 3. Send confirmation emails, etc.

            return redirect()->route('games.index')->with('success', 'Payment completed successfully!');
        } catch (\Exception $e) {
            return redirect()->route('games.index')->with('error', 'Payment failed: '.$e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('games.index')->with('info', 'Payment was cancelled.');
    }
}
