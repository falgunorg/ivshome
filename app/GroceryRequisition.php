<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroceryRequisition extends Model {

    use HasFactory;
    use SoftDeletes;

    protected $table = 'grocery_requisitions';
    protected $dates = ['deleted_at'];
    protected $fillable = ['requisition_no', 'user_id ', 'requested_date', 'status', 'approved_by', 'approved_at', 'remarks'];

    public function items() {
        return $this->hasMany(GroceryRequisitionItem::class, 'grocery_requisition_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
