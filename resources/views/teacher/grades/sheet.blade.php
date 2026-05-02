<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $subject->name }} - {{ $section->name }}
            </h2>
            <div class="flex items-center space-x-6">
                <span class="text-xs font-bold text-navy/40 uppercase tracking-widest">Active Period:</span>
                <div class="flex bg-eggshell border-2 border-navy p-1 shadow-[2px_2px_0_0_#0B132B]">
                    @foreach($periods as $period)
                        <a href="{{ route('teacher.grades.sheet', [$subject->id, $section->id, $period->id]) }}" 
                           class="px-4 py-1 text-[10px] font-bold uppercase tracking-widest transition-colors {{ $activePeriod->id == $period->id ? 'bg-navy text-eggshell' : 'text-navy hover:bg-navy/10' }}">
                            {{ $period->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        transmute(initial) {
            // Placeholder for transmutation logic if needed, currently just returns average
            return initial;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('teacher.grades.update-sheet') }}" method="POST">
                @csrf
                <input type="hidden" name="sts_id" value="{{ $sts->id }}">

                <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B]">
                    <div class="p-6 border-b-2 border-navy bg-eggshell/50 flex justify-between items-center">
                        <div>
                            <h3 class="font-display font-bold text-navy text-xl uppercase tracking-widest">Simplified Grade Entry</h3>
                            <p class="text-[10px] text-navy/60 font-mono uppercase tracking-tighter mt-1">Editing: {{ $activePeriod->name }}</p>
                        </div>
                        
                        @php
                            $isFinalized = $activePeriod && $students->isNotEmpty() && $students->first()->all_grades->get($activePeriod->id)->is_finalized;
                            $hasPendingRequest = \App\Models\GradeUnlockRequest::where('teacher_id', auth()->id())
                                ->where('subject_id', $subject->id)
                                ->where('section_id', $section->id)
                                ->where('status', 'pending')
                                ->exists();
                        @endphp
                        
                        <div class="flex space-x-4">
                            @if($isReadOnly)
                                <div class="bg-navy/10 text-navy border-2 border-navy/20 px-8 py-3 font-display font-bold text-sm uppercase tracking-widest">
                                    READ ONLY VIEW
                                </div>
                            @elseif($isFinalized)
                                @if($hasPendingRequest)
                                    <div class="bg-yellow-400 text-navy border-2 border-navy px-8 py-3 font-display font-bold text-sm uppercase tracking-widest shadow-[4px_4px_0_0_#0B132B]">
                                        Unlock Requested
                                    </div>
                                @else
                                    <button type="button" @click="$dispatch('open-unlock-modal')" class="bg-navy text-eggshell border-2 border-navy px-8 py-3 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy shadow-[4px_4px_0_0_#0B132B] transition-all duration-200">
                                        Request Unlock
                                    </button>
                                @endif
                            @else
                                <button type="submit" class="bg-navy text-eggshell border-2 border-navy px-8 py-3 font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell hover:text-navy shadow-[4px_4px_0_0_#0B132B] transition-all duration-200">
                                    SAVE DRAFT
                                </button>
                                <button type="submit" formaction="{{ route('teacher.grades.finalize-sheet') }}" onclick="return confirm('Submit final grades for {{ $activePeriod->name }}? This will lock the sheet.')" class="bg-crimson text-white border-2 border-navy px-8 py-3 font-display font-bold text-sm uppercase tracking-widest hover:bg-navy hover:text-eggshell shadow-[4px_4px_0_0_#0B132B] transition-all duration-200">
                                    SUBMIT FINAL
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-navy text-eggshell">
                                    <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10">Learners' Names</th>
                                    @foreach($periods as $period)
                                        <th class="px-4 py-4 text-center font-display font-bold uppercase tracking-widest text-xs border-r-2 border-white/10 {{ $activePeriod->id == $period->id ? 'bg-crimson' : '' }}">
                                            {{ $period->name }}
                                        </th>
                                    @endforeach
                                    <th class="px-6 py-4 text-center font-display font-bold uppercase tracking-widest text-xs bg-navy">Final Rating</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-navy">
                                @foreach($students as $student)
                                    <tr class="hover:bg-eggshell/30 transition-colors" x-data="{
                                        grades: {
                                            @foreach($periods as $period)
                                                p{{ $period->id }}: {{ $student->all_grades->get($period->id)->grade ?? 0 }},
                                            @endforeach
                                        },
                                        get average() {
                                            let sum = 0;
                                            let count = 0;
                                            @foreach($periods as $period)
                                                if(this.grades.p{{ $period->id }} > 0) {
                                                    sum += parseFloat(this.grades.p{{ $period->id }});
                                                    count++;
                                                }
                                            @endforeach
                                            return count > 0 ? (sum / count).toFixed(0) : 0;
                                        }
                                    }">
                                        <td class="px-6 py-4 border-r-2 border-navy bg-white z-10 sticky left-0">
                                            <div class="font-display font-bold text-navy uppercase">{{ $student->last_name }}, {{ $student->first_name }}</div>
                                            <div class="text-[10px] text-navy/40 font-mono tracking-widest">{{ $student->id_number }}</div>
                                        </td>
                                        
                                        @foreach($periods as $period)
                                            <td class="p-0 border-r-2 border-navy text-center {{ $activePeriod->id == $period->id ? 'bg-eggshell/50' : 'bg-white' }}">
                                                @if($activePeriod->id == $period->id)
                                                    <input type="number" step="0.01" x-model="grades.p{{ $period->id }}" 
                                                           name="grades[{{ $student->all_grades->get($period->id)->id }}][grade]"
                                                           {{ ($isFinalized || $isReadOnly) ? 'readonly' : '' }}
                                                           class="w-full h-16 text-center font-display font-bold text-xl text-navy bg-transparent border-none focus:ring-4 focus:ring-inset focus:ring-navy transition-all p-0">
                                                @else
                                                    <span class="font-display font-bold text-lg text-navy/40" x-text="grades.p{{ $period->id }} || '-'"></span>
                                                @endif
                                            </td>
                                        @endforeach

                                        <td class="px-6 py-4 text-center bg-navy text-white">
                                            <span class="text-2xl font-display font-bold" x-text="average"></span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>

            <!-- Unlock Request Modal -->
            <div x-data="{ open: false }" @open-unlock-modal.window="open = true" x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-navy/90 backdrop-blur-sm transition-opacity" @click="open = false"></div>
                    
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white border-4 border-navy p-8 shadow-[8px_8px_0_0_#0B132B] max-w-lg w-full text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle">
                        <div class="flex justify-between items-center mb-6 border-b-2 border-navy/10 pb-4">
                            <h3 class="font-display font-bold text-navy text-xl uppercase tracking-widest">Request Unlock</h3>
                            <button @click="open = false" class="text-navy hover:text-crimson transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <p class="text-navy/60 text-[10px] mb-6 uppercase tracking-widest font-bold">Explain why grades for {{ $activePeriod->name }} need modification.</p>
                        
                        <form action="{{ route('teacher.grades.request-unlock') }}" method="POST">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                            <input type="hidden" name="section_id" value="{{ $section->id }}">
                            
                            <textarea name="reason" required rows="4" class="w-full border-2 border-navy p-4 font-mono text-sm focus:ring-0 focus:border-crimson outline-none mb-6" placeholder="Reason for modification..."></textarea>
                            
                            <div class="flex space-x-4">
                                <button type="submit" class="flex-grow bg-crimson text-white border-2 border-navy py-4 font-display font-bold text-sm uppercase tracking-widest hover:bg-navy transition-colors">
                                    Send Request
                                </button>
                                <button type="button" @click="open = false" class="px-8 border-2 border-navy font-display font-bold text-sm uppercase tracking-widest hover:bg-eggshell transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
