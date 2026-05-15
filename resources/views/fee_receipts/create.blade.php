<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Collect Fee') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-semibold text-gray-800">Fee Collection Form</h3>
                <p class="text-sm text-gray-500">Enter payment details to generate a receipt.</p>
            </div>

            <form action="{{ route('fee-receipts.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Student Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Student</label>
                        <select name="student_id" id="student_id" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm" required>
                            <option value="">-- Choose Student --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ ($selected_student && $selected_student->id == $student->id) ? 'selected' : '' }} data-due="{{ $student->due_fee }}">
                                    {{ $student->full_name }} ({{ $student->student_id }}) - Due: ₹{{ number_format($student->due_fee, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Collection Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm" required>
                    </div>

                    <!-- Payment Mode -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Mode</label>
                        <select name="payment_mode" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <option value="Cash">Cash</option>
                            <option value="Online">Online / UPI</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date</label>
                        <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm" required>
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Remarks (Optional)</label>
                        <input type="text" name="remarks" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 p-4 bg-gray-50 -mx-6 -mb-6">
                    <a href="{{ route('students.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm font-medium shadow-md">
                        Generate Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
