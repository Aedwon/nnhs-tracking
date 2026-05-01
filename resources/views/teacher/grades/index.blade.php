<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Grade Submissions') }}
            </h2>
            <a href="{{ route('teacher.dashboard') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted At</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($grades as $grade)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $grade->student->last_name ?? 'N/A' }}, {{ $grade->student->first_name ?? '' }}</div>
                                        <div class="text-xs text-gray-400 font-mono">{{ $grade->student->id_number ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $grade->subject->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $grade->grade }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($grade->is_finalized)
                                            <span class="px-2 py-1 text-xs rounded-full font-bold bg-emerald-100 text-emerald-800">Finalized</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full font-bold bg-amber-100 text-amber-800">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $grade->submitted_at ? $grade->submitted_at->format('M d, Y h:i A') : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                        No grades submitted yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6">
                    {{ $grades->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>