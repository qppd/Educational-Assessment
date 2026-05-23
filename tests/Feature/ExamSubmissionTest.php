<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Examination;
use App\Models\Question;
use App\Models\Result;
use App\Models\Answer;

/**
 * Feature tests for the exam submission flow.
 *
 * Prerequisites before running:
 * 1. Set up a test database (SQLite in-memory or test MySQL)
 * 2. Uncomment DB_CONNECTION and DB_DATABASE in phpunit.xml
 * 3. Run: php artisan migrate --env=testing
 *
 * These tests use RefreshDatabase to reset between runs.
 */
class ExamSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $examination;
    protected $questions;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test student
        $this->student = User::factory()->create([
            'role' => 3,
            'status' => 1,
        ]);

        // Create a test examination
        $this->examination = Examination::factory()->create([
            'title' => 'Test Exam',
            'duration' => 10,
            'limit' => 100,
            'status' => 1,
        ]);

        // Create test questions
        $this->questions = Question::factory()->count(5)->create([
            'examination_id' => $this->examination->id,
            'status' => 1,
        ]);
    }

    /**
     * Test that submitting answers creates Answer records.
     */
    public function test_submit_answers_creates_answer_records(): void
    {
        $this->actingAs($this->student);

        $questionIds = $this->questions->pluck('id')->toArray();
        $answers = [];
        foreach ($this->questions as $q) {
            $answers[$q->id] = $q->answer; // All correct
        }

        $response = $this->post('/portal/examination/submit', [
            'examination_id' => $this->examination->id,
            'question_ids' => $questionIds,
            'answers_data' => $answers,
        ]);

        $response->assertRedirect('/portal/dashboard');

        // Verify Answer records were created
        $this->assertDatabaseCount('answers', 5);
        foreach ($this->questions as $q) {
            $this->assertDatabaseHas('answers', [
                'student_id' => $this->student->id,
                'examination_id' => $this->examination->id,
                'question_id' => $q->id,
            ]);
        }
    }

    /**
     * Test that correct answers increment the score.
     */
    public function test_correct_answers_increment_score(): void
    {
        $this->actingAs($this->student);

        $questionIds = $this->questions->pluck('id')->toArray();
        $answers = [];
        foreach ($this->questions as $q) {
            $answers[$q->id] = $q->answer; // All correct
        }

        $this->post('/portal/examination/submit', [
            'examination_id' => $this->examination->id,
            'question_ids' => $questionIds,
            'answers_data' => $answers,
        ]);

        // Score should equal number of correct answers
        $result = Result::where('user_id', $this->student->id)
            ->where('examination_id', $this->examination->id)
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals(5, $result->score);
    }

    /**
     * Test that wrong answers don't increment the score.
     */
    public function test_wrong_answers_dont_increment_score(): void
    {
        $this->actingAs($this->student);

        $questionIds = $this->questions->pluck('id')->toArray();
        $answers = [];
        foreach ($this->questions as $q) {
            // Deliberately wrong answer
            $answers[$q->id] = 'WRONG_ANSWER_' . $q->id;
        }

        $this->post('/portal/examination/submit', [
            'examination_id' => $this->examination->id,
            'question_ids' => $questionIds,
            'answers_data' => $answers,
        ]);

        $result = Result::where('user_id', $this->student->id)
            ->where('examination_id', $this->examination->id)
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals(0, $result->score);
    }

    /**
     * Test mixed correct/wrong answers produce the correct partial score.
     */
    public function test_mixed_answers_produce_partial_score(): void
    {
        $this->actingAs($this->student);

        $questionIds = $this->questions->pluck('id')->toArray();
        $answers = [];
        foreach ($this->questions as $i => $q) {
            // First 3 correct, last 2 wrong
            $answers[$q->id] = $i < 3 ? $q->answer : 'WRONG';
        }

        $this->post('/portal/examination/submit', [
            'examination_id' => $this->examination->id,
            'question_ids' => $questionIds,
            'answers_data' => $answers,
        ]);

        $result = Result::where('user_id', $this->student->id)
            ->where('examination_id', $this->examination->id)
            ->first();

        $this->assertEquals(3, $result->score);
    }

    /**
     * Test that the examination attempt creates a Result record on start.
     */
    public function test_examination_attempt_creates_result(): void
    {
        $this->actingAs($this->student);

        $response = $this->get('/portal/examination/attempt?id=' . $this->examination->id);

        $response->assertStatus(200);

        $this->assertDatabaseHas('results', [
            'user_id' => $this->student->id,
            'examination_id' => $this->examination->id,
        ]);
    }
}
