<?php

namespace OlaHub\DesignerCorner\ModuleName\Handlers;

use OlaHub\DesignerCorner\ModuleName\Models\Example;
use League\Fractal;

class ExamplesHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Example $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $this->return = [
            "number" => isset($this->data->id) ? $this->data->id : 0,
        ];
    }

}
