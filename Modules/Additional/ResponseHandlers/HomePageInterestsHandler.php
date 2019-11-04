<?php

namespace OlaHub\DesignerCorner\Additional\Handlers;

use OlaHub\DesignerCorner\Additional\Models\Interest;
use League\Fractal;

class HomePageInterestsHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Interest $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $this->return = [
            "interest" => isset($this->data->interest_id) ? $this->data->interest_id : 0,
            "interestSlug" => isset($this->data->interest_slug) ? $this->data->interest_slug : null,
            "interestName" => isset($this->data->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, "name") : null,
            "interestImage" => isset($this->data->image) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->image) : null,
        ];
    }

}