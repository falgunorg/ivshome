<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drawer extends Model {
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['title', 'cabinet_id'];

    public function cabinet() {
        return $this->belongsTo(Cabinet::class);
    }

    public function items() {
        return $this->hasMany(Item::class);
    }
}
