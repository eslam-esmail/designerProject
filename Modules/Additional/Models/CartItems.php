<?php

namespace OlaHub\DesignerCorner\Additional\Models;

use Illuminate\Database\Eloquent\Model;

class CartItems extends Model {

    protected $table = 'shopping_carts_details';

    public function cartMainData() {
        return $this->belongsTo('OlaHub\DesignerCorner\Additional\Models\Cart', 'shopping_cart_id');
    }

    
}
