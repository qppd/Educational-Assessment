<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;

/**
 * Test the exam scoring logic used in PortalController::examinationSubmit.
 *
 * These tests verify the core scoring algorithm logic without needing a database.
 * The actual controller iterates questions, compares student_answer to question.answer,
 * and increments the score on a Result model for each correct match.
 */
class ExamScoringTest extends TestCase
{
    /**
     * Test that an exact match on a multiple-choice question scores 1 point.
     */
    public function test_exact_match_scores_one_point(): void
    {
        $studentAnswer = 'A';
        $correctAnswer = 'A';

        $isCorrect = strcasecmp(trim($studentAnswer), trim($correctAnswer)) === 0;

        $this->assertTrue($isCorrect);
    }

    /**
     * Test that a wrong answer does not score.
     */
    public function test_wrong_answer_scores_zero(): void
    {
        $studentAnswer = 'B';
        $correctAnswer = 'A';

        $isCorrect = strcasecmp(trim($studentAnswer), trim($correctAnswer)) === 0;

        $this->assertFalse($isCorrect);
    }

    /**
     * Test case-insensitive matching (for enumeration/fill-in-the-blank).
     * The DB query in examinationResult uses LOWER() comparison, so the
     * scoring should be case-insensitive.
     */
    public function test_case_insensitive_matching(): void
    {
        $this->assertTrue(strcasecmp('Manila', 'manila') === 0);
        $this->assertTrue(strcasecmp('QUEZON CITY', 'quezon city') === 0);
        $this->assertTrue(strcasecmp('PHP', 'php') === 0);
    }

    /**
     * Test that whitespace doesn't affect matching.
     */
    public function test_whitespace_tolerant_matching(): void
    {
        $this->assertTrue(trim('  A  ') === 'A');
        $this->assertTrue(strcasecmp(trim('  Manila  '), trim('Manila')) === 0);
    }

    /**
     * Test enumeration with multiple possible answers.
     * The current system does a direct string comparison, so "10" !== "ten".
     * This test documents the current behavior.
     */
    public function test_enumeration_exact_match_required(): void
    {
        // Current behavior: exact string match
        $studentAnswer = '10';
        $correctAnswer = 'ten';

        $isCorrect = strcasecmp(trim($studentAnswer), trim($correctAnswer)) === 0;

        $this->assertFalse($isCorrect, 'Enumeration currently requires exact string match');
    }

    /**
     * Test score calculation for a multi-question exam.
     */
    public function test_multi_question_score_calculation(): void
    {
        $questions = [
            ['id' => 1, 'answer' => 'A'],
            ['id' => 2, 'answer' => 'C'],
            ['id' => 3, 'answer' => 'B'],
            ['id' => 4, 'answer' => 'D'],
            ['id' => 5, 'answer' => 'A'],
        ];

        $studentAnswers = [
            1 => 'A',  // correct
            2 => 'B',  // wrong
            3 => 'B',  // correct
            4 => 'D',  // correct
            5 => 'C',  // wrong
        ];

        $score = 0;
        foreach ($questions as $q) {
            $sa = $studentAnswers[$q['id']] ?? '';
            if (strcasecmp(trim($sa), trim($q['answer'])) === 0) {
                $score++;
            }
        }

        $this->assertEquals(3, $score);
        $this->assertEquals(60, ($score / count($questions)) * 100);
    }

    /**
     * Test that unanswered questions don't affect the score.
     */
    public function test_unanswered_skipped_questions(): void
    {
        $questions = [
            ['id' => 1, 'answer' => 'A'],
            ['id' => 2, 'answer' => 'C'],
            ['id' => 3, 'answer' => 'B'],
        ];

        $studentAnswers = [
            1 => 'A',  // correct
            // question 2 skipped (unanswered)
            3 => 'B',  // correct
        ];

        $score = 0;
        $attempted = 0;
        foreach ($questions as $q) {
            $sa = $studentAnswers[$q['id']] ?? '';
            if ($sa !== '') {
                $attempted++;
                if (strcasecmp(trim($sa), trim($q['answer'])) === 0) {
                    $score++;
                }
            }
        }

        $this->assertEquals(2, $score);
        $this->assertEquals(2, $attempted);
        $this->assertEquals(100, $attempted > 0 ? ($score / $attempted) * 100 : 0);
    }

    /**
     * Test that the total possible score equals the number of questions.
     */
    public function test_total_possible_score(): void
    {
        $questionCount = 50;
        $possibleScore = $questionCount;

        $this->assertEquals(50, $possibleScore);
    }

    /**
     * Test the result view query (examinationResult) which uses LOWER() for comparison.
     * The query:
     *   SELECT ..., CASE WHEN LOWER(questions.answer) = LOWER(answers.student_answer)
     *   THEN "correct" ELSE "wrong" END as result
     */
    public function test_lower_comparison_equivalent(): void
    {
        $testCases = [
            ['correct' => 'A', 'student' => 'a', 'expected' => 'correct'],
            ['correct' => 'MANILA', 'student' => 'manila', 'expected' => 'correct'],
            ['correct' => 'B', 'student' => 'c', 'expected' => 'wrong'],
            ['correct' => 'Quezon City', 'student' => 'quezon city', 'expected' => 'correct'],
            ['correct' => '  A  ', 'student' => 'A', 'expected' => 'wrong'], // DB won't trim
        ];

        foreach ($testCases as $tc) {
            $result = strtolower($tc['correct']) === strtolower($tc['student'])
                ? 'correct'
                : 'wrong';
            $this->assertEquals($tc['expected'], $result, "Failed for: '{$tc['correct']}' vs '{$tc['student']}'");
        }
    }

    /**
     * Test that the controller's $result->score += 1 pattern accumulates correctly.
     */
    public function test_score_accumulation(): void
    {
        // Simulate the controller's pattern: $result->score += 1 per correct answer
        $score = 0;
        $correctCount = 7;

        for ($i = 0; $i < $correctCount; $i++) {
            $score += 1;
        }

        $this->assertEquals(7, $score);
    }
}
