<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-bold text-xl text-navy uppercase tracking-widest">
            {{ __('Subject Assignments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
            
            <!-- My Current Subjects -->
            <section>
                <h3 class="font-display font-bold text-navy text-sm uppercase tracking-widest border-l-4 border-crimson pl-4 mb-6">My Active Load</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($mySubjects as $subj)
                        <div class="bg-white border-2 border-navy shadow-[4px_4px_0_0_#0B132B] p-5 flex justify-between items-center">
                            <div>
                                <h4 class="font-display font-bold text-navy text-lg uppercase">{{ $subj->name }}</h4>
                                <p class="font-mono text-[10px] text-navy/60 uppercase">{{ $subj->section->name }}</p>
                            </div>
                            <form action="{{ route('teacher.assignments.unclaim', $subj) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[9px] font-bold text-crimson uppercase hover:underline">Unclaim</button>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center border-2 border-dashed border-navy/10 font-mono text-[10px] text-navy/40 uppercase">
                            You haven't claimed any subjects yet.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Available Slots -->
            <section>
                <h3 class="font-display font-bold text-navy text-sm uppercase tracking-widest border-l-4 border-navy pl-4 mb-6">Available Subject Slots</h3>
                <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-navy text-eggshell">
                                    <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-[10px]">Section</th>
                                    <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-[10px]">Level</th>
                                    <th class="px-6 py-4 text-left font-display font-bold uppercase tracking-widest text-[10px]">Available Subject</th>
                                    <th class="px-6 py-4 text-center font-display font-bold uppercase tracking-widest text-[10px]">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-navy">
                                @php $hasSlots = false; @endphp
                                @foreach($sections as $section)
                                    @foreach($section->subjects as $subj)
                                        @php $hasSlots = true; @endphp
                                        <tr class="hover:bg-eggshell/30 transition-colors font-mono text-sm text-navy">
                                            <td class="px-6 py-4 whitespace-nowrap font-bold uppercase">{{ $section->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs uppercase">{{ $section->level }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap font-bold text-crimson uppercase">{{ $subj->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <form action="{{ route('teacher.assignments.claim', $subj) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-navy text-eggshell px-6 py-2 text-[10px] font-bold uppercase tracking-widest hover:bg-crimson transition-colors border-2 border-navy">
                                                        Claim This Subject
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach

                                @if(!$hasSlots)
                                    <tr>
                                        <td colspan="4" class="px-6 py-20 text-center text-navy/40 font-mono text-xs uppercase tracking-widest">
                                            No available subject slots found. Check with section advisers.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
