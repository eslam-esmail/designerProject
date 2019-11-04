<?php

namespace OlaHub\DesignerCorner\commonData\Models;
use Illuminate\Database\Eloquent\Model;

class CommonMySQLModel extends Model
{
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }
    
    public static function boot() {
        parent::boot();
    }

}
