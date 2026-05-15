<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Attendance;
use App\Models\Student;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $attendance = Attendance::with('student')->whereDate('attendance_date', $date)->get();
        $students = Student::active()->get();
        
        return view('attendance.index', compact('attendance', 'students', 'date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
        ]);

        foreach ($request->attendance as $student_id => $data) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student_id,
                    'attendance_date' => $request->attendance_date,
                ],
                [
                    'status' => $data['status'],
                    'fine_amount' => $data['fine'] ?? 0,
                ]
            );
        }

        return redirect()->back()->with('success', 'Attendance updated successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
