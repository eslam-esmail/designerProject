<?php

namespace OlaHub\DesignerCorner\Classifications\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class Classifications extends CommonMySQLModel {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    protected $table = 'lkp_catalog_items_classification';

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
