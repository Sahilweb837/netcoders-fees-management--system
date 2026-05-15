<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ERP Dashboard') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Students Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-lg bg-orange-50 text-orange-600 mr-4">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Total Students</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] }}</p>
            </div>
        </div>

        <!-- Staff Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-lg bg-blue-50 text-blue-600 mr-4">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Total Staff</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_staff'] }}</p>
            </div>
        </div>

        <!-- Courses Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-lg bg-green-50 text-green-600 mr-4">
                <i class="fas fa-book text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Total Courses</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
            </div>
        </div>

        <!-- Fees Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-lg bg-purple-50 text-purple-600 mr-4">
                <i class="fas fa-indian-rupee-sign text-2xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Total Fees</p>
                <p class="text-2xl font-bold text-gray-900">₹{{ number_format($stats['total_fees_collected'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Receipts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Fee Receipts</h3>
                <a href="#" class="text-sm font-medium text-orange-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">Invoice</th>
                            <th class="px-4 py-2">Student</th>
                            <th class="px-4 py-2">Amount</th>
                            <th class="px-4 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_receipts'] as $receipt)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $receipt->invoice_no }}</td>
                            <td class="px-4 py-2">{{ $receipt->student->full_name }}</td>
                            <td class="px-4 py-2 font-semibold">₹{{ number_format($receipt->final_amount, 2) }}</td>
                            <td class="px-4 py-2 text-xs">{{ $receipt->invoice_date }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">No recent transactions</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="#" class="flex flex-col items-center p-4 rounded-xl border border-gray-100 hover:border-orange-200 hover:bg-orange-50 transition-all group">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600 mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Add Student</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition-all group">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-money-check-dollar"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Collect Fee</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition-all group">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Client Invoice</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 rounded-xl border border-gray-100 hover:border-purple-200 hover:bg-purple-50 transition-all group">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Take Attendance</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
