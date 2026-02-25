<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroceryIssue extends Model {

    use HasFactory;
    use SoftDeletes;

    protected $table = 'grocery_issues';
    protected $dates = ['deleted_at'];
    protected $fillable = ['grocery_id', 'user_id', 'receive_id', 'issued_qty', 'issue_date', 'issued_to'];
}
