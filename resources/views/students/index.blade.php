<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Management') }}
            </h2>
            <a href="{{ route('students.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm font-medium">
                <i class="fas fa-plus mr-2"></i> Add New Student
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <form action="{{ route('students.index') }}" method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID or phone..." class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors text-sm font-medium">
                    Search
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Student ID</th>
                        <th class="px-6 py-3">Full Name</th>
                        <th class="px-6 py-3">Course / Batch</th>
                        <th class="px-6 py-3">Fees (Paid/Total)</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            {{ $student->student_id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mr-3 font-bold">
                                    {{ substr($student->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $student->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900">{{ $student->course->course_name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $student->batch->batch_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-green-600">₹{{ number_format($student->paid_fee, 2) }}</span>
                                <span class="text-xs text-gray-400">of ₹{{ number_format($student->total_fee, 2) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($student->status)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('students.edit', $student) }}" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('fee-receipts.create', ['student_id' => $student->id]) }}" class="text-orange-600 hover:text-orange-900 p-2 rounded-lg hover:bg-orange-50 transition-colors" title="Collect Fees">
                                <i class="fas fa-indian-rupee-sign"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-slash text-4xl mb-4 opacity-20"></i>
                                <p>No students found in the records.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $students->links() }}
        </div>
    </div>
</x-app-layout>
