<?php

namespace OlaHub\DesignerCorner\DesginerItems\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class ItemCategory extends CommonMySQLModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    protected $table = 'catalog_item_categories';
    protected $columnsMaping = [
        'catName' => [
            'column' => 'name',
            'type' => 'multiLang',
            'relation' => false,
            'validation' => 'required|max:4000'
        ],
        'catParent' => [
            'column' => 'parent_id',
            'type' => 'numNull',
            'manyToMany' => false,
            'validation' => '',
            'filterValidation' => 'integer',
        ],
    ];

    static function setReturnResponse($categories, $childsID = []) {
        $return['data'] = [];
        foreach ($categories as $category) {
            $catData = [
                "classID" => isset($category->id) ? $category->id : 0,
                "classSlug" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkSlug($category, 'category_slug', \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($category, 'name')),
                "className" => isset($category->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($category, 'name') : NULL,
            ];
            
            $catData['childsData'] = [];
            $childs = [];
            if (count($childsID) > 0) {
                $childs = ItemCategory::where("parent_id", $category->id)->whereIn('id', $childsID)->get();
            } 
            foreach ($childs as $child) {
                $catData['childsData'][] = [
                    "classID" => isset($child->id) ? $child->id : 0,
                    "classSlug" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkSlug($child, 'category_slug', \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($child, 'name')),
                    "className" => isset($category->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($child, 'name') : NULL,
                ];
            }
            $return['data'][] = $catData;
        }
        return (array) $return;
    }

}
