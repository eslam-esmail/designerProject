<?php

namespace OlaHub\DesignerCorner\DesignerPlaceholders\Models;

use OlaHub\DesignerCorner\commonData\Models\CommonMySQLModel;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class DesignerPlaceholders extends Eloquent {

        protected $connection = 'mongo';
        protected $collection = 'placeholders';

}
