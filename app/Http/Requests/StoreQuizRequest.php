<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreQuizRequest extends FormRequest
{
    private ?array $resolvedQuestions = null;

    private array $questionErrors = [];

    public function rules(): array
    {
        return [
            'title'                            => 'required|string|max:255',
            'description'                      => 'nullable|string|max:1000',
            'time_limit'                       => 'nullable|integer|min:1|max:180',
            'questions'                        => 'required|array|min:1',
            'questions.*.type'                 => 'required|in:multiple_choice,identification',
            'questions.*.question'             => 'required|string|max:2000',
            'questions.*.correct_answer'       => 'nullable|string|max:255',
            'questions.*.correct_choice_index' => 'nullable|integer|min:0',
            'questions.*.choices'              => 'nullable|array|max:10',
            'questions.*.choices.*'            => 'nullable|string|max:255',
        ];
    }

    /**
     * Every question needs an answer a student can actually give: a multiple-choice answer must be
     * one of at least two non-blank choices, and an identification question needs an answer.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $this->resolveQuestions();

                foreach ($this->questionErrors as $field => $message) {
                    $validator->errors()->add($field, $message);
                }
            },
        ];
    }

    /**
     * The validated questions with each correct answer resolved to text and blank choices removed.
     */
    public function questions(): array
    {
        return $this->resolveQuestions();
    }

    private function resolveQuestions(): array
    {
        if ($this->resolvedQuestions !== null) {
            return $this->resolvedQuestions;
        }

        $questions = [];

        foreach ($this->input('questions', []) as $index => $questionData) {
            $correctAnswer = trim($questionData['correct_answer'] ?? '');
            $choices = null;

            if ($questionData['type'] === 'multiple_choice') {
                $rawChoices = $questionData['choices'] ?? [];

                // Resolve from the selected index before blank choices are dropped and indexes shift
                if (isset($questionData['correct_choice_index'])) {
                    $correctAnswer = trim($rawChoices[$questionData['correct_choice_index']] ?? '');
                }

                $choices = array_values(array_filter(
                    array_map(fn ($choice) => trim((string) $choice), $rawChoices),
                    fn ($choice) => $choice !== ''
                ));

                if (count($choices) < 2) {
                    $this->questionErrors["questions.$index.choices"] = 'Question '.($index + 1).' needs at least two choices.';
                } elseif ($correctAnswer === '' || ! in_array($correctAnswer, $choices, true)) {
                    $this->questionErrors["questions.$index.correct_choice_index"] = 'Question '.($index + 1).' needs a correct answer selected from its choices.';
                }
            } elseif ($correctAnswer === '') {
                $this->questionErrors["questions.$index.correct_answer"] = 'Question '.($index + 1).' needs a correct answer.';
            }

            $questions[] = [
                'type'           => $questionData['type'],
                'question'       => $questionData['question'],
                'correct_answer' => $correctAnswer,
                'choices'        => $choices,
            ];
        }

        return $this->resolvedQuestions = $questions;
    }
}
