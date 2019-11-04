<?php

namespace OlaHub\DesignerCorner\Additional\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model {

    protected $table = 'user_sessions';

    public function user() {
        return $this->belongsTo('OlaHub\DesignerCorner\Additional\Models\User');
    }

}
