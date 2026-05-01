<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $subject->name }} - {{ $section->name }}
            </h2>
            <div class="space-x-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Weights: WW ({{ $subject->written_weight }}%) | PT ({{ $subject->performance_weight }}%) | QA ({{ $subject->exam_weight }}%)</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        max_scores: {
            ww: {{ json_encode($sts->ww_max_scores ?? []) }},
            pt: {{ json_encode($sts->pt_max_scores ?? []) }},
            qa: {{ $sts->qa_max_score ?? 0 }}
        },
        weights: { 
            ww: {{ $subject->written_weight }}, 
            pt: {{ $subject->performance_weight }}, 
            qa: {{ $subject->exam_weight }} 
        },
        calcComponent(scores, maxScores, weight) {
            let studentTotal = scores.reduce((a,b) => a + parseFloat(b||0), 0);
            let maxTotal = maxScores.reduce((a,b) => a + parseFloat(b||0), 0);
            if(maxTotal <= 0) return { total: studentTotal, ps: 0, ws: 0 };
            let ps = (studentTotal / maxTotal) * 100;
            let ws = ps * (weight / 100);
            return { total: studentTotal, ps: ps, ws: ws };
        },
        calcQA(score, maxScore, weight) {
            let studentTotal = parseFloat(score || 0);
            let maxTotal = parseFloat(maxScore || 0);
            if(maxTotal <= 0) return { ps: 0, ws: 0 };
            let ps = (studentTotal / maxTotal) * 100;
            let ws = ps * (weight / 100);
            return { ps: ps, ws: ws };
        },
        calcInitial(ww_ws, pt_ws, qa_ws) {
            return ww_ws + pt_ws + qa_ws;
        },
        transmute(initial) {
            if (initial >= 100) return 100;
            if (initial >= 98.40) return 99;
            if (initial >= 96.80) return 98;
            if (initial >= 95.20) return 97;
            if (initial >= 93.60) return 96;
            if (initial >= 92.00) return 95;
            if (initial >= 90.40) return 94;
            if (initial >= 88.80) return 93;
            if (initial >= 87.20) return 92;
            if (initial >= 85.60) return 91;
            if (initial >= 84.00) return 90;
            if (initial >= 82.40) return 89;
            if (initial >= 80.80) return 88;
            if (initial >= 79.20) return 87;
            if (initial >= 77.60) return 86;
            if (initial >= 76.00) return 85;
            if (initial >= 74.40) return 84;
            if (initial >= 72.80) return 83;
            if (initial >= 71.20) return 82;
            if (initial >= 69.60) return 81;
            if (initial >= 68.00) return 80;
            if (initial >= 66.40) return 79;
            if (initial >= 64.80) return 78;
            if (initial >= 63.20) return 77;
            if (initial >= 61.60) return 76;
            if (initial >= 60.00) return 75;
            if (initial >= 56.00) return 74;
            if (initial >= 52.00) return 73;
            if (initial >= 48.00) return 72;
            if (initial >= 44.00) return 71;
            if (initial >= 40.00) return 70;
            if (initial >= 36.00) return 69;
            if (initial >= 32.00) return 68;
            if (initial >= 28.00) return 67;
            if (initial >= 24.00) return 66;
            if (initial >= 20.00) return 65;
            if (initial >= 16.00) return 64;
            if (initial >= 12.00) return 63;
            if (initial >= 8.00) return 62;
            if (initial >= 4.00) return 61;
            return 60;
        }
    }">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('teacher.grades.update-sheet') }}" method="POST">
                @csrf
                <input type="hidden" name="sts_id" value="{{ $sts->id }}">

                <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B]">
                    <div class="p-6 border-b-2 border-navy bg-eggshell/50 flex justify-between items-center">
                        <h3 class="font-display font-bold text-navy text-xl uppercase tracking-widest">Grade Entry Sheet</h3>
                        @php
                            $isFinalized = $students->isNotEmpty() && $students->first()->grade_record->is_finalized;
                            $hasPendingRequest = \App\Models\GradeUnlockRequest::where('teacher_id', auth()->id())
                                ->where('subject_id', $subject->id)
                                ->where('section_id', $section->id)
                                ->where('status', 'pending')
                                ->exists();
                        @endphp
                        
                        <div class="flex space-x-4">
                            @if($isFinalized)
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
                                <button type="submit" formaction="{{ route('teacher.grades.finalize-sheet') }}" onclick="return confirm('Submit final grades? You cannot edit this sheet after finalization.')" class="bg-crimson text-white border-2 border-navy px-8 py-3 font-display font-bold text-sm uppercase tracking-widest hover:bg-navy hover:text-eggshell shadow-[4px_4px_0_0_#0B132B] transition-all duration-200">
                                    SUBMIT FINAL GRADES
                                </button>
                            @endif
                        </div>
                    </div>



                    <div class="overflow-x-auto overflow-y-visible">
                        <table class="min-w-full border-collapse text-sm">
                            <thead class="bg-navy text-eggshell">
                                <!-- Main Header Row -->
                                <tr>
                                    <th rowspan="2" class="px-6 py-4 text-left font-bold uppercase tracking-widest border-r-2 border-white align-middle whitespace-nowrap sticky left-0 bg-navy z-20">Learners' Names</th>
                                    
                                    <th :colspan="max_scores.ww.length === 0 ? 4 : max_scores.ww.length + 3" class="px-4 py-3 text-center font-bold uppercase tracking-widest border-r-2 border-white border-b-2 align-middle">
                                        <div class="flex justify-center items-center gap-4">
                                            <span>Written Works ({{ $subject->written_weight }}%)</span>
                                            @if(!$isFinalized)
                                                <div class="flex space-x-2">
                                                    <button type="button" @click="max_scores.ww.push(0); $dispatch('add-ww')" class="w-6 h-6 bg-eggshell text-navy border border-navy hover:bg-crimson hover:text-white hover:border-white font-mono text-lg leading-none flex items-center justify-center transition-colors">+</button>
                                                    <button type="button" @click="if(max_scores.ww.length>0){max_scores.ww.pop(); $dispatch('remove-ww')}" class="w-6 h-6 bg-eggshell text-navy border border-navy hover:bg-crimson hover:text-white hover:border-white font-mono text-lg leading-none flex items-center justify-center transition-colors">-</button>
                                                </div>
                                            @endif
                                        </div>
                                    </th>

                                    <th :colspan="max_scores.pt.length === 0 ? 4 : max_scores.pt.length + 3" class="px-4 py-3 text-center font-bold uppercase tracking-widest border-r-2 border-white border-b-2 align-middle">
                                        <div class="flex justify-center items-center gap-4">
                                            <span>Performance Tasks ({{ $subject->performance_weight }}%)</span>
                                            @if(!$isFinalized)
                                                <div class="flex space-x-2">
                                                    <button type="button" @click="max_scores.pt.push(0); $dispatch('add-pt')" class="w-6 h-6 bg-eggshell text-navy border border-navy hover:bg-crimson hover:text-white hover:border-white font-mono text-lg leading-none flex items-center justify-center transition-colors">+</button>
                                                    <button type="button" @click="if(max_scores.pt.length>0){max_scores.pt.pop(); $dispatch('remove-pt')}" class="w-6 h-6 bg-eggshell text-navy border border-navy hover:bg-crimson hover:text-white hover:border-white font-mono text-lg leading-none flex items-center justify-center transition-colors">-</button>
                                                </div>
                                            @endif
                                        </div>
                                    </th>

                                    <th colspan="3" class="px-4 py-3 text-center font-bold uppercase tracking-widest border-r-2 border-white border-b-2 align-middle">
                                        Quarterly Assessment ({{ $subject->exam_weight }}%)
                                    </th>
                                    
                                    <th rowspan="2" class="px-4 py-3 text-center font-bold uppercase tracking-widest border-r-2 border-white align-middle">Initial<br>Grade</th>
                                    <th rowspan="2" class="px-4 py-3 text-center font-bold uppercase tracking-widest bg-crimson align-middle">Quarterly<br>Grade</th>
                                </tr>
                                <!-- Sub Header Row -->
                                <tr class="text-xs">
                                    <!-- Written Work Sub-headers -->
                                    <template x-if="max_scores.ww.length === 0">
                                        <th class="px-3 py-2 border-r border-white/30 font-mono">1</th>
                                    </template>
                                    <template x-for="(score, index) in max_scores.ww" :key="index">
                                        <th class="px-3 py-2 border-r border-white/30 font-mono" x-text="index + 1"></th>
                                    </template>
                                    <th class="px-3 py-2 border-r border-white/30 font-bold uppercase">Total</th>
                                    <th class="px-3 py-2 border-r border-white/30 font-bold uppercase">PS</th>
                                    <th class="px-3 py-2 border-r-2 border-white font-bold uppercase">WS</th>

                                    <!-- Performance Task Sub-headers -->
                                    <template x-if="max_scores.pt.length === 0">
                                        <th class="px-3 py-2 border-r border-white/30 font-mono">1</th>
                                    </template>
                                    <template x-for="(score, index) in max_scores.pt" :key="index">
                                        <th class="px-3 py-2 border-r border-white/30 font-mono" x-text="index + 1"></th>
                                    </template>
                                    <th class="px-3 py-2 border-r border-white/30 font-bold uppercase">Total</th>
                                    <th class="px-3 py-2 border-r border-white/30 font-bold uppercase">PS</th>
                                    <th class="px-3 py-2 border-r-2 border-white font-bold uppercase">WS</th>

                                    <!-- Quarterly Assessment Sub-headers -->
                                    <th class="px-3 py-2 border-r border-white/30 font-mono">1</th>
                                    <th class="px-3 py-2 border-r border-white/30 font-bold uppercase">PS</th>
                                    <th class="px-3 py-2 border-r-2 border-white font-bold uppercase">WS</th>
                                </tr>
                            </thead>
                            
                            <tbody class="bg-white divide-y-2 divide-navy font-mono text-sm border-b-2 border-navy">
                                <!-- HIGHEST POSSIBLE SCORE ROW -->
                                <tr class="bg-eggshell/30 font-bold text-navy border-b-4 border-navy" x-data="{
                                    get wwRes() { return calcComponent(max_scores.ww, max_scores.ww, weights.ww); },
                                    get ptRes() { return calcComponent(max_scores.pt, max_scores.pt, weights.pt); },
                                    get qaRes() { return calcQA(max_scores.qa, max_scores.qa, weights.qa); }
                                }">
                                    <td class="px-6 py-3 border-r-2 border-navy text-right uppercase tracking-widest text-xs sticky left-0 bg-eggshell/95 z-10 font-display">
                                        Highest Possible Score
                                    </td>
                                    
                                    <!-- WW Max Scores -->
                                    <template x-if="max_scores.ww.length === 0">
                                        <td class="p-0 text-center border-r border-navy/30"></td>
                                    </template>
                                    <template x-for="(score, index) in max_scores.ww" :key="index">
                                        <td class="p-0 text-center border-r border-navy/30 relative">
                                            <input type="number" step="0.01" x-model="max_scores.ww[index]" @if($isFinalized) readonly @endif class="w-full h-full min-w-[4rem] min-h-[3rem] text-center font-bold text-navy bg-transparent border-none focus:ring-2 focus:ring-inset focus:ring-navy focus:bg-white transition-all m-0 p-2" :name="'max_scores[ww]['+index+']'">
                                        </td>
                                    </template>
                                    <td class="px-3 py-3 text-center border-r border-navy/30 bg-white/50" x-text="wwRes.total.toFixed(0)"></td>
                                    <td class="px-3 py-3 text-center border-r border-navy/30 bg-white/50">100.00</td>
                                    <td class="px-3 py-3 text-center border-r-2 border-navy bg-white/50" x-text="weights.ww + '%'"></td>

                                    <!-- PT Max Scores -->
                                    <template x-if="max_scores.pt.length === 0">
                                        <td class="p-0 text-center border-r border-navy/30"></td>
                                    </template>
                                    <template x-for="(score, index) in max_scores.pt" :key="index">
                                        <td class="p-0 text-center border-r border-navy/30 relative">
                                            <input type="number" step="0.01" x-model="max_scores.pt[index]" @if($isFinalized) readonly @endif class="w-full h-full min-w-[4rem] min-h-[3rem] text-center font-bold text-navy bg-transparent border-none focus:ring-2 focus:ring-inset focus:ring-navy focus:bg-white transition-all m-0 p-2" :name="'max_scores[pt]['+index+']'">
                                        </td>
                                    </template>
                                    <td class="px-3 py-3 text-center border-r border-navy/30 bg-white/50" x-text="ptRes.total.toFixed(0)"></td>
                                    <td class="px-3 py-3 text-center border-r border-navy/30 bg-white/50">100.00</td>
                                    <td class="px-3 py-3 text-center border-r-2 border-navy bg-white/50" x-text="weights.pt + '%'"></td>

                                    <!-- QA Max Score -->
                                    <td class="p-0 text-center border-r border-navy/30 relative">
                                        <input type="number" step="0.01" x-model="max_scores.qa" @if($isFinalized) readonly @endif class="w-full h-full min-w-[4rem] min-h-[3rem] text-center font-bold text-navy bg-transparent border-none focus:ring-2 focus:ring-inset focus:ring-navy focus:bg-white transition-all m-0 p-2" name="max_scores[qa]">
                                    </td>
                                    <td class="px-3 py-3 text-center border-r border-navy/30 bg-white/50">100.00</td>
                                    <td class="px-3 py-3 text-center border-r-2 border-navy bg-white/50" x-text="weights.qa + '%'"></td>

                                    <!-- Initial & Quarterly for HPS Row -->
                                    <td class="px-3 py-3 border-r-2 border-navy bg-eggshell/50"></td>
                                    <td class="px-3 py-3 bg-eggshell/50"></td>
                                </tr>

                                <!-- STUDENT ROWS -->
                                @foreach($students as $student)
                                    <tr class="hover:bg-eggshell/30 transition-colors" x-data="{ 
                                        ww_scores: {{ json_encode($student->grade_record->written_work_scores ?? []) }},
                                        pt_scores: {{ json_encode($student->grade_record->performance_task_scores ?? []) }},
                                        qa_score: {{ $student->grade_record->exam_score ?? 0 }},
                                        get wwRes() { return calcComponent(this.ww_scores, max_scores.ww, weights.ww); },
                                        get ptRes() { return calcComponent(this.pt_scores, max_scores.pt, weights.pt); },
                                        get qaRes() { return calcQA(this.qa_score, max_scores.qa, weights.qa); },
                                        get initialGrade() { return calcInitial(this.wwRes.ws, this.ptRes.ws, this.qaRes.ws); },
                                        get finalGrade() { return transmute(this.initialGrade); }
                                    }"
                                    @add-ww.window="ww_scores.push(0)"
                                    @remove-ww.window="if(ww_scores.length > 0) ww_scores.pop()"
                                    @add-pt.window="pt_scores.push(0)"
                                    @remove-pt.window="if(pt_scores.length > 0) pt_scores.pop()">
                                        <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy sticky left-0 bg-white group-hover:bg-eggshell/50 transition-colors z-10">
                                            <div class="text-sm font-display font-bold text-navy uppercase truncate w-48">{{ $student->last_name }}, {{ $student->first_name }}</div>
                                            <div class="text-[10px] text-navy/60 font-mono tracking-widest mt-1">{{ $student->id_number }}</div>
                                        </td>
                                        
                                        <!-- Written Work -->
                                        <template x-if="max_scores.ww.length === 0">
                                            <td class="p-0 text-center border-r border-navy/30 bg-eggshell/10"></td>
                                        </template>
                                        <template x-for="(score, index) in max_scores.ww" :key="index">
                                            <td class="p-0 text-center border-r border-navy/30 bg-eggshell/10 relative">
                                                <input type="number" step="0.01" x-model="ww_scores[index]" @if($isFinalized) readonly @endif
                                                    class="w-full h-full min-w-[4rem] min-h-[3rem] text-center font-mono font-bold text-navy bg-transparent border-none focus:ring-2 focus:ring-inset focus:ring-navy focus:bg-white transition-all m-0 p-2"
                                                    :name="'grades['+{{ $student->grade_record->id }}+'][ww]['+index+']'">
                                            </td>
                                        </template>
                                        <td class="px-3 py-3 text-center border-r border-navy/30 font-bold bg-eggshell/20" x-text="wwRes.total.toFixed(0)"></td>
                                        <td class="px-3 py-3 text-center border-r border-navy/30 font-bold text-navy/70 bg-eggshell/20" x-text="wwRes.ps.toFixed(2)"></td>
                                        <td class="px-3 py-3 text-center border-r-2 border-navy font-bold text-navy bg-eggshell/30" x-text="wwRes.ws.toFixed(2)"></td>

                                        <!-- Performance Tasks -->
                                        <template x-if="max_scores.pt.length === 0">
                                            <td class="p-0 text-center border-r border-navy/30 bg-eggshell/10"></td>
                                        </template>
                                        <template x-for="(score, index) in max_scores.pt" :key="index">
                                            <td class="p-0 text-center border-r border-navy/30 bg-eggshell/10 relative">
                                                <input type="number" step="0.01" x-model="pt_scores[index]" @if($isFinalized) readonly @endif
                                                    class="w-full h-full min-w-[4rem] min-h-[3rem] text-center font-mono font-bold text-navy bg-transparent border-none focus:ring-2 focus:ring-inset focus:ring-navy focus:bg-white transition-all m-0 p-2"
                                                    :name="'grades['+{{ $student->grade_record->id }}+'][pt]['+index+']'">
                                            </td>
                                        </template>
                                        <td class="px-3 py-3 text-center border-r border-navy/30 font-bold bg-eggshell/20" x-text="ptRes.total.toFixed(0)"></td>
                                        <td class="px-3 py-3 text-center border-r border-navy/30 font-bold text-navy/70 bg-eggshell/20" x-text="ptRes.ps.toFixed(2)"></td>
                                        <td class="px-3 py-3 text-center border-r-2 border-navy font-bold text-navy bg-eggshell/30" x-text="ptRes.ws.toFixed(2)"></td>

                                        <!-- Exam -->
                                        <td class="p-0 text-center border-r border-navy/30 bg-eggshell/10 relative">
                                            <input type="number" step="0.01" x-model="qa_score" @if($isFinalized) readonly @endif
                                                class="w-full h-full min-w-[5rem] min-h-[3rem] text-center font-mono font-bold text-navy bg-transparent border-none focus:ring-2 focus:ring-inset focus:ring-navy focus:bg-white transition-all m-0 p-2"
                                                name="grades[{{ $student->grade_record->id }}][qa]">
                                        </td>
                                        <td class="px-3 py-3 text-center border-r border-navy/30 font-bold text-navy/70 bg-eggshell/20" x-text="qaRes.ps.toFixed(2)"></td>
                                        <td class="px-3 py-3 text-center border-r-2 border-navy font-bold text-navy bg-eggshell/30" x-text="qaRes.ws.toFixed(2)"></td>

                                        <!-- Initial Grade -->
                                        <td class="px-3 py-3 text-center border-r-2 border-navy font-bold text-navy bg-white" x-text="initialGrade.toFixed(2)"></td>

                                        <!-- Quarterly Grade -->
                                        <td class="px-3 py-3 text-center bg-navy text-white">
                                            <span class="text-2xl font-display font-bold tracking-tight" x-text="finalGrade.toFixed(0)"></span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>

            <!-- Unlock Request Modal -->
            <div x-data="{ open: false }" @open-unlock-modal.window="open = true" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-navy/80 transition-opacity" @click="open = false"></div>
                    <div class="bg-white border-4 border-navy p-8 shadow-[12px_12px_0_0_#0B132B] max-w-lg w-full relative z-10">
                        <h3 class="font-display font-bold text-navy text-2xl uppercase tracking-widest mb-4">Request Grade Unlock</h3>
                        <p class="text-navy/60 text-sm mb-6 uppercase tracking-wide">Explain to the principal why grades need to be modified.</p>
                        
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
