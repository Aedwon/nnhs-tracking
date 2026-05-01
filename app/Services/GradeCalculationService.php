<?php

namespace App\Services;

use App\Models\Grade;

class GradeCalculationService
{
    /**
     * Calculate the quarterly grade based on DepEd weighting and transmutation.
     */
    public function calculate(Grade $grade): float
    {
        $subject = $grade->subject;
        $sts = \App\Models\SubjectTeacherSection::where('teacher_id', $grade->teacher_id)
            ->where('subject_id', $grade->subject_id)
            ->where('section_id', $grade->section_id)
            ->first();

        if (!$sts) return 0;

        $wwWS = $this->calculateWeightedScore(
            $grade->written_work_scores, 
            $sts->ww_max_scores, 
            $subject->written_weight
        );

        $ptWS = $this->calculateWeightedScore(
            $grade->performance_task_scores, 
            $sts->pt_max_scores, 
            $subject->performance_weight
        );

        $qaWS = $this->calculateWeightedScore(
            [$grade->exam_score ?? 0], 
            [$sts->qa_max_score ?? 0], 
            $subject->exam_weight
        );

        $initialGrade = $wwWS + $ptWS + $qaWS;
        
        return $this->transmute($initialGrade);
    }

    /**
     * Calculates the Weighted Score (WS) for a component.
     * PS = (Total Student Score / Total Highest Possible Score) * 100
     * WS = PS * Weight
     */
    private function calculateWeightedScore(?array $studentScores, ?array $maxScores, float $weight): float
    {
        if (empty($studentScores) || empty($maxScores)) {
            return 0;
        }

        $studentTotal = array_sum($studentScores);
        $maxTotal = array_sum($maxScores);

        if ($maxTotal <= 0) {
            return 0;
        }

        $percentageScore = ($studentTotal / $maxTotal) * 100;
        return $percentageScore * ($weight / 100);
    }

    /**
     * DepEd Transmutation Table (D.O. 8, s. 2015)
     */
    private function transmute(float $initialGrade): float
    {
        if ($initialGrade >= 100) return 100;
        if ($initialGrade >= 98.40) return 99;
        if ($initialGrade >= 96.80) return 98;
        if ($initialGrade >= 95.20) return 97;
        if ($initialGrade >= 93.60) return 96;
        if ($initialGrade >= 92.00) return 95;
        if ($initialGrade >= 90.40) return 94;
        if ($initialGrade >= 88.80) return 93;
        if ($initialGrade >= 87.20) return 92;
        if ($initialGrade >= 85.60) return 91;
        if ($initialGrade >= 84.00) return 90;
        if ($initialGrade >= 82.40) return 89;
        if ($initialGrade >= 80.80) return 88;
        if ($initialGrade >= 79.20) return 87;
        if ($initialGrade >= 77.60) return 86;
        if ($initialGrade >= 76.00) return 85;
        if ($initialGrade >= 74.40) return 84;
        if ($initialGrade >= 72.80) return 83;
        if ($initialGrade >= 71.20) return 82;
        if ($initialGrade >= 69.60) return 81;
        if ($initialGrade >= 68.00) return 80;
        if ($initialGrade >= 66.40) return 79;
        if ($initialGrade >= 64.80) return 78;
        if ($initialGrade >= 63.20) return 77;
        if ($initialGrade >= 61.60) return 76;
        if ($initialGrade >= 60.00) return 75;
        if ($initialGrade >= 56.00) return 74;
        if ($initialGrade >= 52.00) return 73;
        if ($initialGrade >= 48.00) return 72;
        if ($initialGrade >= 44.00) return 71;
        if ($initialGrade >= 40.00) return 70;
        if ($initialGrade >= 36.00) return 69;
        if ($initialGrade >= 32.00) return 68;
        if ($initialGrade >= 28.00) return 67;
        if ($initialGrade >= 24.00) return 66;
        if ($initialGrade >= 20.00) return 65;
        if ($initialGrade >= 16.00) return 64;
        if ($initialGrade >= 12.00) return 63;
        if ($initialGrade >= 8.00) return 62;
        if ($initialGrade >= 4.00) return 61;
        return 60;
    }
}
