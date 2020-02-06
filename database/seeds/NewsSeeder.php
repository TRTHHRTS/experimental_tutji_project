<?php

use Illuminate\Database\Seeder;
use \App\Models\NewsRecord;

class NewsSeeder extends Seeder
{
    public function run()
    {
        NewsRecord::create([
            'user_id' => 0,
            'title_ru' => 'Сайт в разработке',
            'title_en' => 'Project in development',
            'message_ru' => 'Сайт в разработке. Скоро все сделаем.',
            'message_en' => 'Project in development. But all soon will be done.',
            'importance' => 2,
        ]);
    }
}
