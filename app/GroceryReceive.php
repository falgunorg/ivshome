<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroceryReceive extends Model {

    use HasFactory;
    use SoftDeletes;

    protected $table = 'grocery_receives';
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id', 'grocery_requisition_item_id ', 'grocery_id ', 'received_qty', 'current_stock', 'purchase_date', 'expiry_date', 'lot_number'];
}
