<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display font-bold text-xl text-navy uppercase tracking-widest">
                {{ __('Unified Personnel Portal') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('teacher.assignments.index') }}" class="bg-navy text-eggshell px-6 py-2 font-display font-bold text-xs uppercase tracking-widest border-2 border-navy hover:bg-eggshell hover:text-navy transition-all shadow-[4px_4px_0_0_#0B132B]">
                    + Claim New Subject
                </a>
                @if($sectionsToAdvise->isNotEmpty())
                    <a href="{{ route('adviser.dashboard') }}" class="bg-crimson text-white px-6 py-2 font-display font-bold text-xs uppercase tracking-widest border-2 border-navy hover:bg-white hover:text-crimson transition-all shadow-[4px_4px_0_0_#0B132B]">
                        Adviser Dashboard
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Left Sidebar: Deadlines & Profile -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white border-2 border-navy shadow-[4px_4px_0_0_#0B132B] p-6 text-center">
                        <div class="h-20 w-20 bg-navy mx-auto mb-4 flex items-center justify-center text-eggshell text-3xl font-display font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <h3 class="font-display font-bold text-navy text-xl uppercase leading-tight">{{ auth()->user()->name }}</h3>
                        <p class="text-[10px] font-bold text-navy/40 uppercase tracking-widest mt-1">{{ auth()->user()->getRoleNames()->first() }}</p>
                    </div>

                    <div class="bg-white border-2 border-navy shadow-[4px_4px_0_0_#0B132B]">
                        <div class="p-4 border-b-2 border-navy bg-crimson">
                            <h3 class="font-display font-bold text-white text-[10px] uppercase tracking-widest">Grading Deadlines</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            @forelse($deadlines as $deadline)
                                <div class="p-3 border-2 border-navy bg-eggshell/30">
                                    <div class="text-[9px] text-crimson font-bold uppercase tracking-widest mb-1">{{ $deadline->gradingPeriod->name }}</div>
                                    <div class="text-base font-display font-bold text-navy">{{ $deadline->deadline_at->diffForHumans() }}</div>
                                    <div class="text-[10px] text-navy/40 mt-1 font-mono uppercase">{{ $deadline->deadline_at->format('M d, Y') }}</div>
                                </div>
                            @empty
                                <p class="text-[10px] text-navy/30 text-center py-4 font-mono uppercase">No upcoming deadlines.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3 space-y-12">
                    
                    <!-- My Subjects -->
                    <section>
                        <div class="flex justify-between items-end mb-6 border-b-2 border-navy/10 pb-4">
                            <h3 class="font-display font-bold text-navy text-2xl uppercase tracking-widest">My Active Load</h3>
                            <span class="text-[10px] font-bold text-navy/40 uppercase">Period: {{ $activePeriod->name }}</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @forelse($subjectsToGrade as $subject)
                                <div class="bg-white border-2 border-navy shadow-[6px_6px_0_0_#0B132B] p-6 hover:-translate-y-1 transition-transform group">
                                    <div class="flex justify-between items-start mb-6">
                                        <div>
                                            <h4 class="font-display font-bold text-navy text-xl uppercase leading-tight">{{ $subject->name }}</h4>
                                            <p class="font-mono text-xs text-navy/60 uppercase">{{ $subject->section->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-display font-bold text-navy">{{ $subject->progress }}%</div>
                                            <p class="text-[8px] font-bold text-navy/40 uppercase tracking-tighter">Grading Progress</p>
                                        </div>
                                    </div>
                                    
                                    <div class="w-full bg-eggshell border-2 border-navy h-4 mb-8 relative overflow-hidden">
                                        <div class="bg-navy h-full transition-all duration-700" style="width: {{ $subject->progress }}%"></div>
                                    </div>

                                    <div class="flex space-x-3">
                                        <a href="{{ route('teacher.grades.sheet', [$subject->id, $subject->section_id]) }}" class="flex-grow bg-navy text-eggshell text-center py-3 font-display font-bold text-[10px] uppercase tracking-widest border-2 border-navy hover:bg-eggshell hover:text-navy transition-colors">
                                            Open Grade Sheet
                                        </a>
                                        <form action="{{ route('teacher.assignments.unclaim', $subject) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-3 border-2 border-navy/10 text-navy/40 hover:text-crimson hover:border-crimson transition-colors text-[10px] font-bold uppercase" title="Unclaim Subject">
                                                ×
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-20 text-center bg-eggshell/20 border-2 border-dashed border-navy/10">
                                    <p class="font-mono text-xs text-navy/40 uppercase tracking-widest mb-4">No subjects claimed yet.</p>
                                    <a href="{{ route('teacher.assignments.index') }}" class="text-navy font-bold text-[10px] uppercase underline underline-offset-4 decoration-2 decoration-crimson hover:text-crimson transition-colors">
                                        Browse Available Sections →
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <!-- Advising Overview (Quick Stats) -->
                    @if($sectionsToAdvise->isNotEmpty())
                    <section>
                        <h3 class="font-display font-bold text-navy text-sm uppercase tracking-widest mb-6 border-l-4 border-crimson pl-4">Advised Sections Progress</h3>
                        <div class="space-y-4">
                            @foreach($sectionsToAdvise as $section)
                                <div class="bg-white border-2 border-navy p-5 flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-8">
                                    <div class="md:w-1/4">
                                        <h4 class="font-display font-bold text-navy text-lg uppercase leading-tight">{{ $section->name }}</h4>
                                        <p class="text-[10px] font-bold text-navy/40 uppercase">Consolidation: {{ $section->consolidation_progress }}%</p>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="w-full bg-eggshell border-2 border-navy h-4 overflow-hidden relative">
                                            <div class="bg-crimson h-full transition-all duration-700" style="width: {{ $section->consolidation_progress }}%"></div>
                                        </div>
                                    </div>
                                    <div class="md:w-1/4 flex justify-end">
                                        <a href="{{ route('adviser.dashboard') }}" class="text-[10px] font-bold text-navy uppercase underline underline-offset-4 decoration-navy/20 hover:text-crimson hover:decoration-crimson transition-all">
                                            View Details →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>