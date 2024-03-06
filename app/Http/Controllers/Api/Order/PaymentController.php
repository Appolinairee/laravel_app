<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Api\Interaction\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentUpdateRequest;
use App\Models\Contribution;
use App\Models\Order;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Notifications\NewCreatorNotification;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentUpdateRequest $request, Order $order){
        try {
            $this->authorize('storeInExistingOrder', $order);

            $orderData = $request->only(['amount_paid', 'payment_type']);

            if (empty($orderData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucune information à mettre à jour.',
                ], 400);
            }

            // verify order status
            if($order->status !== 0 && $order->status !== 1){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Le statut de la commande doit être 0 ou 1 pour le paiement.',
                ], 403);
            } 

            if($request->payment_type === 1){
                if($request->amount_paid < $order->total_amount){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Somme insuffisante pour le paiement en bloc de cette commande: '.$order->total_amount,
                    ], 403);
                }
                
                //update total amount (to fix it)
                $order->update(['total_amount' => $order->calculateTotalAmount()]);

                if($order->total_amount < $request->amount_paid){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Une erreur est survenue. Montant supérieure à celuide la commande.'
                    ], 403);
                }

                $order->update(['amount_paid' => $request->amount_paid]); 
                $order->update(['payment_type' => $request->payment_type]); 


                $order->update(['payment_status' => 1]); //paid status
                $order->update(['status' => 2]); //delievering status

                return response()->json([
                    'status' => 'success',
                    'message' => "Paiement ajouté avec succès.",
                    'data' => $order
                ], 200);
            }

            $contributionMinimum = 5000; //FCFA

            if($request->payment_type === 0 && $request->amount_paid < 5000){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Montant de cotisation insuffisante. Le minimum est de '.$contributionMinimum,
                ], 403);
            }


            //update total amount (to fix it)
            $order->update(['total_amount' => $order->calculateTotalAmount()]);

            if($order->total_amount < $request->amount_paid){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Une erreur est survenue. Montant supérieur à celuide la commande.'
                ], 403);
            }

            $order->update(['payment_type' => $request->payment_type]);
            $order->update(['amount_paid' => $request->amount_paid + $order->amount_paid]); 

            $order->update(['payment_status' => 0]); // in payment status
            $order->update(['status' => 1]); //payment order status

            // store contribution
            Contribution::create([
                'order_id' => $request->amount_paid,
                'amount' => $request->amount_paid
            ]);

            // verify if is total level of contribution
            if($order->amount_paid >= $order->total_amount){
                $order->update(['payment_status' => 1]); //paid status
                $order->update(['status' => 2]); //delievering status

                return response()->json([
                    'status' => 'success',
                    'message' => "Cotisation ajoutée avec succès. Paiement pour la commande achevé.",
                    'data' => [
                        'order' => $order,
                        'contributions' => $order->contributions
                    ]
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => "Cotisation ajoutée avec succès.",
                'data' => [
                    'order' => $order,
                    'contributions' => $order->contributions
                ]
            ], 200);

        }catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);

        }   catch (Exception $e) {
            return response()->json($e);
        }
    }

    private function notifyForPayment($creator, $user, $order){
        // intern notifications

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $notificationData  = [
                'title' => "Paiment effectué.",
                'content' => "$user->name vient de solder pour une commande.",
                'user_id' => $admin->id,
                'notifiable_id' => $order->id,
                'notifiable_type' => \App\Models\Order::class,
            ];

            (new NotificationController)->store($notificationData);


            // extern notification
            $data = [
                'subject' => 'Un paiment effectué avec succès.',
                'greeting' => $admin->name,
                'message' => "$user->name vient de solder pour une commande. Veuillez faire les configurations et les vérifications nécessaire!",
                'actionText' => 'Voir la commande',
                'actionUrl' => '',
            ];

            $admin->notify(new GeneralNotification($data));
        }

        $notificationData  = [
            'title' => "Paiment effectué.",
            'content' => "$user->name vient de solder pour une commande.",
            'user_id' => $creator->id,
            'notifiable_id' => $order->id,
            'notifiable_type' => \App\Models\Order::class,
        ];

        (new NotificationController)->store($notificationData);

        // extern notification
        $data = [
            'subject' => 'Un paiment effectué avec succès.',
            'greeting' => $admin->name,
            'message' => "$user->name vient de solder pour une commande. Veuillez faire les configurations et les vérifications nécessaire!",
            'actionText' => 'Voir la commande',
            'actionUrl' => '',
        ];

        $admin->notify(new GeneralNotification($data));
    }

    private function notifyForContribution($creator, $user, $order){
        // intern notifications
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $notificationData  = [
                'title' => "Paiment effectué.",
                'content' => "Demande de création de compte vendeur par $creator->name.",
                'user_id' => $admin->id,
                'notifiable_id' => $creator->id,
                'notifiable_type' => \App\Models\Creator::class,
            ];

            (new NotificationController)->store($notificationData);
        }

    }


}
