<?php

namespace App\Helpers;
use Illuminate\Support\Str;

class FrontendLink {

    public static function productLink($productName){
        $slugName = Str::slug($productName);

        return env('URL_FRONTEND'). '/produit/' . $slugName;
    }

    public function affiliateLink($productName, $userAffiliateCode){
        return $this->productLink($productName). '?a=' . $userAffiliateCode;
    }

    public function userLink($userName){
        $slugName = Str::slug($userName);
        return env('URL_FRONTEND'). '/utilisateur?u=' . $slugName;
    }

    public function orderLink($orderId){
        return env('URL_FRONTEND'). '/commande?c=' . $orderId;
    }

    public function notificationLink($notificationType, $notificationEntity) {

        if($notificationType === \App\Models\User::class){
            return str_replace(env('URL_FRONTEND'), '', $this->userLink($notificationEntity->name));
        }else if($notificationType === \App\Models\Order::class){
            if($notificationEntity && $notificationEntity->id)
                return str_replace(env('URL_FRONTEND'), '', $this->orderLink($notificationEntity->id));
            
                return '';
        }else if($notificationType === \App\Models\Product::class){
            return str_replace(env('URL_FRONTEND'), '', $this->productLink($notificationEntity->title));
        }

        return null;
    }
}