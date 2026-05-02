<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data="{ open: false }">
            <h2 class="font-display font-bold text-xl text-navy uppercase tracking-widest">
                {{ __('Grading Schedule') }}
            </h2>
            <button @click="$dispatch('open-period-modal')"
                class="bg-navy text-eggshell border-2 border-navy px-6 py-2 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy shadow-[4px_4px_0_0_#0B132B] transition-all duration-200">
                + Create Period
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ showModal: false }" @open-period-modal.window="showModal = true">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($periods as $period)
                    <div class="bg-white border-2 border-navy shadow-[6px_6px_0_0_#0B132B] p-6 hover:-translate-y-1 hover:shadow-[10px_10px_0_0_#0B132B] transition-all duration-200">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-xl font-display font-bold text-navy uppercase leading-tight">{{ $period->name }}</h3>
                                <span class="px-2 py-0.5 bg-eggshell border border-navy text-[10px] font-bold uppercase tracking-widest mt-2 inline-block">
                                    {{ $period->level }}
                                </span>
                            </div>
                            <span class="px-3 py-1 border-2 border-navy font-display font-bold text-[10px] uppercase tracking-widest {{ $period->is_active ? 'bg-navy text-eggshell' : 'bg-eggshell/50 text-navy/40' }}">
                                {{ $period->is_active ? 'Active' : 'Closed' }}
                            </span>
                        </div>
                        
                        <div class="space-y-3 font-mono text-xs text-navy/70 border-b-2 border-navy/10 pb-6 mb-6">
                            <div class="flex items-center">
                                <span class="w-20 font-bold text-navy uppercase tracking-tighter">Start:</span>
                                {{ $period->start_date }}
                            </div>
                            <div class="flex items-center">
                                <span class="w-20 font-bold text-navy uppercase tracking-tighter">End:</span>
                                {{ $period->end_date }}
                            </div>
                        </div>

                        <div>
                            <h4 class="text-[10px] font-bold text-navy/40 uppercase tracking-widest mb-4">Final Submission Deadline</h4>
                            @foreach($period->deadlines as $deadline)
                                <div class="flex items-center p-3 bg-crimson/10 border-2 border-crimson/20 text-crimson font-display font-bold text-sm">
                                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"></path>
                                    </svg>
                                    {{ $deadline->deadline_at->format('M d, Y h:i A') }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Add Period Modal -->
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-navy/90 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white border-4 border-navy p-8 shadow-[8px_8px_0_0_#0B132B] max-w-xl w-full text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle">
                    <div class="flex justify-between items-center mb-8 border-b-2 border-navy/10 pb-4">
                        <h3 class="font-display font-bold text-navy text-2xl uppercase tracking-widest">New Period</h3>
                        <button @click="showModal = false" class="text-navy hover:text-crimson transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('admin.grading-periods.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Period Name</label>
                                    <input type="text" name="name" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none" placeholder="e.g., 1st Quarter">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Target Level</label>
                                    <select name="level" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none bg-white">
                                        <option value="JHS">JHS (Quarters)</option>
                                        <option value="SHS">SHS (Semesters)</option>
                                        <option value="BOTH">Universal</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">Start Date</label>
                                    <input type="date" name="start_date" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2">End Date</label>
                                    <input type="date" name="end_date" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-navy uppercase tracking-widest mb-2 text-crimson">Submission Deadline</label>
                                <input type="datetime-local" name="deadline_at" required class="w-full border-2 border-crimson p-3 font-mono text-sm focus:ring-0 focus:border-navy outline-none bg-crimson/5">
                            </div>
                        </div>

                        <div class="mt-10 flex space-x-4">
                            <button type="submit" class="flex-grow bg-navy text-eggshell border-2 border-navy py-4 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy transition-all shadow-[4px_4px_0_0_#0B132B]">
                                Activate Period
                            </button>
                            <button type="button" @click="showModal = false" class="px-8 border-2 border-navy font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>