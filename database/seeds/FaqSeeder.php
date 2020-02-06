<?php

use Illuminate\Database\Seeder;
use \App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run()
    {
        $titles = [
            [
                'q_ru' => 'Когда релиз?',
                'q_en' => 'When the release?',
                'a_ru' => 'Скоро',
                'a_en' => 'Soon'
            ],
            [
                'q_ru' => 'А релиз точно скоро?',
                'q_en' => 'And release for sure soon?',
                'a_ru' => 'Да, точно скоро!',
                'a_en' => 'Yes, for sure!'
            ]
        ];
        for ($i = 0; $i < count($titles); $i++) {
            Faq::create([
                'question_ru' => $titles[$i]['q_ru'],
                'question_en' => $titles[$i]['q_en'],
                'answer_ru' => $titles[$i]['a_ru'],
                'answer_en' => $titles[$i]['a_en']
            ]);
        }
    }
}