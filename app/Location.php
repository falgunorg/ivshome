<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model {
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'user_id','code'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function cabinets() {
        return $this->hasMany(Cabinet::class, 'location_id');
    }

    public function items() {
        return $this->hasMany(Item::class, 'location_id');
    }
}
