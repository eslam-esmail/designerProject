<?php

namespace OlaHub\DesignerCorner\DesignerSliders\Handlers;

use OlaHub\DesignerCorner\DesignerSliders\Models\DesignerSliders;
use League\Fractal;

class DesignerSlidersHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(DesignerSliders $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $language = config("def_lang");
        $this->return = [
            "number" => isset($this->data->_id) ? $this->data->_id : 0,
            "item"   => isset($this->data->slider_image[$language]) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->slider_image[$language]): NULL,
            "sliderLink"   => isset($this->data->slider_link) ? $this->data->slider_link : NULL,
            "sliderText"   => isset($this->data->slider_description[$language]) ? $this->data->slider_description[$language] : NULL,
            "sliderRef"   => isset($this->data->slider_image[$language]) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->slider_image[$language]): NULL,
        ];
    }

}
