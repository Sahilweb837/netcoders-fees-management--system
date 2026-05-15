<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Student;
use App\Models\Staff;
use App\Models\FeeReceipt;
use App\Models\Course;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_staff' => Staff::count(),
            'total_courses' => Course::count(),
            'total_fees_collected' => FeeReceipt::sum('final_amount'),
            'recent_receipts' => FeeReceipt::with('student')->latest()->take(5)->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
