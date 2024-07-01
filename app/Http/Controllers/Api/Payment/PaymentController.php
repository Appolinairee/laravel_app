<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Traits\CrudActions;
use App\Models\Payment;
use App\Models\User;

class PaymentController extends Controller
{
    use CrudActions;

    public function store(StorePaymentRequest $request)
    {
        return $this->tryCatchWrapper(function () use ($request) {

            $minimumAmount = 2000;

            if ($request->amount < $minimumAmount) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Le montant minimum pour un paiement est de 2000 FCFA.',
                ], 400);
            }

            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'type' => $request->type,
                'description' => $request->description,
            ]);

            $user = $request->user();
            $user->standardWallets()->increment('balance', $request->amount);

            return response()->json([
                'status' => 'success',
                'data' => $payment,
            ], 201);
        });
    }


    public function getByUser(User $user)
    {
        return $this->tryCatchWrapper(function () use ($user) {
            $data = $user->payments;

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        });
    }
}