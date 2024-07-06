<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrderDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'stock_id',
        'quantity',       
    ];
   public function order()
    {
        return $this->belongsTo('App\Models\UserOrder');
    }
    public function stock()
    {
        return $this->belongsTo('App\Models\Stock');
    }
    public function product()
    {
        return $this->hasOneThrough('App\Models\Product', 'App\Models\Stock');
    }
   
}