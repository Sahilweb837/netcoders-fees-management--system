<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeReceipt extends Model
{
    protected $fillable = [
        'invoice_no', 'student_id', 'amount', 'discount', 'tax', 'final_amount', 'payment_mode', 'payment_status', 'invoice_date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
