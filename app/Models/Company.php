<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'email',
        'logo',
        'website',
        'user_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'company_id', 'id')->with('tasks');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'company_id', 'id')->with('candidates');
    }

    public function candidates()
    {
        return $this->hasOneThrough(Candidate::class, Job::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Employee::class, 'company_id', 'employee_id', 'id', 'id');
    }
}
