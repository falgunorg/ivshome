<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grocery extends Model {
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'qty', 'unit', 'category', 'image', 'min_stock'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
