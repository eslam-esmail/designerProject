<?php

namespace OlaHub\DesignerCorner\DesginerItems\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class FranchiseDesginerCountry extends CommonMySQLModel {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    protected $table = 'franchise_designer_country';
    
    static $columnsMapping = [
        "requestName1" => [
            "columnName" => "original_name",
            "validations" => "validation1|validation2",
        ],
        "requestName2" => [
            "columnName" => "original_name",
            "validations" => "validation1|validation2",
        ],
    ];

}
