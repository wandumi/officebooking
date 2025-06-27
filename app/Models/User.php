<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        $this->loadMissing('roles');

        $userRoles = $this->roles->pluck('name')->map(fn ($role) => strtolower($role));

        $roles = collect((array) $role)->map(fn ($r) => strtolower($r));

        return $userRoles->intersect($roles)->isNotEmpty();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission($permission)
    {
        $rolePermissions = $this->roles
            ->flatMap->permissions
            ->pluck('name');

        $directPermissions = $this->permissions->pluck('name');

        return $rolePermissions->merge($directPermissions)->contains($permission);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


}
