<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'number', 'status', 'total', 
        'payment_method', 'payment_status', 'notes',
        'shipping_name', 'shipping_phone', 'shipping_address',
        'shipping_city', 'shipping_country'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}