<?php

namespace OlaHub\DesignerCorner\ItemAttributeValues\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class ItemAttributeValues extends CommonMySQLModel {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    protected $table = 'designer_item_attribute_values';

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

    static function getItemAttributs($itemId){
        $return = [];
        $itemAttributs = ItemAttributeValues::where('item_id',$itemId)->get();

        foreach ($itemAttributs as $one){

            $itemAttributesValues = \OlaHub\DesignerCorner\ProductAttributeValue\Models\ProductAttributeValue::where('id',$one->value_id)->first();

            if($itemAttributesValues){
                $attribute = \OlaHub\DesignerCorner\ProductAttribute\Models\ProductAttribute::where('id',$itemAttributesValues->product_attribute_id)->first();
                array_push($return, $itemAttributesValues->id);
            }else{
                continue;
            }

        }
        return $return;
    }

    static function getItemAttributsValues($itemId){
        $return = [];
        $itemAttributs = ItemAttributeValues::where('item_id',$itemId)->get();
        foreach ($itemAttributs as $one){

            $itemAttributesValues = \OlaHub\DesignerCorner\ProductAttributeValue\Models\ProductAttributeValue::where('id',$one->value_id)->first();
            if($itemAttributesValues) {
                $attribute = \OlaHub\DesignerCorner\ProductAttribute\Models\ProductAttribute::where('id', $itemAttributesValues->product_attribute_id)->first();
            }else{
                continue;
            }
            $return[] = [
                "value" => isset($itemAttributesValues->id) ?  $itemAttributesValues->id : 0,
                "text" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($itemAttributesValues, 'attribute_value'),
                "attrText" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($attribute, 'name'),
                "attrValue" => isset($attribute->id) ?  $attribute->id : 0,
            ];

        }
        return $return;


    }
}
