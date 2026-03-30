<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Konstanta role
    const ROLE_ADMIN = 'admin';
    const ROLE_WRITER = 'writer';
    const ROLE_UPLOADER = 'uploader';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // ditambahkan agar bisa diisi saat create/update
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Helper methods untuk pengecekan role
    public function isAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    public function isWriter()
    {
        return $this->role == self::ROLE_WRITER;
    }

    public function isUploader()
    {
        return $this->role == self::ROLE_UPLOADER;
    }

    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

}
