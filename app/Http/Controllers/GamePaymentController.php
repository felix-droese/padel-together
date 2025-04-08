<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePayment;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class GamePaymentController extends Controller
{
    public function create(Request $request, Game $game)
    {
        $userPlayer = Player::where('user_id', Auth::id())->firstOrFail();
        $payment = GamePayment::where('game_id', $game->id)
            ->where('player_id', $userPlayer->id)
            ->firstOrFail();

        if ($payment->status === 'completed') {
            return response()->json(['error' => 'Payment already completed'], 400);
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);

            $amount = number_format($payment->amount_in_cent / 100, 2);

            $order = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('game-payments.success', ['game' => $game->id]),
                    'cancel_url' => route('game-payments.cancel', ['game' => $game->id]),
                ],
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'EUR',
                            'value' => $amount,
                        ],
                        'description' => "Padel Game Payment - {$game->date}",
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

    public function success(Request $request, Game $game)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);

            $result = $provider->capturePaymentOrder($request->token);

            if ($result['status'] === 'COMPLETED') {
                $userPlayer = Player::where('user_id', Auth::id())->firstOrFail();
                $payment = GamePayment::where('game_id', $game->id)
                    ->where('player_id', $userPlayer->id)
                    ->firstOrFail();

                $payment->update([
                    'status' => 'completed',
                    'paypal_payment_id' => $result['id'],
                ]);
            }

            return redirect()->route('games.index');
        } catch (\Exception $e) {
            return redirect()->route('games.index');
        }
    }

    public function cancel(Request $request, Game $game)
    {
        return redirect()->route('games.index');
    }
}
