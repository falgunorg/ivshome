<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cabinet extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'location_id', 'user_id', 'code'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // App\Cabinet.php
    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function drawers() {
        return $this->hasMany(Drawer::class);
    }

    public function items() {
        return $this->hasMany(Item::class);
    }
}
