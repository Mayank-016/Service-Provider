<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
    /**
     * Get the user's role as a readable string.
     */
    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            1 => 'user',
            2 => 'provider',
            default => 'user',
        };
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class)->where('status', '!=', BOOKING_STATUS_CANCELLED);
    }

    public function providerBookings()
    {
        return $this->hasMany(Booking::class, 'provider_id')->where('status', '!=', BOOKING_STATUS_CANCELLED);
    }

}
