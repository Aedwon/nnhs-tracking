<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Analytics Dashboard') }}
            </h2>
            <div class="text-sm font-medium text-gray-500">
                Compliance Rate: <span class="text-indigo-600">{{ $complianceRate }}%</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Total Grade Submissions</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $totalGrades }}</div>
                    <div class="mt-4 text-xs text-gray-400">Successfully recorded in system</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">On-Time Submissions</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $onTimeGrades }}</div>
                    <div class="mt-4 text-xs text-emerald-500">Great job! Teachers are complying.</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-rose-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Late Submissions</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $lateGrades }}</div>
                    <div class="mt-4 text-xs text-rose-500">Requiring immediate attention</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Frequent Late Uploaders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Top Frequent Late Uploaders</h3>
                    </div>
                    <div class="p-6">
                        @if($frequentLateUploaders->count() > 0)
                            <div class="flow-root">
                                <ul role="list" class="-my-5 divide-y divide-gray-200">
                                    @foreach($frequentLateUploaders as $uploader)
                                        <li class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <span
                                                        class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-rose-100 text-rose-600 font-bold">
                                                        {{ strtoupper(substr($uploader->teacher->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $uploader->teacher->name }}
                                                    </p>
                                                    <p class="text-sm text-gray-500 truncate">
                                                        {{ $uploader->teacher->email }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                        {{ $uploader->late_count }} Late
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No late uploads recorded.</p>
                        @endif
                    </div>
                </div>

                <!-- Submission Trends -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Submissions per Period</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($gradingPeriods as $period)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-700">{{ $period->name }}</span>
                                        <span class="font-medium text-gray-900">{{ $period->grades_count }} Uploads</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full"
                                            style="width: {{ $totalGrades > 0 ? ($period->grades_count / $totalGrades) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>