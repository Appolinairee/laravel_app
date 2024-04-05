<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Api\Interaction\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentUpdateRequest;
use App\Models\Contribution;
use App\Models\Order;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;

class PaymentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentUpdateRequest $request, Order $order)
    {
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
            if ($order->status != 0 && $order->status != 1) {
                return response()->json([
                    'message' => 'Le statut de la commande doit être 0 ou 1 pour le paiement.',
                ], 403);
            }

            // for incorrect payment_type choice
            if($request->payment_type == 1 && $order->payment_type == 0){
                return response()->json([
                    'message' => 'Mauvais type de paiement.',
                ], 403);
            }

            //update total amount (to fix it)
            $order->update(['total_amount' => $order->calculateTotalAmount()]);

            if ($request->payment_type == 1) {
                if ($request->amount_paid < $order->total_amount) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Somme insuffisante pour le paiement en bloc de cette commande: ' . $order->total_amount,
                    ], 403);
                }

                if ($order->total_amount < $request->amount_paid) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Une erreur est survenue. Montant supérieure à celui de la commande.'
                    ], 403);
                }

                $order->update(['amount_paid' => $request->amount_paid]);
                $order->update(['payment_type' => $request->payment_type]);

                $order->update(['payment_status' => 1]); //paid status
                $order->update(['status' => 1]); //delievering status

                $this->notifyForPayment($order->creator, auth()->user(), $order);


                $order->load('contributions');
                $order->total_amount = $order->calculateTotalAmount();
                $order->load('order_items');

                return response()->json([
                    'status' => 'success',
                    'message' => "Paiement ajouté avec succès.",
                    'data' => $order
                ], 200);
            }

            $contributionMinimum = 5000; //FCFA

            if ($request->payment_type === 0 && $request->amount_paid < 5000) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Montant de cotisation insuffisante. Le minimum est de ' . $contributionMinimum,
                ], 403);
            }


            //update total amount (to fix it)
            if ($order->total_amount < $request->amount_paid) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Une erreur est survenue. Montant supérieur à celui de la commande.'
                ], 403);
            }

            $order->update(['payment_type' => $request->payment_type]);
            $order->update(['amount_paid' => $request->amount_paid + $order->amount_paid]);

            $order->update(['payment_status' => 0]); // in payment status
            $order->update(['status' => 0]); //payment order status

            // store contribution
            Contribution::create([
                'order_id' => $request->amount_paid,
                'amount' => $request->amount_paid
            ]);

            $order->update(['total_amount' => $order->calculateTotalAmount()]);
            $order->total_amount = $order->calculateTotalAmount();
            $order->load('contributions');
            $order->load('order_items');

            // verify if is total level of contribution
            if ($order->amount_paid >= $order->total_amount) {
                $order->update(['payment_status' => 1]); //paid status
                $order->update(['status' => 2]); //delievering status

                $this->notifyForPayment($order->creator, auth()->user(), $order);

                return response()->json([
                    'status' => 'success',
                    'message' => "Cotisation ajoutée avec succès. Paiement pour la commande achevé.",
                    'data' => $order
                ], 200);
            }


            $this->notifyForPayment($order->creator, auth()->user(), $order);

            return response()->json([
                'status' => 'success',
                'message' => "Cotisation ajoutée avec succès.",
                'data' => $order,
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    private function InternNotificationUnit($receiver_id, $user, $order)
    {

        if ($receiver_id == $user->id)
            $message =  "Vous avez payé pour l'une de vos commandes. Veuillez lancer la configuration!";
        else
            $message = "$user->name vient de solder pour une commande.";

        $notificationData  = [
            'title' => "Paiment effectué.",
            'content' =>  $message,
            'user_id' => $receiver_id,
            'notifiable_id' => $order->id,
            'notifiable_type' => \App\Models\Order::class,
        ];

        (new NotificationController)->store($notificationData);
    }


    private function ExternNotificationUnit($receiver, $user)
    {

        if ($receiver->id == $user->id)
            $message =  "Vous avez payé pour l'une de vos commandes.!";
        else
            $message = "$user->name vient de solder pour une commande. Veuillez faire les configurations nécessaires!";

        // extern notification
        $data = [
            'subject' => 'Un paiment effectué avec succès.',
            'greeting' => $receiver->name,
            'message' => $message,
            'actionText' => 'Voir la commande',
            'actionUrl' => '',
        ];

        $receiver->notify(new GeneralNotification($data));
    }

    private function notifyForPayment($creator, $user, $order)
    {
        // intern notifications

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {

            $this->InternNotificationUnit($admin->id, $user, $order);

            $this->ExternNotificationUnit($admin, $user);
        }

        // creator
        $this->InternNotificationUnit($creator->id, $user, $order);
        $this->ExternNotificationUnit($creator, $user);

        // user
        $this->InternNotificationUnit($user->id, $user, $order);
        $this->ExternNotificationUnit($user, $user);
    }
}
