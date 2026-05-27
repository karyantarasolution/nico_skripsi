<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceOrder extends Model
{
    use HasFactory;

    protected $casts = [
        'complaint_date' => 'date',
        'completion_date' => 'date',
        'sla_deadline' => 'datetime',
        'cost' => 'integer',
        'estimated_cost' => 'decimal:2',
    ];

    protected $fillable = [
        'ownership_id',
        'reporter_id',
        'technician_id',
        'complaint_date',
        'complaint_title',
        'complaint_description',
        'complaint_photo',
        'priority',
        'status',
        'sla_deadline',
        'sla_status',
        'completion_date',
        'rating',
        'review',
        'cost',
        'payment_status',
        'estimated_cost',
        'estimated_description',
        'cost_status',
        'cost_approved_by',
        'cost_approved_at',
        'scheduled_date',
        'admin_notes',
        'rejection_reason',
    ];

    public function ownership()
    {
        return $this->belongsTo(Ownership::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function costApprover()
    {
        return $this->belongsTo(User::class, 'cost_approved_by');
    }

    public function scopeWithSlaViolation($query)
    {
        return $query->where('sla_status', 'violated');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
