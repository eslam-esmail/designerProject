<?php

namespace OlaHub\DesignerCorner\ProductAttribute\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class ProductAttribute extends CommonMySQLModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    protected $table = 'catalog_item_attributes';

    public function productAttributeValue() {
        return $this->hasMany('\OlaHub\DesignerCorner\ProductAttributeValue\Models\ProductAttributeValue', 'product_attribute_id');
    }

    public function attributeCategories() {
        return $this->hasMany('\OlaHub\DesignerCorner\ProductCategoryAttribute\Models\ProductCategoryAttribute', 'catalog_item_attribute_id');
    }

}
