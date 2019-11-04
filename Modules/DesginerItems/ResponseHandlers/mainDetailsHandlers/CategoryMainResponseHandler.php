<?php

namespace OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers;

use OlaHub\DesignerCorner\Categories\Models\Categories;
use League\Fractal;

class CategoryMainResponseHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Categories $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $className = isset($this->data->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, 'name') : NULL;
        $this->return = [
            "id" => isset($this->data->id) ? $this->data->id : 0,
            "mainSlug" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkSlug($this->data, 'category_slug', $className),
            "mainLogo" => false,
            "mainBanner" => false,
        ];
        $this->setName($className);
    }
    
    private function setName($name){
        $finalName = "";
        if($this->data->parent_id > 0){
            $parent = Categories::find($this->data->parent_id);
            if($parent){
                $finalName = \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($parent, 'name')." - ";
            }
        }
        $this->return["mainName"] = $finalName.$name;
    }

}
