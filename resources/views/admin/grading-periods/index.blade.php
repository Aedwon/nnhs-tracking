<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Grading Periods & Deadlines') }}
            </h2>
            <a href="{{ route('admin.grading-periods.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                + New Period
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($periods as $period)
                        <div class="border rounded-xl p-6 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ $period->name }}</h3>
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-bold {{ $period->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $period->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500 space-y-2">
                                <p>Start: {{ $period->start_date }}</p>
                                <p>End: {{ $period->end_date }}</p>
                            </div>
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Associated Deadlines</h4>
                                @foreach($period->deadlines as $deadline)
                                    <div class="flex items-center text-sm font-medium text-indigo-600">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"></path>
                                        </svg>
                                        {{ $deadline->deadline_at->format('M d, Y h:i A') }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>