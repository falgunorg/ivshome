<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemPurchase extends Model {
    
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'item_purchase';
    protected $fillable = ['item_id', 'supplier_id', 'user_id', 'qty', 'date', 'status'];
    protected $hidden = ['created_at', 'updated_at'];

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
