<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['name'];

    public function items() {
        return $this->hasMany(Item::class);
    }
}
