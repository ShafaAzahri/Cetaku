<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

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
        $isValid = $this->api_token && now()->lt($this->token_expires_at);
        
        Log::debug('Token validity check', [
            'user_id' => $this->id,
            'has_token' => (bool) $this->api_token,
            'token_expires_at' => $this->token_expires_at,
            'is_valid' => $isValid
        ]);
        
        return $isValid;
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        $hasRole = $this->role && $this->role->nama_role === $roleName;
        
        Log::debug('Role check', [
            'user_id' => $this->id,
            'role_id' => $this->role_id,
            'role_name' => $this->role ? $this->role->nama_role : null,
            'checked_role' => $roleName,
            'has_role' => $hasRole
        ]);
        
        return $hasRole;
    }
    
    /**
     * Check if user is a regular user
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
            return '/superadmin/dashboard';
        } elseif ($this->isAdmin()) {
            return '/admin/dashboard';
        } else {
            return '/user/welcome';
        }
    }
}