<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledMaintenance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_date' => 'date',
        'completion_date' => 'date',
    ];

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
