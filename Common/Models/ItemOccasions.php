<?php

namespace OlaHub\DesignerCorner\ItemOccasions\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class ItemOccasions extends CommonMySQLModel {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    protected $table = 'designer_item_occasions';

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

    public function itemData() {
        return $this->belongsTo('OlaHub\DesignerCorner\DesignerItemsManagements\Models\DesignerItemsManagement','item_id');
    }

    public function occasionData() {
        return $this->belongsTo('OlaHub\DesignerCorner\Occasions\Models\Occasions','occasion_id');
    }

}
