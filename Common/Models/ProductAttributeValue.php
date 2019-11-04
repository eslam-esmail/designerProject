<?php

namespace OlaHub\DesignerCorner\ProductAttributeValue\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;

class ProductAttributeValue extends CommonMySQLModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }
    protected $table = 'catalog_attribute_values';

    public function productAttribute(){
        return $this->belongsTo('\OlaHub\DesignerCorner\ProductAttribute\Models\ProductAttribute', 'product_attribute_id');
    }


}
