<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Damage extends Model {
    
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['item_id', 'user_id', 'qty', 'date', 'remarks', 'image', 'status'];

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
