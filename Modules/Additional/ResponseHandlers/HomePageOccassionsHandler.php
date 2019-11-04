<?php

namespace OlaHub\DesignerCorner\Additional\Handlers;

use OlaHub\DesignerCorner\Occasions\Models\Occasions;
use League\Fractal;

class HomePageOccassionsHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Occasions $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $this->return = [
            "occassion" => isset($this->data->id) ? $this->data->id : 0,
            "occassionSlug" => isset($this->data->occasion_slug) ? $this->data->occasion_slug : null,
            "occassionName" => isset($this->data->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, "name") : null,
            "occassionImage" => isset($this->data->logo_ref) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->logo_ref) : null,
        ];
    }

}