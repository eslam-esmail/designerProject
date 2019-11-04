<?php

namespace OlaHub\DesignerCorner\Additional\Models;

use Illuminate\Database\Eloquent\Model;

class CelebrationParticipant extends Model {

    protected static function boot() {
        parent::boot();
    }

    protected $table = 'celebration_participants';
    
    public function celebration() {
        return $this->belongsTo('OlaHub\DesignerCorner\Additional\Models\Celebration','celebration_id');
    }

}
