<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.sections.index') }}" class="text-navy/40 hover:text-navy transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="font-display font-bold text-xl text-navy uppercase tracking-widest">
                        Section: {{ $section->name }}
                    </h2>
                    <p class="text-[10px] font-bold text-navy/60 uppercase tracking-widest">Grade {{ $section->grade_level }} • {{ $section->level }} • Adviser: {{ $section->adviser->name }}</p>
                </div>
            </div>
            <button @click="$dispatch('open-modal', 'assign-subject')"
                class="bg-navy text-eggshell border-2 border-navy px-6 py-2 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy shadow-[4px_4px_0_0_#0B132B] transition-all duration-200">
                + Assign Subject
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        unassignUrl: '',
        confirmUnassign(url) {
            this.unassignUrl = url;
            $dispatch('open-modal', 'unassign-confirm');
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B]">
                <div class="p-6 border-b-2 border-navy bg-eggshell/30">
                    <h3 class="font-display font-bold text-navy text-xs uppercase tracking-widest">Assigned Subjects & Teachers</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-navy/5 text-navy">
                                <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-xs border-r-2 border-navy/10">Subject</th>
                                <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-xs border-r-2 border-navy/10">Teacher</th>
                                <th class="px-6 py-4 text-center font-display font-bold uppercase tracking-widest text-xs">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-navy/10">
                            @forelse($section->subjectTeacherSections as $sts)
                                <tr class="hover:bg-eggshell/10 transition-colors font-mono text-sm text-navy">
                                    <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy/10 font-bold uppercase">
                                        {{ $sts->subject->name }} <span class="ml-2 text-[10px] text-navy/40 font-normal">({{ $sts->subject->subject_code }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy/10">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 bg-navy text-eggshell flex items-center justify-center font-display font-bold text-xs mr-3">
                                                {{ substr($sts->teacher->name, 0, 1) }}
                                            </div>
                                            {{ $sts->teacher->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button @click="confirmUnassign('{{ route('admin.sections.unassign-subject', [$section, $sts]) }}')" class="text-navy/30 hover:text-crimson transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-navy/40 font-mono text-xs uppercase tracking-widest">No subjects assigned to this section yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Assign Subject Modal -->
        <x-modal name="assign-subject" focusable>
            <div class="p-8 bg-white border-4 border-navy shadow-[8px_8px_0_0_#0B132B]">
                <div class="flex justify-between items-center mb-8 border-b-2 border-navy/10 pb-4">
                    <h3 class="font-display font-bold text-navy text-2xl uppercase tracking-widest">Assign Subject</h3>
                    <button @click="$dispatch('close')" class="text-navy hover:text-crimson transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.sections.assign-subject', $section) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Select Subject</label>
                            <select name="subject_id" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none bg-white">
                                <option value="">-- Choose Subject --</option>
                                @foreach($allSubjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->subject_code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Assigned Teacher</label>
                            <select name="teacher_id" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none bg-white">
                                <option value="">-- Choose Teacher --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-10 flex space-x-4">
                        <button type="submit" class="flex-grow bg-navy text-eggshell border-2 border-navy py-4 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy transition-all shadow-[4px_4px_0_0_#0B132B]">
                            Assign to Section
                        </button>
                        <button type="button" @click="$dispatch('close')" class="px-8 border-2 border-navy font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <!-- Unassign Confirmation -->
        <x-modal name="unassign-confirm" maxWidth="sm">
            <div class="p-8 bg-white border-4 border-navy shadow-[8px_8px_0_0_#0B132B] text-center">
                <div class="w-16 h-16 bg-crimson/10 text-crimson mx-auto rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="font-display font-bold text-navy text-xl uppercase tracking-widest mb-2">Unassign Subject</h3>
                <p class="font-mono text-xs text-navy/60 mb-8">This will remove the subject from this section's curriculum. Existing grade entries for this subject in this section will be hidden but not deleted.</p>
                
                <form :action="unassignUrl" method="POST" class="flex space-x-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex-grow bg-crimson text-white py-3 font-display font-bold text-xs uppercase tracking-widest hover:bg-crimson/90 transition-colors shadow-[4px_4px_0_0_#0B132B]">Unassign Now</button>
                    <button type="button" @click="$dispatch('close')" class="flex-grow border-2 border-navy py-3 font-display font-bold text-xs uppercase tracking-widest hover:bg-eggshell transition-colors">Cancel</button>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>
