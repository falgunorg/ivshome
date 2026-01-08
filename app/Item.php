<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {

    protected $fillable = ['category_id', 'name', 'user_id', 'description', 'condition', 'location', 'price', 'image', 'qty'];
    protected $hidden = ['created_at', 'updated_at'];

    public function getShowPhotoAttribute() { // Changed from getImagePathAttribute
        if (!$this->image) {
            return asset('upload/no-image.png');
        }
        return asset('upload/items/' . $this->image);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
