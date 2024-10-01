<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'images',
        'description',
        'price',
        'category_id',
    ];

    protected $casts = [
        'images' => 'array'
    ];

    public function category() {
        return $this->belongsTo(Categorie::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
}
