<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cabinet extends Model {

    protected $fillable = ['title', 'location', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(Item::class);
    }
     public function drawers() {
        return $this->hasMany(Drawer::class);
    }
}
