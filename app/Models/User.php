<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'api_token',
        'token_expires_at',
        'role_id',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'token_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user's token is valid.
     *
     * @return bool
     */
    public function isTokenValid()
    {
        return $this->api_token && now()->lt($this->token_expires_at);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->nama_role === $roleName;
    }
    
    /**
     * Check if user is a user (normal user role)
     * 
     * @return bool
     */
    public function isUser()
    {
        return $this->hasRole('user');
    }
    
    /**
     * Check if user is an admin
     * 
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Check if user is a super admin
     * 
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }
    
    /**
     * Get user's redirect path after login
     * 
     * @return string
     */
    public function getRedirectPath()
    {
        if ($this->isSuperAdmin()) {
            return '/superadmin/AdminDashboard';
        } elseif ($this->isAdmin()) {
            return '/dashboard';
        } else {
            return '/welcome';
        }
    }
}