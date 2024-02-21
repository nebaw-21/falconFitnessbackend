<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;
    protected $fillable =['product_color_id', 'product_id','image','published'];

public function color():BelongsTo{
    return $this->belongsTo(ProductColor::class, 'product_color_id');
}

}
