<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroceryRequisitionItem extends Model {

    use HasFactory;
    use SoftDeletes;

    protected $table = 'grocery_requisition_items';
    protected $dates = ['deleted_at'];
    protected $fillable = ['grocery_requisition_id', 'grocery_id', 'qty_requested', 'qty_received'];

    public function grocery() {
        return $this->belongsTo(Grocery::class);
    }
}
