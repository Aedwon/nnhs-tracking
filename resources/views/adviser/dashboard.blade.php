<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-bold text-xl text-navy uppercase tracking-widest">
            {{ __('Adviser Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <h3 class="font-display font-bold text-navy text-lg uppercase tracking-widest border-b-2 border-navy/10 pb-2">Your Advised Sections</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($sections as $section)
                    <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B] p-6 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-display font-bold text-navy text-2xl uppercase">{{ $section->name }}</h4>
                                <p class="font-mono text-xs text-navy/60 uppercase">Grade {{ $section->grade_level }} • {{ $section->level }}</p>
                            </div>
                            <span class="bg-navy text-eggshell text-[10px] font-bold px-2 py-1 uppercase">Adviser</span>
                        </div>

                        <div class="flex-grow space-y-4 mb-6">
                            <div class="space-y-1">
                                <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest text-navy">
                                    <span>Curriculum Filling</span>
                                    <span>{{ $section->subjects->count() }} / {{ $section->expected_subjects_count }}</span>
                                </div>
                                <div class="w-full h-3 bg-eggshell border-2 border-navy overflow-hidden">
                                    <div class="h-full bg-crimson transition-all duration-500" style="width: {{ $section->expected_subjects_count > 0 ? ($section->subjects->count() / $section->expected_subjects_count) * 100 : 0 }}%"></div>
                                </div>
                            </div>

                            <div class="bg-eggshell/30 border border-navy/10 p-3">
                                <p class="text-[10px] font-bold text-navy uppercase mb-2">Subject Status</p>
                                <div class="space-y-1">
                                    @forelse($section->subjects as $subj)
                                        <div class="flex justify-between items-center text-[10px] font-mono">
                                            <span class="text-navy/70">{{ $subj->name }}</span>
                                            <span class="{{ $subj->teacher_id ? 'text-navy font-bold' : 'text-crimson' }}">
                                                {{ $subj->teacher->name ?? 'UNCLAIMED' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-[9px] text-navy/40 italic">No subject slots defined.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('adviser.subjects', $section) }}" class="mt-auto w-full bg-navy text-eggshell text-center py-3 font-display font-bold text-xs uppercase tracking-widest hover:bg-eggshell hover:text-navy border-2 border-navy transition-all shadow-[4px_4px_0_0_#0B132B]">
                            Manage Curriculum
                        </a>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white border-2 border-dashed border-navy/20">
                        <p class="font-mono text-xs text-navy/40 uppercase tracking-widest">You are not assigned as an adviser to any section.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
