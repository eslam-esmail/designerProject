<?php

namespace OlaHub\DesignerCorner\ExchangRefund\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class ExchangRefund extends CommonMySQLModel {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    protected $table = 'exchange_refund_policies';

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

    public function itemExchRefPolicyData() {
        return $this->belongsTo('OlaHub\DesignerCorner\ExchangRefund\Models\ExchangRefund','id');
    }

}
