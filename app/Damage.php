<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Damage extends Model {

    protected $fillable = ['item_id', 'user_id', 'qty', 'date', 'remarks', 'image', 'status'];

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
