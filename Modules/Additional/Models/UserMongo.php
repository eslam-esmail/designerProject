<?php

namespace OlaHub\DesignerCorner\Additional\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserMongo extends Eloquent {

    protected $connection = 'mongo';
    protected $primaryKey = 'user_id';
    protected $collection = 'users';

}
