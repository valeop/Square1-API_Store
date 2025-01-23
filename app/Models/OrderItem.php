<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_variant_id', 'quantity', 'price'
    ];

    public function productVariant() {
        return $this->belongsTo(ProductVariant::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
