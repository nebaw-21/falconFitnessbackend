<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    use HasFactory;
    protected $fillable =[
        'sub_category'
    ];

public function products():HasMany{
    return $this->hasMany(product::class, 'product_id');
}
}
