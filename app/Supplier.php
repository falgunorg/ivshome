<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'address', 'email', 'phone'];
    protected $hidden = ['created_at', 'updated_at'];
}
