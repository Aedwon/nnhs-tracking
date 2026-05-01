<?php

namespace App\Services;

use App\Models\Section;
use App\Models\Subject;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExcelInjectionService
{
    /**
     * Inject grades into an official DepEd template.
     */
    public function injectGrades(Section $section, Subject $subject, string $templatePath, string $outputPath)
    {
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Fetch finalized grades for this section and subject
        $grades = \App\Models\Grade::with('student')
            ->where('section_id', $section->id)
            ->where('subject_id', $subject->id)
            ->where('is_finalized', true)
            ->get();

        // Placeholder logic: Finding students in the spreadsheet
        // In a real DepEd template (SF9/SF10), names are usually in Column B starting from a specific row.
        $startRow = 10; 
        $maxRow = 60;

        for ($row = $startRow; $row <= $maxRow; $row++) {
            $nameInSheet = $sheet->getCell('B' . $row)->getValue();
            
            if (!$nameInSheet) continue;

            // Try to match with our database students
            foreach ($grades as $grade) {
                $fullName = $grade->student->last_name . ', ' . $grade->student->first_name;
                
                if (stripos($nameInSheet, $grade->student->last_name) !== false && 
                    stripos($nameInSheet, $grade->student->first_name) !== false) {
                    
                    // Injecting Grade (e.g., Column S for Final Grade in some SF9s)
                    $sheet->setCellValue('S' . $row, $grade->grade);
                }
            }
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);
    }
}
