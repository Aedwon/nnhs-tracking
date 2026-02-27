<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Grade Submission') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <form method="POST" action="{{ route('teacher.grades.store') }}">
                        @csrf

                        <!-- Grading Period -->
                        <div class="mb-6">
                            <label for="grading_period_id" class="block text-sm font-bold text-gray-700 mb-2">Grading
                                Period</label>
                            <select name="grading_period_id" id="grading_period_id"
                                class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                required>
                                <option value="">Select Period</option>
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('grading_period_id')" class="mt-2" />
                        </div>

                        <!-- Student Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="student_id_number"
                                    class="block text-sm font-bold text-gray-700 mb-2">Student ID</label>
                                <input type="text" name="student_id_number" id="student_id_number"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                    placeholder="e.g. 2025-0001" required>
                                <x-input-error :messages="$errors->get('student_id_number')" class="mt-2" />
                            </div>
                            <div>
                                <label for="student_name" class="block text-sm font-bold text-gray-700 mb-2">Full
                                    Name</label>
                                <input type="text" name="student_name" id="student_name"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                    required>
                                <x-input-error :messages="$errors->get('student_name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Subject & Grade -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="subject_code" class="block text-sm font-bold text-gray-700 mb-2">Subject
                                    Code</label>
                                <input type="text" name="subject_code" id="subject_code"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                    placeholder="e.g. CS101" required>
                                <x-input-error :messages="$errors->get('subject_code')" class="mt-2" />
                            </div>
                            <div>
                                <label for="grade" class="block text-sm font-bold text-gray-700 mb-2">Grade</label>
                                <input type="number" step="0.01" name="grade" id="grade"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                    required>
                                <x-input-error :messages="$errors->get('grade')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('teacher.dashboard') }}"
                                class="px-6 py-2 text-gray-600 font-bold hover:text-gray-900 transition mt-2">Cancel</a>
                            <button type="submit"
                                class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg transition-transform transform hover:-translate-y-1">
                                Submit Grade
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>