<?php

namespace OlaHub\DesignerCorner\Additional\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model {

    protected $table = 'shopping_carts';

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('countryUser', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where('user_id', app('session')->get('tempID'));
        });


        static::saving(function ($query) {
            if (!isset($query->celebration_id) && !($query->celebration_id > 0)) {
                if (!isset($query->user_id) && !$query->user_id) {
                    $query->user_id = app('session')->get('tempID');
                }
            }
        });
    }

    static $columnsMaping = [
        'itemID' => [
            'column' => 'item_id',
            'type' => 'number',
            'relation' => false,
            'validation' => 'required'
        ],
        'itemQuantity' => [
            'column' => 'quantity',
            'type' => 'number',
            'relation' => false,
            'validation' => 'numeric'
        ],
    ];

    public function cartDetails() {
        return $this->hasMany('OlaHub\DesignerCorner\Additional\Models\CartItems', 'shopping_cart_id');
    }

    
}
