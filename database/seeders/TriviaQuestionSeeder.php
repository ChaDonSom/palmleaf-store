<?php

namespace Database\Seeders;

use App\Models\TriviaQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TriviaQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question' => 'How many books are in the Bible?',
                'correct_answer' => '66',
                'wrong_answers' => ['39', '27', '73'],
            ],
            [
                'question' => 'Who built the ark?',
                'correct_answer' => 'Noah',
                'wrong_answers' => ['Moses', 'Abraham', 'David'],
            ],
            [
                'question' => 'What is the first book of the Bible?',
                'correct_answer' => 'Genesis',
                'wrong_answers' => ['Exodus', 'Matthew', 'Psalms'],
            ],
            [
                'question' => 'Who was swallowed by a great fish?',
                'correct_answer' => 'Jonah',
                'wrong_answers' => ['Daniel', 'Peter', 'Paul'],
            ],
            [
                'question' => 'How many disciples did Jesus have?',
                'correct_answer' => '12',
                'wrong_answers' => ['10', '7', '24'],
            ],
            [
                'question' => 'Who baptized Jesus?',
                'correct_answer' => 'John the Baptist',
                'wrong_answers' => ['Peter', 'James', 'Andrew'],
            ],
            [
                'question' => 'What is the last book of the Bible?',
                'correct_answer' => 'Revelation',
                'wrong_answers' => ['Jude', 'Malachi', 'Acts'],
            ],
            [
                'question' => 'Who defeated Goliath?',
                'correct_answer' => 'David',
                'wrong_answers' => ['Saul', 'Jonathan', 'Samuel'],
            ],
            [
                'question' => 'How many days did God take to create the world?',
                'correct_answer' => '6',
                'wrong_answers' => ['7', '5', '10'],
            ],
            [
                'question' => 'Who parted the Red Sea?',
                'correct_answer' => 'Moses',
                'wrong_answers' => ['Aaron', 'Joshua', 'Elijah'],
            ],
        ];

        foreach ($questions as $question) {
            TriviaQuestion::create($question);
        }
    }
}
