<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'plain_password', 'role', 
        'job_number', 'phone', 'department_id', 'last_login_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getRoleLabelAttribute()
    {
        $roles = [
            'central' => 'رئيس إدارة مركزية',
            'general' => 'مدير عام',
            'committee' => 'عضو لجنة',
            'chairman' => 'رئيس اللجنة العليا',
        ];
        return $roles[$this->role] ?? $this->role;
    }
}
