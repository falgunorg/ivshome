<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemPurchase extends Model {

    protected $table = 'item_purchase';
    protected $fillable = ['item_id', 'supplier_id', 'user_id', 'qty', 'date'];
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
