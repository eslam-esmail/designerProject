<?php

namespace OlaHub\DesignerCorner\DesginerItems\Handlers;

use OlaHub\DesignerCorner\Additional\Models\Desginers;
use League\Fractal;

class DesginersHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;

    public function transform(Desginers $data) {
        $this->data = $data;
        $this->setDefaultData();
        return $this->return;
    }

    private function setDefaultData() {
        $user = app('session')->get('tempID') ? \OlaHub\DesignerCorner\Additional\Models\UserMongo::where('user_id', app('session')->get('tempID'))->first() : false;
        $this->return = [
            "desginerId" => isset($this->data->id) ? $this->data->id : 0,
            "desginerSlug" => isset($this->data->designer_slug) ? $this->data->designer_slug : null,
            "desginerBrandName" => isset($this->data->brand_name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($this->data, 'brand_name') : null,
            "desginerLogo" => isset($this->data->logo_ref) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($this->data->logo_ref) : null,
            "followed" => $user && isset($user->followed_designers) && is_array($user->followed_designers) && in_array($this->data->id, $user->followed_designers) ? true : false
        ];
    }

}
