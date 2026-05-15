<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daily Attendance') }}
            </h2>
            <div class="flex items-center gap-3">
                <form action="{{ route('attendance.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="date" name="date" value="{{ $date }}" class="rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm" onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="attendance_date" value="{{ $date }}">
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Student Name</th>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-center">Fine (If Any)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        @php
                            $record = $attendance->where('student_id', $student->id)->first();
                            $status = $record ? $record->status : 'present';
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $student->full_name }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $student->student_id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="present" {{ $status == 'present' ? 'checked' : '' }} class="text-green-600 focus:ring-green-500">
                                        <span class="ml-2 text-xs font-medium text-green-700">P</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="absent" {{ $status == 'absent' ? 'checked' : '' }} class="text-red-600 focus:ring-red-500">
                                        <span class="ml-2 text-xs font-medium text-red-700">A</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="late" {{ $status == 'late' ? 'checked' : '' }} class="text-yellow-600 focus:ring-yellow-500">
                                        <span class="ml-2 text-xs font-medium text-yellow-700">L</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="leave" {{ $status == 'leave' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-xs font-medium text-blue-700">V</span>
                                    </label>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="number" name="attendance[{{ $student->id }}][fine]" value="{{ $record ? $record->fine_amount : 0 }}" class="w-20 rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm text-center">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-8 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors font-medium shadow-sm">
                    Save Attendance
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
