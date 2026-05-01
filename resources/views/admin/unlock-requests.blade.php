<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center border-b-4 border-navy pb-6">
                <div>
                    <h2 class="font-display font-bold text-5xl text-navy uppercase tracking-tighter">
                        Unlock <span class="text-crimson">Requests</span>
                    </h2>
                    <p class="text-navy/60 font-medium uppercase tracking-widest text-sm mt-2">Grade Modification Authorizations</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-navy text-eggshell px-4 py-2 font-mono text-sm font-bold">
                        PENDING: {{ $requests->where('status', 'pending')->count() }}
                    </div>
                </div>
            </div>

            <div class="bg-white border-4 border-navy shadow-[12px_12px_0_0_#0B132B] overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-navy text-eggshell">
                            <th class="px-6 py-5 font-display font-bold uppercase tracking-widest text-sm">Teacher</th>
                            <th class="px-6 py-5 font-display font-bold uppercase tracking-widest text-sm">Target</th>
                            <th class="px-6 py-5 font-display font-bold uppercase tracking-widest text-sm">Reason</th>
                            <th class="px-6 py-5 font-display font-bold uppercase tracking-widest text-sm text-center">Status</th>
                            <th class="px-6 py-5 font-display font-bold uppercase tracking-widest text-sm text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-4 divide-navy">
                        @forelse($requests as $request)
                            <tr class="hover:bg-eggshell/50 transition-colors">
                                <td class="px-6 py-6">
                                    <div class="font-display font-bold text-navy uppercase text-lg leading-tight">{{ $request->teacher->name }}</div>
                                    <div class="font-mono text-[10px] text-navy/40 uppercase">{{ $request->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-col">
                                        <span class="bg-crimson text-white px-2 py-1 text-[10px] font-bold uppercase tracking-widest self-start mb-1">
                                            {{ $request->section->name }}
                                        </span>
                                        <span class="font-display font-bold text-navy uppercase tracking-tight text-sm">
                                            {{ $request->subject->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <p class="text-navy/80 font-medium text-sm leading-relaxed max-w-xs italic">"{{ $request->reason }}"</p>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="inline-block px-3 py-1 border-2 border-navy font-display font-bold text-[10px] uppercase tracking-widest 
                                        @if($request->status === 'pending') bg-yellow-400 @elseif($request->status === 'approved') bg-green-400 @else bg-red-400 @endif shadow-[2px_2px_0_0_#0B132B]">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    @if($request->status === 'pending')
                                        <div class="flex justify-end space-x-2">
                                            <form action="{{ route('admin.unlock-requests.process', $request) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="bg-navy text-eggshell px-4 py-2 font-display font-bold text-[10px] uppercase tracking-widest hover:bg-crimson transition-colors">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.unlock-requests.process', $request) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="border-2 border-navy px-4 py-2 font-display font-bold text-[10px] uppercase tracking-widest hover:bg-eggshell transition-colors">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-navy/20 font-display font-bold uppercase text-[10px] tracking-widest italic">Processed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="font-display font-bold text-navy/20 text-4xl uppercase tracking-tighter">Clear Slate</div>
                                    <p class="text-navy/40 uppercase tracking-widest text-xs font-bold mt-2">No pending unlock requests found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
