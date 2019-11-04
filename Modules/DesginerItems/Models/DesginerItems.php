<?php

namespace OlaHub\DesignerCorner\DesginerItems\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class DesginerItems extends Eloquent {

    protected $connection = 'mongo';
    protected $collection = 'designers_items';
    
    static function checkPrice($item, $final = false, $withCurr = true){
    $return["productPrice"] = isset($item->item_price) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($item->item_price, $withCurr) : 0;
        $return["productDiscountedPrice"] = \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($item->item_price, $withCurr);
        $return["productHasDiscount"] = false;
        if (isset($item->item_original_price) && $item->item_original_price && strtotime($item->discount_start_date) <= time() && strtotime($item->discount_end_date) >= time()) {
            $return["productPrice"] = isset($item->item_original_price) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($item->item_original_price, $withCurr) : 0;
            $return["productHasDiscount"] = true;
        }

        if ($final) {
            return $return["productDiscountedPrice"];
        }
        return $return;
    }

}
