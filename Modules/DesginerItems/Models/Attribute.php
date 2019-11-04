<?php

namespace OlaHub\DesignerCorner\DesginerItems\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class Attribute extends CommonMySQLModel {

    protected $table = 'catalog_item_attributes';

    static function setReturnResponse($attributes, $childsId = []) {
        $return['data'] = [];
        foreach ($attributes as $attribute) {
            $attrData = [
                "valueID" => isset($attribute->id) ? $attribute->id : 0,
                "valueName" => isset($attribute->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($attribute, 'name') : NULL,
                "valueColorStyle" => isset($attribute->is_color_style) ? $attribute->is_color_style : 0,
                "valueSizeStyle" => isset($attribute->is_size_style) ? $attribute->is_size_style : 0,
            ];

            $attrData['childsData'] = [];
            $childs = [];
            if (count($childsId) > 0) {
                $childs = AttrValue::where('product_attribute_id', $attribute->id)->whereIn('id', $childsId)->get();
            }
            foreach ($childs as $child) {
                $attrData['childsData'][] = [
                    "valueID" => isset($child->id) ? $child->id : 0,
                    "valueName" => isset($child->attribute_value) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($child, 'attribute_value') : NULL,
                    "valueHexColor" => isset($child->color_hex_code) ? $child->color_hex_code : NULL,
                ];
            }
            $return['data'][] = $attrData;
        }
        return (array) $return;
    }

    /*static function setOneProductReturnResponse($attributes, $itemsIDs = false, $first = false) {
        $return['data'] = [];
        foreach ($attributes as $attribute) {
            $attrData = [
                "valueID" => isset($attribute->id) ? $attribute->id : 0,
                "valueName" => isset($attribute->name) ? \OlaHub\UserPortal\Helpers\OlaHubCommonHelper::returnCurrentLangField($attribute, 'name') : NULL,
                "valueColorStyle" => isset($attribute->is_color_style) ? $attribute->is_color_style : 0,
                "valueSizeStyle" => isset($attribute->is_size_style) ? $attribute->is_size_style : 0,
            ];

            $attrData['childsData'] = [];
            if ($itemsIDs) {
                $childs = $attribute->valuesData()->whereHas('valueItemsData', function($q) use($itemsIDs, $first) {
                            if ($first) {
                                $q->whereIn('parent_item_id', $itemsIDs);
                            } else {
                                $q->whereIn('item_id', $itemsIDs);
                            }
                        })->groupBy('id')->get();
            } else {
                $childs = $attribute->childsData()->has('itemsMainData')->groupBy('id')->get();
            }
            foreach ($childs as $child) {
                $attrData['childsData'][] = [
                    "value" => isset($child->id) ? (string) $child->id : 0,
                    "text" => isset($child->attribute_value) ? \OlaHub\UserPortal\Helpers\OlaHubCommonHelper::returnCurrentLangField($child, 'attribute_value') : NULL,
                    "valueHexColor" => isset($child->color_hex_code) ? $child->color_hex_code : NULL,
                ];
            }
            $return['data'][] = $attrData;
        }
        return (array) $return;
    }*/

}
