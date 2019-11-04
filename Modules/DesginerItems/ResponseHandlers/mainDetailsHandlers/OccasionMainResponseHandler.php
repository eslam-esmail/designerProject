<?php

namespace OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers;

use OlaHub\DesignerCorner\Occasions\Models\Occasions;
use League\Fractal;

class OccasionMainResponseHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Occasions $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $className = isset($this->data->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, 'name') : NULL;
        $this->return = [
            "id" => isset($this->data->id) ? $this->data->id : 0,
            "mainSlug" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkSlug($this->data, 'occasion_slug', $className),
            "mainName" => $className,
            "mainLogo" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->logo_ref),
            "mainBanner" => false,
        ];
    }
}
