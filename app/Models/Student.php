<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_id', 'full_name', 'father_name', 'phone', 'email', 'address', 'photo', 
        'course_id', 'batch_id', 'admission_date', 'total_fee', 'discount', 'paid_fee', 'due_fee', 'status'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function receipts()
    {
        return $this->hasMany(FeeReceipt::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
