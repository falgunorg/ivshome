<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grocery extends Model {

    protected $fillable = ['name', 'qty', 'unit', 'category', 'image', 'min_stock'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
