<?php

namespace App\Services;

use App\Models\Grade;

class GradeCalculationService
{
    /**
     * Calculate the quarterly grade based on DepEd weighting.
     * 
     * Formula: 
     * (Written Work Total / Written Max) * Weight + 
     * (Performance Task Total / Performance Max) * Weight + 
     * (Quarterly Exam / Exam Max) * Weight
     */
    public function calculate(Grade $grade): float
    {
        $subject = $grade->subject;
        
        $writtenScore = $this->calculateComponentScore($grade->written_work_scores);
        $performanceScore = $this->calculateComponentScore($grade->performance_task_scores);
        $examScore = $grade->exam_score ?? 0;

        // Note: In a real system, we would also need the 'Max Scores' for these components.
        // For this implementation, we'll assume the scores are already percentages (0-100) 
        // for simplicity, or we can assume max scores are 100.
        
        $finalGrade = (
            ($writtenScore * ($subject->written_weight / 100)) +
            ($performanceScore * ($subject->performance_weight / 100)) +
            ($examScore * ($subject->exam_weight / 100))
        );

        return round($finalGrade, 2);
    }

    /**
     * Calculates the average of a scores array.
     */
    private function calculateComponentScore(?array $scores): float
    {
        if (empty($scores)) {
            return 0;
        }

        return array_sum($scores) / count($scores);
    }
}
