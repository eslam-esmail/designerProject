<?php

namespace OlaHub\DesignerCorner\Additional\Models;

use Illuminate\Database\Eloquent\Model;

class Celebration extends Model {

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('checkUserInCelebration', function ($query) {
            $query->whereHas("participants", function ($q) {
                $q->where("user_id", app('session')->get("tempID"));
                $q->where("is_approved", "1");
            });
            $query->where("celebration_status", "1");
        });
    }

    protected $table = 'celebrations';

    public function participants() {
        return $this->hasMany('OlaHub\DesignerCorner\Additional\Models\CelebrationParticipant', 'celebration_id');
    }

}
