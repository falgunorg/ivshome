<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Remove the HasRichText import

class Recipe extends Model {

    use HasFactory;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    // Remove 'use HasRichText;'

    protected $fillable = ['title', 'instructions', 'note', 'image'];

    public function getShowPhotoAttribute() { // Changed from getImagePathAttribute
        if (!$this->image) {
            return asset('upload/no-image.png');
        }
        return asset('upload/recipes/' . $this->image);
    }

    // Remove the $richTextAttributes array
}
