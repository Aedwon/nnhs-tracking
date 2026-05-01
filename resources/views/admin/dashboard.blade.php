<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Analytics Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Heatmap Header -->
            <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B] mb-8">
                <div class="p-8 flex items-center bg-eggshell/50">
                    <div class="h-20 w-20 bg-crimson flex items-center justify-center text-white border-2 border-navy shadow-[4px_4px_0_0_#0B132B]">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div class="ml-8">
                        <h3 class="text-3xl font-display font-bold text-navy tracking-tight uppercase">Principal's Monitoring Heatmap</h3>
                        <p class="text-crimson font-bold uppercase tracking-widest text-xs mt-1">Real-time Grade Submission Status</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-6 mb-8">
                <div class="flex space-x-6 px-4 border-2 border-navy bg-white p-4 shadow-[4px_4px_0_0_#0B132B]">
                    <div class="flex items-center space-x-2">
                        <div class="h-5 w-5 bg-navy border-2 border-navy"></div>
                        <span class="text-xs font-bold text-navy uppercase tracking-widest">Finalized</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="h-5 w-5 bg-crimson border-2 border-navy"></div>
                        <span class="text-xs font-bold text-navy uppercase tracking-widest">In Progress</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="h-5 w-5 bg-eggshell border-2 border-navy"></div>
                        <span class="text-xs font-bold text-navy uppercase tracking-widest">Not Started</span>
                    </div>
                </div>

                <a href="{{ route('admin.unlock-requests') }}" class="relative bg-white border-2 border-navy p-4 font-display font-bold text-navy uppercase tracking-widest text-xs hover:bg-navy hover:text-eggshell transition-all shadow-[4px_4px_0_0_#0B132B] hover:shadow-none hover:translate-y-0.5 hover:translate-x-0.5">
                    Unlock Requests
                    @if($pendingRequestsCount > 0)
                        <span class="absolute -top-3 -right-3 h-6 w-6 bg-crimson text-white border-2 border-navy flex items-center justify-center text-[10px]">
                            {{ $pendingRequestsCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Heatmap Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach($heatmapData as $data)
                    <div class="bg-white border-2 border-navy shadow-[4px_4px_0_0_#0B132B] flex flex-col">
                        <div class="p-5 border-b-2 border-navy bg-eggshell/30 flex justify-between items-center">
                            <div>
                                <h4 class="font-display font-bold text-navy text-xl uppercase tracking-widest">{{ $data['section'] }}</h4>
                                <p class="text-xs text-navy/60 font-mono uppercase mt-1">{{ $data['grade_level'] }}</p>
                            </div>
                            <div class="h-10 w-10 bg-white border-2 border-navy flex items-center justify-center text-lg font-display font-bold text-navy shadow-[2px_2px_0_0_#0B132B]">
                                {{ count($data['subjects']) }}
                            </div>
                        </div>
                        <div class="p-6 grid grid-cols-4 gap-4">
                            @foreach($data['subjects'] as $sub)
                                <div class="group relative">
                                    <div class="h-12 w-full border-2 border-navy transition-all duration-200 
                                        {{ $sub['status'] == 'submitted' ? 'bg-navy' : ($sub['status'] == 'progress' ? 'bg-crimson' : 'bg-eggshell') }} hover:-translate-y-1 hover:shadow-[4px_4px_0_0_#0B132B]">
                                    </div>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-56 p-4 bg-white border-2 border-navy text-navy text-xs invisible group-hover:visible z-10 shadow-[4px_4px_0_0_#0B132B]">
                                        <div class="font-display font-bold mb-2 uppercase tracking-widest text-sm">{{ $sub['subject'] }}</div>
                                        <div class="text-xs font-mono mb-3">Teacher: {{ $sub['teacher'] }}</div>
                                        <div class="flex items-center space-x-2">
                                            <span class="h-3 w-3 border border-navy {{ $sub['status'] == 'submitted' ? 'bg-navy' : ($sub['status'] == 'progress' ? 'bg-crimson' : 'bg-eggshell') }}"></span>
                                            <span class="font-bold uppercase tracking-widest text-[10px]">{{ $sub['status'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-auto p-4 bg-eggshell/30 border-t-2 border-navy flex justify-center">
                            <button class="text-xs font-bold text-navy uppercase tracking-widest hover:text-crimson hover:bg-navy p-2 transition-all">View Full Section Report</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>