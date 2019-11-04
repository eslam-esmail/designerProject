<?php

namespace OlaHub\DesignerCorner\Additional\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Interest extends Eloquent {

    protected $connection = 'mongo';
    protected $collection = 'interests';

}
