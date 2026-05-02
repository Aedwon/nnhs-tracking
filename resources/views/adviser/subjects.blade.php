<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display font-bold text-xl text-navy uppercase tracking-widest">
                Manage Curriculum: {{ $section->name }}
            </h2>
            <a href="{{ route('adviser.dashboard') }}" class="text-navy hover:text-crimson font-bold text-xs uppercase tracking-widest flex items-center transition-colors">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        subjects: {{ json_encode($section->subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'teacher' => $s->teacher->name ?? null])) }},
        addSubject() {
            this.subjects.push({ name: '', teacher: null });
        },
        removeSubject(index) {
            this.subjects.splice(index, 1);
        }
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B] p-8">
                <form action="{{ route('adviser.subjects.update', $section) }}" method="POST">
                    @csrf
                    
                    <div class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                        <div>
                            <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Total Expected Subjects</label>
                            <p class="text-[9px] text-navy/50 mb-2 uppercase italic">How many subjects should this section have? (Used for monitoring)</p>
                            <input type="number" name="expected_subjects_count" value="{{ $section->expected_subjects_count }}" required min="0" class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none">
                        </div>
                        <div class="pb-1 text-[10px] font-bold text-navy uppercase">
                            Target Progress: {{ $section->subjects->count() }} / {{ $section->expected_subjects_count }}
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex justify-between items-center border-b-2 border-navy/10 pb-4">
                            <h3 class="font-display font-bold text-navy text-sm uppercase tracking-widest">Subject Slots</h3>
                            <button type="button" @click="addSubject()" class="bg-navy text-eggshell px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest hover:bg-crimson transition-colors border-2 border-navy">
                                + Add Slot
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <template x-for="(subj, index) in subjects" :key="index">
                                <div class="flex space-x-4 items-center">
                                    <div class="flex-grow flex space-x-4 bg-eggshell/30 border-2 border-navy p-4 items-center">
                                        <template x-if="subj.id">
                                            <input type="hidden" :name="`subjects[${index}][id]`" :value="subj.id">
                                        </template>
                                        
                                        <div class="flex-grow">
                                            <label class="block text-[8px] font-bold text-navy/40 uppercase mb-1">Subject Name</label>
                                            <input type="text" :name="`subjects[${index}][name]`" x-model="subj.name" required class="w-full border-b border-navy/20 bg-transparent p-1 font-mono text-sm focus:ring-0 focus:border-crimson outline-none" placeholder="e.g. Mathematics 7">
                                        </div>

                                        <div class="w-1/3 border-l border-navy/10 pl-4">
                                            <label class="block text-[8px] font-bold text-navy/40 uppercase mb-1">Assigned Teacher</label>
                                            <span class="text-[10px] font-bold uppercase block truncate" x-text="subj.teacher || 'WAITING FOR CLAIM...'"></span>
                                        </div>
                                    </div>
                                    
                                    <button type="button" @click="removeSubject(index)" class="text-navy/20 hover:text-crimson transition-colors" title="Remove Slot">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>

                            <template x-if="subjects.length === 0">
                                <div class="py-12 text-center bg-eggshell/10 border-2 border-dashed border-navy/10">
                                    <p class="font-mono text-xs text-navy/30 uppercase italic">No subjects defined yet. Add slots for teachers to claim.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-12 flex space-x-4">
                        <button type="submit" class="flex-grow bg-navy text-eggshell py-4 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy border-2 border-navy transition-all shadow-[4px_4px_0_0_#0B132B]">
                            Save Curriculum Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
