<?php

namespace OlaHub\DesignerCorner\Additional\Handlers;

use OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems;
use League\Fractal;

class HomePageItemsHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(DesginerItems $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $this->return = [
            "productID" => isset($this->data->id) ? $this->data->id : 0,
            "productSlug" => isset($this->data->item_slug) ? $this->data->item_slug : null,
            "productName" => isset($this->data->item_title) ? $this->data->item_title : null,
            "productDescription" => isset($this->data->item_description) ? $this->data->item_description : null,
            "productInStock" => isset($this->data->item_stock) ? $this->data->item_stock : 0,
            "productPrice" => isset($this->data->item_price) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($this->data->item_price, true) : 0,
            "productOwner" => isset($this->data->designer_id) ? $this->data->designer_id : 0,
            "productOwnerName" => isset($this->data->designer_name) ? $this->data->designer_name : 0,
            "productOwnerSlug" => isset($this->data->designer_slug) ? $this->data->designer_slug : 0,
            "productImage" => isset($this->data->content_ref) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->content_ref) : null,
            "number" => isset($this->data->_id) ? $this->data->_id : 0,
            "productSlug" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkSlug($this->data, 'item_slug', $this->data->item_title),
            "productName" => isset($this->data->item_title) ? $this->data->item_title : NULL,
            "productDescription" => isset($this->data->item_description) ? $this->data->item_description : NULL,
            "productInStock" => isset($this->data->item_stock) ? $this->data->item_stock : NULL,
        ];
        
        $this->setPriceData();
        $this->setImageData();
    }
    
    private function setPriceData(){
        if($this->data->discount_end_date && $this->data->discount_end_date >= date("Y-m-d")){
            $this->return["productOriginalPrice"] = $this->data->item_original_price ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($this->data->item_original_price, true): 0;
        }
    }
    
    private function setImageData(){
        $images = $this->data->item_images ? $this->data->item_images : [];
        if(is_array($images) && count($images) > 0){
            $this->return["productImage"] = isset($images[0]) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($images[0]) : \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl(false);
        }else{
            $this->return["productImage"] = \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl(false);
        }
    }

}
