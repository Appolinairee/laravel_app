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
}