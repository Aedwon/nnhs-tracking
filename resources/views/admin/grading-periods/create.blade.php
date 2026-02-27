<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Grading Period') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <form method="POST" action="{{ route('admin.grading-periods.store') }}">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <x-input-label for="name" :value="__('Period Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                placeholder="e.g. 1st Semester Midterm" required />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('Start Date')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date"
                                    required />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('End Date')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date"
                                    required />
                            </div>
                        </div>

                        <div class="pt-4 border-t">
                            <h3 class="font-bold text-gray-800 mb-4">Submission Deadline</h3>
                            <x-input-label for="deadline_at" :value="__('Deadline Date & Time')" />
                            <x-text-input id="deadline_at" class="block mt-1 w-full" type="datetime-local"
                                name="deadline_at" required />
                            <p class="mt-2 text-xs text-gray-400">Teachers must upload grades before this time to avoid
                                "Late" status.</p>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold">
                                Save Period & Deadline
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>