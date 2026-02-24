<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSale extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'item_sale';
    protected $fillable = ['item_id', 'customer_id', 'user_id', 'qty', 'date', 'status'];
    protected $hidden = ['created_at', 'updated_at'];

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
