<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'phone',
        'bio',
        'address',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function boxes()
    {
        return $this->hasMany(Box::class);
    }

    // Transactions where user is seller (pengguna)
    public function salesTransactions()
    {
        return $this->hasMany(Transaction::class, 'pengguna_id');
    }

    // Transactions where user is collector (pengepul)
    public function collectorTransactions()
    {
        return $this->hasMany(Transaction::class, 'pengepul_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Check if user is admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Check if user is pengepul
    public function isPengepul()
    {
        return $this->role === 'pengepul';
    }

    // Check if user is pengguna
    public function isPengguna()
    {
        return $this->role === 'pengguna';
    }
}
