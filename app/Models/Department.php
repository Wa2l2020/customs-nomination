<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'location', 'parent_id'];

    // Get General Departments (Children)
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    // Get Central Department (Parent)
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }
    
    public function manager()
    {
        return $this->hasOne(User::class, 'department_id');
    }
}
