<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display font-bold text-xl text-navy uppercase tracking-widest">
                {{ __('Personnel Management') }}
            </h2>
            <button @click="$dispatch('open-modal', 'add-teacher')"
                class="bg-navy text-eggshell border-2 border-navy px-6 py-2 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy shadow-[4px_4px_0_0_#0B132B] transition-all duration-200">
                + Register Personnel
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        editMode: false,
        activeTeacher: null,
        deleteUrl: '',
        confirmDelete(url) {
            this.deleteUrl = url;
            $dispatch('open-modal', 'delete-confirm');
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Global Error Display -->
            @if ($errors->any())
                <div class="mb-8 bg-crimson/10 border-l-4 border-crimson p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-crimson" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-crimson uppercase tracking-widest">Entry Errors Detected</h3>
                            <div class="mt-2 text-xs text-crimson/80 font-mono">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Table -->
            <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B]">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-navy text-eggshell">
                                <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Name</th>
                                <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Email</th>
                                <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Level</th>
                                <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Roles</th>
                                <th class="px-6 py-4 text-center font-display font-bold uppercase tracking-widest text-xs">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-navy">
                            @foreach($teachers as $teacher)
                                <tr class="hover:bg-eggshell/30 transition-colors font-mono text-sm text-navy">
                                    <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 bg-navy text-eggshell flex items-center justify-center font-display font-bold mr-4 border-2 border-navy">
                                                {{ substr($teacher->name, 0, 1) }}
                                            </div>
                                            <div class="font-bold uppercase">{{ $teacher->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy text-xs">{{ $teacher->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy text-center">
                                        <span class="px-2 py-0.5 bg-eggshell border border-navy text-[10px] font-bold uppercase">
                                            {{ $teacher->school_level }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($teacher->roles as $role)
                                                <span class="px-2 py-0.5 {{ $role->name == 'Adviser' ? 'bg-crimson text-white' : 'bg-navy text-eggshell' }} text-[9px] font-bold uppercase tracking-tighter">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
                                        <button @click="
                                            editMode = true;
                                            activeTeacher = {{ json_encode($teacher) }};
                                            $dispatch('open-modal', 'teacher-form');
                                        " class="text-navy hover:text-crimson transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="confirmDelete('{{ route('admin.teachers.destroy', $teacher) }}')" class="text-navy/30 hover:text-crimson transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t-2 border-navy">
                    {{ $teachers->links() }}
                </div>
            </div>
        </div>

        <!-- Teacher Form Modal (Add/Edit) -->
        <x-modal name="teacher-form" focusable>
            <div class="p-8 bg-white border-4 border-navy shadow-[8px_8px_0_0_#0B132B]">
                <div class="flex justify-between items-center mb-8 border-b-2 border-navy/10 pb-4">
                    <h3 class="font-display font-bold text-navy text-2xl uppercase tracking-widest" x-text="editMode ? 'Edit Personnel' : 'Register Personnel'"></h3>
                    <button @click="$dispatch('close')" class="text-navy hover:text-crimson transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="editMode ? '{{ route('admin.teachers.index') }}/' + activeTeacher.id : '{{ route('admin.teachers.store') }}'" method="POST">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Full Name</label>
                            <input type="text" name="name" :value="editMode ? activeTeacher.name : '{{ old('name') }}'" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none" placeholder="Juan Dela Cruz">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Email Address</label>
                            <input type="email" name="email" :value="editMode ? activeTeacher.email : '{{ old('email') }}'" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none" placeholder="juan@example.com">
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">School Level</label>
                                <select name="school_level" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none bg-white">
                                    <option value="JHS" :selected="editMode ? activeTeacher.school_level == 'JHS' : '{{ old('school_level') == 'JHS' }}'">JHS (Grade 7-10)</option>
                                    <option value="SHS" :selected="editMode ? activeTeacher.school_level == 'SHS' : '{{ old('school_level') == 'SHS' }}'">SHS (Grade 11-12)</option>
                                    <option value="BOTH" :selected="editMode ? activeTeacher.school_level == 'BOTH' : '{{ old('school_level') == 'BOTH' }}'">BOTH (JHS & SHS)</option>
                                </select>
                            </div>
                            <div class="flex items-end pb-1">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" name="is_adviser" value="1" 
                                        :checked="editMode ? (activeTeacher.roles && activeTeacher.roles.some(r => r.name === 'Adviser')) : '{{ old('is_adviser') }}'"
                                        class="w-6 h-6 border-2 border-navy text-crimson focus:ring-0">
                                    <span class="text-[10px] font-bold text-navy uppercase tracking-widest">Section Adviser</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2" x-text="editMode ? 'New Password (Optional)' : 'Password'"></label>
                                <input type="password" name="password" :required="!editMode" class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Confirm Password</label>
                                <input type="password" name="password_confirmation" :required="!editMode" class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex space-x-4">
                        <button type="submit" class="flex-grow bg-navy text-eggshell border-2 border-navy py-4 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy transition-all shadow-[4px_4px_0_0_#0B132B]" x-text="editMode ? 'Update Account' : 'Create Account'">
                        </button>
                        <button type="button" @click="$dispatch('close')" class="px-8 border-2 border-navy font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <!-- Delete Confirmation Modal -->
        <x-modal name="delete-confirm" maxWidth="sm">
            <div class="p-8 bg-white border-4 border-navy shadow-[8px_8px_0_0_#0B132B] text-center">
                <div class="w-16 h-16 bg-crimson/10 text-crimson mx-auto rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="font-display font-bold text-navy text-xl uppercase tracking-widest mb-2">Confirm Delete</h3>
                <p class="font-mono text-xs text-navy/60 mb-8">This action is irreversible. All associated grade records will be maintained but unlinked.</p>
                
                <form :action="deleteUrl" method="POST" class="flex space-x-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex-grow bg-crimson text-white py-3 font-display font-bold text-xs uppercase tracking-widest hover:bg-crimson/90 transition-colors shadow-[4px_4px_0_0_#0B132B]">Delete Forever</button>
                    <button type="button" @click="$dispatch('close')" class="flex-grow border-2 border-navy py-3 font-display font-bold text-xs uppercase tracking-widest hover:bg-eggshell transition-colors">Cancel</button>
                </form>
            </div>
        </x-modal>

        <!-- Add Teacher Event Handler -->
        <div @open-modal.window="if($event.detail === 'add-teacher') { editMode = false; activeTeacher = null; $dispatch('open-modal', 'teacher-form'); }"></div>
    </div>
</x-app-layout>