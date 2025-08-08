<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use SoftDeletes, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role->name, $role);
        }

        return $this->role->name === $role;
    }

    public function assignRole($role)
    {
        if (is_numeric($role)) {
            $roleModel = Role::find($role);
        } else {
            $roleModel = Role::where('name', $role)->first();
        }

        if (!$roleModel) {
            throw new \Exception("Role tidak ditemukan.");
        }

        $this->role_id = $roleModel->id;
        $this->save();

        return $this;
    }
}
