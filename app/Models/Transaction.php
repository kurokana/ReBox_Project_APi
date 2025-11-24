<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_id',
        'pengguna_id',
        'pengepul_id',
        'status',
        'total_price',
        'admin_fee',
        'pengepul_earnings',
        'notes',
        'accepted_at',
        'completed_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_price' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'pengepul_earnings' => 'decimal:2',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function pengepul()
    {
        return $this->belongsTo(User::class, 'pengepul_id');
    }

    // Scope for pending transactions
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for accepted transactions
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    // Scope for completed transactions
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
