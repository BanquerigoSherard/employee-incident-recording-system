<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'incident_date',
        'description',
        'attachment_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'incident_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
