<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SubjectTeacherSection;
use App\Models\Grade;
use App\Models\GradingPeriod;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);

        // 2. Users
        $principal = User::firstOrCreate(
            ['email' => 'principal@nnhs.edu.ph'],
            ['name' => 'Dr. Principal', 'password' => Hash::make('password')]
        );
        $principal->assignRole($adminRole);

        $teacher = User::firstOrCreate(
            ['email' => 'teacher@nnhs.edu.ph'],
            ['name' => 'Ms. Teacher Adviser', 'password' => Hash::make('password')]
        );
        $teacher->assignRole($teacherRole);

        // 3. Grading Period
        $period = GradingPeriod::firstOrCreate(['name' => '1st Quarter'], [
            'is_active' => true,
            'start_date' => now()->startOfQuarter(),
            'end_date' => now()->endOfQuarter(),
        ]);

        // 4. Subjects
        $math = Subject::firstOrCreate(['subject_code' => 'MATH10'], [
            'name' => 'Mathematics 10',
            'written_weight' => 30,
            'performance_weight' => 50,
            'exam_weight' => 20,
        ]);
        $english = Subject::firstOrCreate(['subject_code' => 'ENG10'], [
            'name' => 'English 10',
            'written_weight' => 40,
            'performance_weight' => 40,
            'exam_weight' => 20,
        ]);

        // 5. Section
        $sectionA = Section::firstOrCreate(['name' => '10-A'], [
            'grade_level' => 'Grade 10',
            'adviser_id' => $teacher->id,
        ]);
        
        $sectionB = Section::firstOrCreate(['name' => '10-B'], [
            'grade_level' => 'Grade 10',
            'adviser_id' => $principal->id, // Just to test
        ]);

        // 6. Students
        $students = [];
        for ($i = 1; $i <= 5; $i++) {
            $students[] = Student::firstOrCreate(['id_number' => '2026-000' . $i], [
                'first_name' => 'Student' . $i,
                'last_name' => 'Sample',
                'gender' => $i % 2 == 0 ? 'Female' : 'Male',
                'section_id' => $sectionA->id,
            ]);
        }

        // 7. Assignments
        SubjectTeacherSection::firstOrCreate([
            'subject_id' => $math->id,
            'teacher_id' => $teacher->id,
            'section_id' => $sectionA->id,
            'school_year' => '2026-2027',
        ]);
        
        SubjectTeacherSection::firstOrCreate([
            'subject_id' => $english->id,
            'teacher_id' => $teacher->id,
            'section_id' => $sectionA->id,
            'school_year' => '2026-2027',
        ]);

        // 8. Some Grades
        foreach ($students as $student) {
            Grade::firstOrCreate([
                'student_id' => $student->id,
                'subject_id' => $math->id,
                'section_id' => $sectionA->id,
                'teacher_id' => $teacher->id,
                'grading_period_id' => $period->id,
            ], [
                'written_work_scores' => [85, 90, 88],
                'performance_task_scores' => [92, 95],
                'exam_score' => 88,
                'grade' => 90.5,
                'is_finalized' => true,
                'submitted_at' => now(),
            ]);
        }
    }
}
