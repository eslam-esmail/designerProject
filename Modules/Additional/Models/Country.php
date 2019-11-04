<?php

namespace OlaHub\DesignerCorner\Additional\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    protected static function boot() {
        parent::boot();
    }

    protected $table = 'countries';

    public function currencyData() {
        return $this->belongsTo('OlaHub\DesignerCorner\commonData\Models\Currency','currency_id');
    }

    public function languageData() {
        return $this->belongsTo('OlaHub\DesignerCorner\commonData\Models\Language','language_id');
    }
}
