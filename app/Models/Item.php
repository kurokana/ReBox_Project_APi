<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'box_id', 'category_id', 'name', 'description', 'quantity', 'image'
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}