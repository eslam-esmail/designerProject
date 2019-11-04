<?php

namespace OlaHub\DesignerCorner\ItemImages\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class ItemImages extends CommonMySQLModel {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    public static function boot() {
        parent::boot();
    }

    protected $table = 'designer_item_images';

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

    static function getItemImage($itemId){
        $itemImage = ItemImages::where('item_id',$itemId)->get();
//        dd($itemImage);
        $itemImageData = [];
        foreach ($itemImage as $image){
            $itemImageData[] =[
                "image" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($image->content_ref),
                "imageId" => isset($image->id) ? $image->id : 0,
                "default_image" =>isset($image->is_default) ? $image->is_default : 0,
            ];
        }
        return $itemImageData;
    }

}
