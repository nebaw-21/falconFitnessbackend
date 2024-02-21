<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductColor extends Model
{
    use HasFactory;
    protected $fillable = [
        'color_id', 'product_id','published'
    ];
    
    public function category():BelongsTo {
        return $this->belongsTo(product::class, 'product_id');
    }
    
    public function sizes():HasMany{
        return $this->hasMany(size::class,'product_color_id');
    }
    
    public function images():HasMany{
        return $this->hasMany(Image::class, 'product_color_id');
    }
    
    public function colorName():HasOne{
        return $this->hasOne(color::class,'color_id');
    }
}
