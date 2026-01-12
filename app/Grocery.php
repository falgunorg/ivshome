<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grocery extends Model {

    protected $fillable = ['user_id', 'item_name', 'quantity', 'unit'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
