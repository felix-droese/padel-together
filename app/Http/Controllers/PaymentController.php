<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
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

            return Inertia::render('Payment/Redirect', [
                'paypalUrl' => $order['links'][1]['href'],
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Payment/Error', [
                'error' => $e->getMessage(),
            ]);
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

            return Inertia::render('Payment/Success', [
                'payment' => $result,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Payment/Error', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
