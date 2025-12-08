<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WasteSale extends Model
{
    protected $fillable = [
        'user_id',
        'waste_type',
        'weight',
        'price_per_kg',
        'total_price',
        'description',
        'photo_path',
        'status',
        'admin_notes',
        'approved_at',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_price' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
