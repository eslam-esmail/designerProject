<?php

namespace OlaHub\DesignerCorner\Additional\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('notTemp', function ($query) {
            $query->where(function ($q) {
                $q->where(function ($temp) {
                    $temp->where('invited_by', '>', '0');
                    $temp->whereNotNull('invitation_accepted_date');
                });
                $q->orWhere(function ($temp) {
                    $temp->where(function($tempNull){
                        $tempNull->where('invited_by', "0");
                        $tempNull->orWhereNull('invited_by');
                    });
                    $temp->whereNull('invitation_accepted_date');
                });
            });
        });
    }

    protected $table = 'users';

}
