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
        weights: { 
            ww: {{ $subject->written_weight }}, 
            pt: {{ $subject->performance_weight }}, 
            qa: {{ $subject->exam_weight }} 
        },
        calculateGrade(ww, pt, qa) {
            let ww_avg = ww.length ? ww.reduce((a, b) => a + parseFloat(b || 0), 0) / ww.length : 0;
            let pt_avg = pt.length ? pt.reduce((a, b) => a + parseFloat(b || 0), 0) / pt.length : 0;
            let final = (ww_avg * (this.weights.ww / 100)) + (pt_avg * (this.weights.pt / 100)) + (parseFloat(qa || 0) * (this.weights.qa / 100));
            return final.toFixed(2);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('teacher.grades.update-sheet') }}" method="POST">
                @csrf
                <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B]">
                    <div class="p-6 border-b-2 border-navy bg-eggshell/50 flex justify-between items-center">
                        <h3 class="font-display font-bold text-navy text-xl uppercase tracking-widest">Grade Entry Sheet</h3>
                        <button type="submit" class="bg-crimson text-white border-2 border-navy px-8 py-3 font-display font-bold text-sm uppercase tracking-widest hover:bg-navy hover:text-eggshell shadow-[4px_4px_0_0_#0B132B] hover:shadow-[6px_6px_0_0_#0B132B] hover:-translate-y-1 active:translate-y-1 active:shadow-none transition-all duration-200">
                            SAVE ALL CHANGES
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead class="bg-navy text-eggshell">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest border-r-2 border-white">Student Name</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest border-r-2 border-white">Written Work</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest border-r-2 border-white">Performance Task</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest border-r-2 border-white">Quarterly Exam</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest bg-crimson">Quarterly Grade</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y-2 divide-navy">
                                @foreach($students as $student)
                                    <tr class="hover:bg-eggshell/30 transition-colors" x-data="{ 
                                        ww_scores: {{ json_encode($student->grade_record->written_work_scores ?? [0,0,0]) }},
                                        pt_scores: {{ json_encode($student->grade_record->performance_task_scores ?? [0,0,0]) }},
                                        qa_score: {{ $student->grade_record->exam_score ?? 0 }}
                                    }">
                                        <td class="px-6 py-4 whitespace-nowrap border-r-2 border-navy">
                                            <div class="text-sm font-display font-bold text-navy uppercase">{{ $student->last_name }}, {{ $student->first_name }}</div>
                                            <div class="text-[10px] text-navy/60 font-mono tracking-widest mt-1">{{ $student->id_number }}</div>
                                        </td>
                                        
                                        <!-- Written Work -->
                                        <td class="px-6 py-4 text-center border-r-2 border-navy bg-eggshell/10">
                                            <div class="flex justify-center space-x-2">
                                                <template x-for="(score, index) in ww_scores" :key="index">
                                                    <input type="number" step="0.01" x-model="ww_scores[index]" 
                                                        class="w-16 h-12 text-center font-mono font-bold text-navy border-2 border-navy/20 bg-white focus:border-navy focus:ring-0 shadow-[2px_2px_0_0_#0B132B] transition-all"
                                                        :name="'grades['+{{ $student->grade_record->id }}+'][ww]['+index+']'">
                                                </template>
                                            </div>
                                        </td>

                                        <!-- Performance Tasks -->
                                        <td class="px-6 py-4 text-center border-r-2 border-navy bg-eggshell/10">
                                            <div class="flex justify-center space-x-2">
                                                <template x-for="(score, index) in pt_scores" :key="index">
                                                    <input type="number" step="0.01" x-model="pt_scores[index]" 
                                                        class="w-16 h-12 text-center font-mono font-bold text-navy border-2 border-navy/20 bg-white focus:border-navy focus:ring-0 shadow-[2px_2px_0_0_#0B132B] transition-all"
                                                        :name="'grades['+{{ $student->grade_record->id }}+'][pt]['+index+']'">
                                                </template>
                                            </div>
                                        </td>

                                        <!-- Exam -->
                                        <td class="px-6 py-4 text-center border-r-2 border-navy bg-eggshell/10">
                                            <input type="number" step="0.01" x-model="qa_score" 
                                                class="w-24 h-12 text-center font-mono font-bold text-navy border-2 border-navy/20 bg-white focus:border-navy focus:ring-0 shadow-[2px_2px_0_0_#0B132B] transition-all"
                                                name="grades[{{ $student->grade_record->id }}][qa]">
                                        </td>

                                        <!-- Result -->
                                        <td class="px-6 py-4 text-center bg-navy text-white">
                                            <span class="text-2xl font-display font-bold tracking-tight" x-text="calculateGrade(ww_scores, pt_scores, qa_score)"></span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
