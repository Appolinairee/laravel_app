<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Traits\CrudActions;
use App\Http\Traits\PermissionCheckTrait;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use CrudActions, PermissionCheckTrait;

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
            $user->standardWallet()->increment('balance', $request->amount);

            $user->standardWallet->walletTransactions()->create([
                'wallet_id' => $user->standardWallet->id,
                'type' => 'credit',
                'amount' => $request->amount,
                'description' => 'Paiement de '. $request->amount .'FCFA pour cotisation',
            ]);

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

    public function paymentForOrder (Order $order, Request $request) {

        return $this->tryCatchWrapper(function () use ($order, $request) {
            if (!$this->hasPermission($order)) {
                return response()->json([
                    'message' => "Vous n'avez pas la permission d'effectuer un paiement pour cette commande.",
                ], 403);
            }

            if($order->total_amount > 0) {
                return response()->json([
                    'message' => "Paiement déjà effectuée.",
                ], 403);
            }
        
            $user = $request->get('user_id') ? User::findOrFail($request->get('user_id')) : auth()->user();

            $order->update(['total_amount' => $order->calculateTotalAmount()]);

            
            if($user->standardWallet->balance < $order->total_amount){
                return response()->json([
                    'message' => "Le solde sur votre portefeuille utilisateur est insuffisant. Veuillez le créditer!",
                ], 403);
            }

            $user->standardWallet()->decrement('balance', $order->total_amount);

            $order->update(['amount_paid' => $order->total_amount]);

            $user->standardWallet->walletTransactions()->create([
                'wallet_id' => $user->standardWallet->id,
                'type' => 'debit',
                'amount' => $order->total_amount,
                'description' => 'Paiement de '. $order->total_amount .'FCFA pour la commande',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Paiement pour la commande effectué avec succès',
            ], 200);
        });
    }
    
    public function paymentForDelivery (Order $order, Request $request) {

        return $this->tryCatchWrapper(function () use ($order, $request) {
            if (!$this->hasPermission($order)) {
                return response()->json([
                    'message' => "Vous n'avez pas la permission d'effectuer un paiement pour la livraison de cette commande.",
                ], 403);
            }

            if($order->shipping_paid) {
                return response()->json([
                    'message' => "Les frais de livraison sont déjà payés.",
                ], 400);
            }

            if(!$order->can_ship) {
                return response()->json([
                    'message' => "Les frais de livraison ne sont pas encore définis. Veuillez patienter!",
                ], 400);
            }

            if($order->can_ship && $order->shipping_price <= 0) {
                return response()->json([
                    'message' => "Aucun frais de livraion ne s'applique à cette commande!",
                ], 400);
            }
        
            $user = $request->get('user_id') ? User::findOrFail($request->get('user_id')) : auth()->user();
            
            if($user->standardWallet->balance < $order->shipping_price){
                return response()->json([
                    'message' => "Le solde sur votre portefeuille utilisateur est insuffisant. Veuillez le créditer!",
                ], 400);
            }

            $user->standardWallet()->decrement('balance', $order->shipping_price);

            $order->update(['shipping_paid' => true]);

            $user->standardWallet->walletTransactions()->create([
                'wallet_id' => $user->standardWallet->id,
                'type' => 'debit',
                'amount' => $order->shipping_price,
                'description' => 'Paiement de '. $order->shipping_price .'FCFA pour la livraison de la commande',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Paiement pour la livraion effectué avec succès',
            ], 200);
        });
    }

}