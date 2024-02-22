<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'price', 'description', 'category_id','published','sub_category_id', 
    ];
    
    public function category():BelongsTo {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function colors():HasMany {
        return $this->hasMany(ProductColor::class, 'product_id');
    }
    
    public function sizes():HasMany{
        return $this->hasMany(Size::class, 'product_id');
    }
    
    public function images():HasMany{
        return $this->hasMany(Image::class, 'product_id');
    }
    
    public function orderItemsProduct():HasMany{
        return $this->hasMany(OrderItem::class);
    }
}
