<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {

    protected $fillable = [
        'user_id', 'item_type', 'name', 'description',
        'image', 'qty', 'condition', 'warranty_date', 'date_of_purchase',
        'date_of_expiry', 'location_id', 'cabinet_id', 'drawer_id'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected static function booted() {
        static::creating(function ($item) {
            // 1. Get the incrementing number
            $lastItem = static::orderBy('id', 'desc')->first();
            $nextId = $lastItem ? $lastItem->id + 1 : 1;
            $paddedId = str_pad($nextId, 5, '0', STR_PAD_LEFT);

            // 2. Fetch relationship codes
            // We use 'optional' to prevent errors if a relation is missing
            $typeCode = optional($item->itemType)->code ?? 'NA';
            $locationCode = optional($item->itemLocation)->code ?? 'NA';
            $cabinetCode = optional($item->cabinet)->code ?? 'NA';
            $drawerTitle = optional($item->drawer)->title ?? 'NA';

            // 3. Combine into the final serial format
            // Format: itemType->code - itemLocation->code - cabinet->code - drawer->title - serial_number
            $item->serial_number = "{$locationCode}-{$cabinetCode}-{$drawerTitle}-{$typeCode}-{$paddedId}";
        });
    }

//    protected static function booted() {
//        static::creating(function ($item) {
//            // Get the last ID or set to 0 if table is empty
//            $lastItem = static::orderBy('id', 'desc')->first();
//            $nextId = $lastItem ? $lastItem->id + 1 : 1;
//
//            // str_pad(string, length, padding_string, type)
//            $item->serial_number = str_pad($nextId, 5, '0', STR_PAD_LEFT);
//        });
//    }

    public function getShowPhotoAttribute() { // Changed from getImagePathAttribute
        if (!$this->image) {
            return asset('upload/no-image.png');
        }
        return asset('upload/items/' . $this->image);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function itemLocation() {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function cabinet() {
        return $this->belongsTo(Cabinet::class);
    }

    public function drawer() {
        return $this->belongsTo(Drawer::class);
    }

    public function itemType() {
        return $this->belongsTo(ItemType::class, 'item_type');
    }
}
