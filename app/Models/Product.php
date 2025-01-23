<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'other_attributes'
    ];

    protected $casts = [
        'other_attributes' => 'array'
    ];

    public function setOtherAttributesAttribute($value): void
    {
        $this->attributes['other_attributes'] = json_encode($value);
    }

    public function variant() {
        return $this->hasMany(ProductVariant::class);
    }
}
