<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'requested_clock_in',
        'requested_clock_out',
        'requested_breaks',
        'reason',
        'status',
        'approver_id',
        'decided_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => '承認待ち',
            'approved' => '承認済み',
            'rejected' => '却下',
            default => $this->status,
        };
    }

}
