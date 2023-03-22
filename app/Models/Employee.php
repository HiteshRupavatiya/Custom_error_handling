<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'joining_date',
        'company_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'employee_id', 'id')->with('tasks');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
