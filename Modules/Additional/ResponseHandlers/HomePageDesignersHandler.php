<?php

namespace OlaHub\DesignerCorner\Additional\Handlers;

use OlaHub\DesignerCorner\Additional\Models\Desginers;
use League\Fractal;

class HomePageDesignersHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Desginers $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $this->return = [
            "designer" => isset($this->data->id) ? $this->data->id : 0,
            "designerSlug" => isset($this->data->designer_slug) ? $this->data->designer_slug : null,
            "designerName" => isset($this->data->brand_name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, "brand_name") : null,
            "designerImage" => isset($this->data->logo_ref) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->logo_ref) : null,
        ];
    }

}