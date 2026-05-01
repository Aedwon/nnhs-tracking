<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teacher Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B] mb-8">
                <div class="p-8 flex items-center bg-eggshell/50">
                    <div class="h-20 w-20 bg-navy flex items-center justify-center text-eggshell text-3xl font-display font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-8">
                        <h3 class="text-3xl font-display font-bold text-navy tracking-tight">{{ auth()->user()->name }}</h3>
                        <p class="text-navy/70 font-bold uppercase tracking-widest text-xs mt-1">Unified Personnel Dashboard</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar: Deadlines -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white border-2 border-navy shadow-[4px_4px_0_0_#0B132B]">
                        <div class="p-4 border-b-2 border-navy bg-crimson flex items-center justify-between">
                            <h3 class="font-display font-bold text-eggshell text-sm uppercase tracking-widest">Critical Deadlines</h3>
                            <span class="flex h-3 w-3 bg-white border-2 border-navy"></span>
                        </div>
                        <div class="p-5 space-y-4">
                            @forelse($deadlines as $deadline)
                                <div class="p-4 border-2 border-navy bg-eggshell/30">
                                    <div class="text-[10px] text-crimson font-bold uppercase tracking-widest mb-1">
                                        {{ $deadline->gradingPeriod->name }}
                                    </div>
                                    <div class="text-lg font-display font-bold text-navy leading-tight">
                                        {{ $deadline->deadline_at->diffForHumans() }}
                                    </div>
                                    <div class="text-xs text-navy/60 mt-2 font-mono uppercase">
                                        {{ $deadline->deadline_at->format('M d, Y') }}
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-navy/50 text-center py-4 font-mono uppercase tracking-widest">No active deadlines.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Main Content: Task Lists -->
                <div class="lg:col-span-3 space-y-8">
                    
                    <!-- Subjects to Grade (Teacher Role) -->
                    <div class="bg-white border-2 border-navy shadow-[4px_4px_0_0_#0B132B]">
                        <div class="p-6 border-b-2 border-navy flex justify-between items-center bg-eggshell/30">
                            <div>
                                <h3 class="text-xl font-display font-bold text-navy uppercase tracking-widest">Subjects to Grade</h3>
                                <p class="text-xs text-navy/60 font-mono mt-1 uppercase">Assigned Subjects & Progress</p>
                            </div>
                            <span class="px-3 py-1 bg-navy text-eggshell text-[10px] font-bold uppercase tracking-widest">Teacher Role</span>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($subjectsToGrade as $sts)
                                    <div class="group p-5 border-2 border-navy bg-white hover:bg-eggshell hover:shadow-[4px_4px_0_0_#0B132B] hover:-translate-y-1 transition-all duration-200">
                                        <div class="flex justify-between items-start mb-6">
                                            <div>
                                                <h4 class="font-display font-bold text-navy text-xl leading-tight uppercase">{{ $sts->subject->name }}</h4>
                                                <span class="text-xs font-mono font-bold text-navy/60">{{ $sts->section->name }} • {{ $sts->subject->subject_code }}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-2xl font-display font-bold text-navy">{{ $sts->progress }}%</span>
                                                <p class="text-[10px] font-bold text-navy/60 uppercase tracking-widest">Progress</p>
                                            </div>
                                        </div>
                                        
                                        <div class="w-full bg-eggshell border-2 border-navy h-4 mb-6 relative">
                                            <div class="bg-navy h-full transition-all duration-1000 border-r-2 border-navy" style="width: {{ $sts->progress }}%"></div>
                                        </div>

                                        <a href="{{ route('teacher.grades.sheet', [$sts->subject_id, $sts->section_id]) }}" class="block text-center py-3 bg-white border-2 border-navy text-navy text-xs font-bold hover:bg-navy hover:text-eggshell transition-colors uppercase tracking-widest">
                                            Enter Grades
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sections to Advise (Adviser Role) -->
                    @if($sectionsToAdvise->isNotEmpty())
                    <div class="bg-white border-2 border-navy shadow-[4px_4px_0_0_#0B132B]">
                        <div class="p-6 border-b-2 border-navy flex justify-between items-center bg-eggshell/30">
                            <div>
                                <h3 class="text-xl font-display font-bold text-navy uppercase tracking-widest">Sections to Advise</h3>
                                <p class="text-xs text-navy/60 font-mono mt-1 uppercase">Monitor Consolidation</p>
                            </div>
                            <span class="px-3 py-1 bg-navy text-eggshell text-[10px] font-bold uppercase tracking-widest">Adviser Role</span>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach($sectionsToAdvise as $section)
                                <div class="flex items-center space-x-6 p-4 border-2 border-navy bg-white">
                                    <div class="flex-shrink-0 h-14 w-14 bg-navy flex items-center justify-center text-eggshell font-display font-bold text-2xl border-2 border-navy">
                                        {{ substr($section->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="font-display font-bold text-navy uppercase tracking-widest">{{ $section->name }} <span class="text-xs font-mono font-normal text-navy/60">({{ $section->grade_level }})</span></h4>
                                            <span class="text-sm font-bold text-navy font-mono">{{ $section->consolidation_progress }}% Consolidated</span>
                                        </div>
                                        <div class="w-full bg-eggshell border-2 border-navy h-4 relative">
                                            <div class="bg-navy h-full transition-all duration-1000 border-r-2 border-navy" style="width: {{ $section->consolidation_progress }}%"></div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button class="p-3 bg-white border-2 border-navy text-navy hover:bg-navy hover:text-eggshell transition shadow-[2px_2px_0_0_#0B132B] hover:shadow-none hover:translate-y-0.5 hover:translate-x-0.5">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>