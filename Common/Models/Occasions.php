<?php

namespace OlaHub\DesignerCorner\Occasions\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class Occasions extends CommonMySQLModel {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    protected $table = 'occasion_types';

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

    public function itemOccasionData() {
        return $this->hasMany('OlaHub\DesignerCorner\Occasions\Models\Occasions','id');
    }

}
