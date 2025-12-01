<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomination extends Model
{
    protected $fillable = [
        'employee_name', 'job_number', 'job_title', 'phone', 'email',
        'central_dept_id', 'general_dept_id', 'category',
        'answers', 'attachments', 'status', 'score_avg'
    ];

    protected $casts = [
        'answers' => 'array',
        'attachments' => 'array',
    ];

    public function centralDept()
    {
        return $this->belongsTo(Department::class, 'central_dept_id');
    }

    public function generalDept()
    {
        return $this->belongsTo(Department::class, 'general_dept_id');
    }
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
