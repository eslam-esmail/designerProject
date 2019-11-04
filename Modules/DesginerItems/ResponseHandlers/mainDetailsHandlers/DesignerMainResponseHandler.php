<?php

namespace OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers;

use OlaHub\DesignerCorner\Additional\Models\Desginers;
use League\Fractal;

class DesignerMainResponseHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Desginers $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $className = isset($this->data->brand_name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, 'brand_name') : NULL;
        $this->return = [
            "id" => isset($this->data->id) ? $this->data->id : 0,
            "mainSlug" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkSlug($this->data, 'designer_slug', $className),
            "mainName" => $className,
            "mainLogo" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->logo_ref),
            "mainBanner" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->banner_image_ref),
        ];
    }

}
