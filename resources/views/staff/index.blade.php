<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Staff Management') }}
            </h2>
            <a href="{{ route('staff.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                <i class="fas fa-user-plus mr-2"></i> Add New Staff
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Staff Name</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Salary</th>
                        <th class="px-6 py-3">Branch</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-3 font-bold">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div class="font-medium text-gray-900">{{ $member->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">{{ $member->role }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900">{{ $member->phone }}</div>
                            <div class="text-xs text-gray-500">{{ $member->email }}</div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            ₹{{ number_format($member->salary, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $member->branch->branch_name ?? 'Head Office' }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('staff.edit', $member) }}" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors" title="Pay Salary">
                                <i class="fas fa-money-bill-wave"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users-slash text-4xl mb-4 opacity-20"></i>
                                <p>No staff members found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $staff->links() }}
        </div>
    </div>
</x-app-layout>
