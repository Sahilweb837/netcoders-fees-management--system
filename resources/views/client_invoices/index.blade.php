<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Client Invoices') }}
            </h2>
            <a href="{{ route('client-invoices.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                <i class="fas fa-plus mr-2"></i> New Client Invoice
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Invoice No</th>
                        <th class="px-6 py-3">Client Details</th>
                        <th class="px-6 py-3">Service</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">
                            {{ $invoice->invoice_no }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $invoice->client_name }}</div>
                            <div class="text-xs text-gray-500">{{ $invoice->client_phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate text-gray-600">{{ $invoice->service_description }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">₹{{ number_format($invoice->total_amount, 2) }}</div>
                            <div class="text-xs text-gray-400">Mode: {{ $invoice->payment_mode }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $invoice->invoice_date }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="#" class="text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Print">
                                <i class="fas fa-print"></i>
                            </a>
                            <a href="{{ route('client-invoices.edit', $invoice) }}" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-invoice-dollar text-4xl mb-4 opacity-20"></i>
                                <p>No client invoices found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $invoices->links() }}
        </div>
    </div>
</x-app-layout>
