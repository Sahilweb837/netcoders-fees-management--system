<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Fee Receipts History') }}
            </h2>
            <a href="{{ route('fee-receipts.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm font-medium">
                <i class="fas fa-plus mr-2"></i> Collect New Fee
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Invoice No</th>
                        <th class="px-6 py-3">Student Name</th>
                        <th class="px-6 py-3 text-center">Amount</th>
                        <th class="px-6 py-3 text-center">Mode</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">
                            {{ $receipt->invoice_no }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-700">
                            {{ $receipt->student->full_name }}
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-green-600">
                            ₹{{ number_format($receipt->final_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">{{ $receipt->payment_mode }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">{{ $receipt->payment_status }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="#" class="text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Print Receipt">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-receipt text-4xl mb-4 opacity-20"></i>
                                <p>No payment records found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $receipts->links() }}
        </div>
    </div>
</x-app-layout>
