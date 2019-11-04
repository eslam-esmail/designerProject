<?php

namespace OlaHub\DesignerCorner\ProductCategoryAttribute\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class ProductCategoryAttribute extends CommonMySQLModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    protected $table = 'catalog_items_attrib_category';

    public function productAttribute() {
        return $this->belongsTo('\OlaHub\DesignerCorner\ProductAttribute\Models\ProductAttribute', 'catalog_item_attribute_id');
    }

    public function category() {
        return $this->belongsTo('\OlaHub\DesignerCorner\Categories\Models\Categories', 'catalog_item_category_id');
    }

}
