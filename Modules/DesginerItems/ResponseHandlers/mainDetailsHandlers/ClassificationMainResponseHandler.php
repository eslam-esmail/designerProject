<?php

namespace OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers;

use OlaHub\DesignerCorner\DesginerItems\Models\Classification;
use League\Fractal;

class ClassificationMainResponseHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Classification $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $className = isset($this->data->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, 'name') : NULL;
        $this->return = [
            "id" => isset($this->data->id) ? $this->data->id : 0,
            "mainSlug" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkSlug($this->data, 'class_slug', $className),
            "mainName" => $className,
            "mainLogo" => false,
            "mainBanner" => false,
        ];
    }
}
