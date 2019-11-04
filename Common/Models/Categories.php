<?php

namespace OlaHub\DesignerCorner\Categories\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class Categories extends CommonMySQLModel {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    protected $table = 'catalog_item_categories';

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



    public function childsData() {
        return $this->hasMany('OlaHub\DesignerCorner\Categories\Models\Categories','parent_id');
    }

    public function itemCategoryData() {
        return $this->belongsTo('OlaHub\DesignerCorner\Categories\Models\Categories','parent_id','id');
    }
}
