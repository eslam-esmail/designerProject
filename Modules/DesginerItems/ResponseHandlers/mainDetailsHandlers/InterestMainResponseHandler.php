<?php

namespace OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers;

use OlaHub\DesignerCorner\Additional\Models\Interest;
use League\Fractal;

class InterestMainResponseHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Interest $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $className = isset($this->data->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, 'name') : NULL;
        $this->return = [
            "id" => isset($this->data->id) ? $this->data->id : 0,
            "mainSlug" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkSlug($this->data, 'interest_slug', $className),
            "mainName" => $className,
            "mainLogo" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->image_ref),
            "mainBanner" => false,
        ];
    }
}
