<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FeeReceipt;
use App\Models\Student;

class FeeReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $receipts = FeeReceipt::with('student')->latest()->paginate(10);
        return view('fee_receipts.index', compact('receipts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $students = Student::active()->get(); // Assuming an active scope
        $selected_student = null;
        if ($request->has('student_id')) {
            $selected_student = Student::find($request->student_id);
        }
        return view('fee_receipts.create', compact('students', 'selected_student'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'required|string',
            'invoice_date' => 'required|date',
        ]);

        \DB::transaction(function () use ($request) {
            $student = Student::findOrFail($request->student_id);
            
            // Generate Invoice No
            $last_receipt = FeeReceipt::latest()->first();
            $next_no = $last_receipt ? (int)substr($last_receipt->invoice_no, 4) + 1 : 1001;
            $invoice_no = 'INV-' . $next_no;

            // Create Receipt
            $receipt = FeeReceipt::create([
                'invoice_no' => $invoice_no,
                'student_id' => $student->id,
                'amount' => $request->amount,
                'final_amount' => $request->amount,
                'payment_mode' => $request->payment_mode,
                'invoice_date' => $request->invoice_date,
            ]);

            // Create Payment
            $receipt->student->payments()->create([
                'receipt_id' => $receipt->id,
                'student_id' => $student->id,
                'amount' => $request->amount,
                'payment_date' => $request->invoice_date,
                'remarks' => $request->remarks,
            ]);

            // Update Student
            $student->paid_fee += $request->amount;
            $student->due_fee -= $request->amount;
            $student->save();
        });

        return redirect()->route('fee-receipts.index')->with('success', 'Fee collected successfully!');
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
