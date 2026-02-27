<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teacher Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Profile -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-8 flex items-center bg-gradient-to-r from-indigo-50 to-white">
                    <div
                        class="h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-6">
                        <h3 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                        <div class="flex space-x-4 mt-1 text-sm text-gray-500">
                            <span>Compliance Score: <b
                                    class="{{ $compliance < 100 ? 'text-rose-500' : 'text-emerald-500' }}">{{ $compliance }}%</b></span>
                            <span>•</span>
                            <span>Total Uploads: <b>{{ $totalUploaded }}</b></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Deadlines Tracker -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-8">
                        <div class="p-6 border-b border-gray-100 bg-gray-50">
                            <h3 class="font-bold text-gray-800 flex items-center">
                                <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Upcoming Deadlines
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @forelse($deadlines as $deadline)
                                <div class="border-l-4 border-indigo-400 pl-4 py-2">
                                    <div class="text-xs text-gray-400 font-bold uppercase">
                                        {{ $deadline->gradingPeriod->name }}</div>
                                    <div class="text-sm font-bold text-gray-800 mt-1">
                                        {{ $deadline->deadline_at->diffForHumans() }}</div>
                                    <div class="text-xs text-gray-500">{{ $deadline->deadline_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 text-center py-4 italic">No active deadlines found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">My Recent Submissions</h3>
                            <a href="{{ route('teacher.grades.create') }}"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                                + New Submission
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Subject</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Grade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentGrades as $grade)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $grade->student_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $grade->subject_code }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">{{ $grade->grade }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full font-bold {{ $grade->submission_status == 'Late' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                                    {{ $grade->submission_status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>