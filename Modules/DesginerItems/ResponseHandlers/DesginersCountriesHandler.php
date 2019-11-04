<?php

namespace OlaHub\DesignerCorner\DesginerItems\Handlers;

use OlaHub\DesignerCorner\Additional\Models\Country;
use League\Fractal;

class DesginersCountriesHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Country $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $this->return = [
            "value" => isset($this->data->id) ? $this->data->id : 0,
            "text" => isset($this->data->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, 'name') : null,
        ];
    }

}
