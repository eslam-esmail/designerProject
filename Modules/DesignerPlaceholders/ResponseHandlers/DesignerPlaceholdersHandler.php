<?php

namespace OlaHub\DesignerCorner\DesignerPlaceholders\Handlers;

use OlaHub\DesignerCorner\DesignerPlaceholders\Models\DesignerPlaceholders;
use League\Fractal;

class DesignerPlaceholdersHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(DesignerPlaceholders $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $language = config("def_lang");
        $this->return = [
            "number" => isset($this->data->_id) ? $this->data->_id : 0,
            "adRef"  => isset($this->data->placeholder_image[$language]) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->placeholder_image[$language]) : NULL,
            "adText" => isset($this->data->placeholder_description[$language]) ? $this->data->placeholder_description[$language] : NULL,
            "adLink" => isset($this->data->placeholder_link) ? $this->data->placeholder_link : NULL,
	    "adTarget" => "self"
        ];
    }

}
