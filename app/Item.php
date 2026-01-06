<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {

    protected $fillable = ['category_id', 'name', 'description', 'condition', 'location', 'price', 'image', 'qty'];
    protected $hidden = ['created_at', 'updated_at'];

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
