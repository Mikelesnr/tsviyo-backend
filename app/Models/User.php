<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Enums\UserRole;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Profile relationship for riders.
     */
    public function riderProfile()
    {
        return $this->hasOne(Rider::class);
    }

    /**
     * Profile relationship for drivers.
     */
    public function driverProfile()
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * Profile image.
     */
    public function profileImage()
    {
        return $this->hasOne(Image::class);
    }
}
