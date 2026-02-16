<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_no',
        'first_name',
        'last_name',
        'department',
        'section',
        'status',
        'photo_path',
    ];

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
