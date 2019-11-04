<?php

namespace OlaHub\DesignerCorner\DesignerSliders\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class DesignerSliders extends Eloquent {

    protected $connection = 'mongo';
    protected $collection = 'sliders';

}
