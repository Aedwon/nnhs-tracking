<x-app-layout>
    <div x-data="{ 
        editMode: false,
        activeSection: null,
        deleteUrl: '',
        confirmDelete(url) {
            this.deleteUrl = url;
            $dispatch('open-modal', 'delete-confirm');
        }
    }">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-display font-bold text-xl text-navy uppercase tracking-widest">
                    {{ __('Section Master List') }}
                </h2>
                <button @click="editMode = false; activeSection = null; $dispatch('open-modal', 'section-form')"
                    class="bg-navy text-eggshell border-2 border-navy px-6 py-2 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy shadow-[4px_4px_0_0_#0B132B] transition-all duration-200">
                    + New Section
                </button>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Global Error Display -->
                @if ($errors->any())
                    <div class="mb-8 bg-crimson border-2 border-navy p-4 shadow-[4px_4px_0_0_#0B132B]">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <h3 class="text-white font-display font-bold text-xs uppercase tracking-widest">Entry Errors Detected</h3>
                        </div>
                        <ul class="mt-2 list-disc list-inside font-mono text-[10px] text-white/80">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B]">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-navy text-eggshell">
                                    <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Section Name</th>
                                    <th class="px-6 py-4 text-center font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Grade</th>
                                    <th class="px-6 py-4 text-center font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Level</th>
                                    <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Adviser</th>
                                    <th class="px-6 py-4 text-center font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Curriculum</th>
                                    <th class="px-6 py-4 text-center font-display font-bold uppercase tracking-widest text-xs">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-navy">
                                @forelse($sections as $section)
                                    <tr class="hover:bg-eggshell/30 transition-colors font-mono text-sm text-navy">
                                        <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy font-bold">{{ $section->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy text-center">Grade {{ $section->grade_level }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy text-center">
                                            <span class="px-2 py-0.5 bg-eggshell border border-navy text-[10px] font-bold uppercase">
                                                {{ $section->level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy font-bold uppercase text-xs">
                                            {{ $section->adviser->name ?? 'NONE ASSIGNED' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <span class="text-[10px] font-bold">{{ $section->subjects->count() }} / {{ $section->expected_subjects_count }}</span>
                                                <div class="w-16 h-2 bg-eggshell border border-navy overflow-hidden">
                                                    <div class="h-full bg-crimson" style="width: {{ $section->expected_subjects_count > 0 ? ($section->subjects->count() / $section->expected_subjects_count) * 100 : 0 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
                                            <button @click="
                                                editMode = true;
                                                activeSection = {{ json_encode($section) }};
                                                $dispatch('open-modal', 'section-form');
                                            " class="text-navy hover:text-crimson transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button @click="confirmDelete('{{ route('admin.sections.destroy', $section) }}')" class="text-navy/30 hover:text-crimson transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-navy/40 font-mono text-xs uppercase tracking-widest">No sections found. Create one to get started.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t-2 border-navy">
                        {{ $sections->links() }}
                    </div>
                </div>
            </div>

            <!-- Section Form Modal -->
            <x-modal name="section-form" focusable>
                <div class="p-8 bg-white border-4 border-navy shadow-[8px_8px_0_0_#0B132B]">
                    <div class="flex justify-between items-center mb-8 border-b-2 border-navy/10 pb-4">
                        <h3 class="font-display font-bold text-navy text-2xl uppercase tracking-widest" x-text="editMode ? 'Edit Section' : 'Create New Section'"></h3>
                        <button @click="$dispatch('close')" class="text-navy hover:text-crimson transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="editMode && activeSection ? '{{ route('admin.sections.index') }}/' + activeSection.id : '{{ route('admin.sections.store') }}'" method="POST">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Section Name</label>
                                <input type="text" name="name" :value="editMode && activeSection ? activeSection.name : '{{ old('name') }}'" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none" placeholder="Diamond">
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Grade Level</label>
                                    <input type="number" name="grade_level" :value="editMode && activeSection ? activeSection.grade_level : '{{ old('grade_level') }}'" required min="1" max="12" class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">School Level</label>
                                    <select name="level" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none bg-white">
                                        <option value="JHS" :selected="editMode && activeSection ? activeSection.level == 'JHS' : '{{ old('level') == 'JHS' }}'">JHS (Grade 7-10)</option>
                                        <option value="SHS" :selected="editMode && activeSection ? activeSection.level == 'SHS' : '{{ old('level') == 'SHS' }}'">SHS (Grade 11-12)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Assigned Adviser</label>
                                <select name="adviser_id" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none bg-white">
                                    <option value="">-- Select Personnel --</option>
                                    @foreach($advisers as $adviser)
                                        <option value="{{ $adviser->id }}" :selected="editMode && activeSection ? activeSection.adviser_id == {{ $adviser->id }} : '{{ old('adviser_id') == $adviser->id }}'">
                                            {{ $adviser->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-10 flex space-x-4">
                            <button type="submit" class="flex-grow bg-navy text-eggshell border-2 border-navy py-4 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy transition-all shadow-[4px_4px_0_0_#0B132B]" x-text="editMode ? 'Update Section' : 'Create Section'">
                            </button>
                            <button type="button" @click="$dispatch('close')" class="px-8 border-2 border-navy font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <!-- Delete Confirmation -->
            <x-modal name="delete-confirm" maxWidth="sm">
                <div class="p-8 bg-white border-4 border-navy shadow-[8px_8px_0_0_#0B132B] text-center">
                    <div class="w-16 h-16 bg-crimson/10 text-crimson mx-auto rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-navy text-xl uppercase tracking-widest mb-2">Confirm Delete</h3>
                    <p class="font-mono text-xs text-navy/60 mb-8">This will delete the section and all its subject slots. Grade records will be wiped.</p>
                    
                    <form :action="deleteUrl" method="POST" class="flex space-x-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex-grow bg-crimson text-white py-3 font-display font-bold text-xs uppercase tracking-widest hover:bg-crimson/90 transition-colors shadow-[4px_4px_0_0_#0B132B]">Delete Section</button>
                        <button type="button" @click="$dispatch('close')" class="flex-grow border-2 border-navy py-3 font-display font-bold text-xs uppercase tracking-widest hover:bg-eggshell transition-colors">Cancel</button>
                    </form>
                </div>
            </x-modal>
        </div>
    </div>
</x-app-layout>
