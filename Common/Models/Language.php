<?php

namespace OlaHub\DesignerCorner\commonData\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class Language extends CommonMySQLModel {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    protected $table = 'languages';


    public function countryData() {
        return $this->hasOne('OlaHub\DesignerCorner\Additional\Models\Country','currency_id');
    }
}
