<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h2 class="font-display font-bold text-4xl text-navy uppercase tracking-tighter">
                        Manage Students: <span class="text-crimson">{{ $section->name }}</span>
                    </h2>
                    <p class="text-navy/60 font-medium uppercase tracking-widest text-sm mt-2">Grade {{ $section->grade_level }} Advisory</p>
                </div>
                <a href="{{ route('teacher.dashboard') }}" class="bg-navy text-eggshell border-2 border-navy px-6 py-2 font-display font-bold text-xs uppercase tracking-widest hover:bg-eggshell hover:text-navy transition-all duration-200 shadow-[4px_4px_0_0_#0B132B]">
                    Back to Dashboard
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Add Student Form -->
                <div class="md:col-span-1">
                    <div class="bg-white border-2 border-navy p-6 shadow-[8px_8px_0_0_#0B132B]">
                        <h3 class="font-display font-bold text-navy text-xl uppercase tracking-widest mb-6">Add New Student</h3>
                        
                        <form action="{{ route('teacher.adviser.store-student', $section) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-navy font-bold uppercase tracking-widest text-xs mb-2">First Name</label>
                                <input type="text" name="first_name" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none">
                            </div>
                            <div>
                                <label class="block text-navy font-bold uppercase tracking-widest text-xs mb-2">Last Name</label>
                                <input type="text" name="last_name" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none">
                            </div>
                            <div>
                                <label class="block text-navy font-bold uppercase tracking-widest text-xs mb-2">Student ID Number</label>
                                <input type="text" name="id_number" required class="w-full border-2 border-navy p-3 font-mono text-sm focus:ring-0 focus:border-crimson outline-none">
                            </div>
                            
                            <button type="submit" class="w-full bg-crimson text-white border-2 border-navy py-4 font-display font-bold text-sm uppercase tracking-widest hover:bg-navy hover:shadow-[4px_4px_0_0_#0B132B] transition-all duration-200">
                                Register Student
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Student List -->
                <div class="md:col-span-2">
                    <div class="bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B] overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-navy text-eggshell">
                                    <th class="px-6 py-4 font-display font-bold uppercase tracking-widest text-sm border-b-2 border-navy">ID Number</th>
                                    <th class="px-6 py-4 font-display font-bold uppercase tracking-widest text-sm border-b-2 border-navy">Name</th>
                                    <th class="px-6 py-4 font-display font-bold uppercase tracking-widest text-sm border-b-2 border-navy text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-navy/10">
                                @forelse($students as $student)
                                    <tr class="hover:bg-eggshell/30 transition-colors">
                                        <td class="px-6 py-4 font-mono text-sm text-navy font-bold">{{ $student->id_number }}</td>
                                        <td class="px-6 py-4 font-display font-bold text-navy uppercase tracking-tight">{{ $student->last_name }}, {{ $student->first_name }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-navy/40 hover:text-crimson transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center text-navy/40 font-display font-bold uppercase tracking-widest">No students registered yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
