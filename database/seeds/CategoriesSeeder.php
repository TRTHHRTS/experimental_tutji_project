<?php

use Illuminate\Database\Seeder;
use \App\Models\Category;
use \App\Models\Aging;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        Category::create(['id' => 1,  'name_ru' => 'наука',            'name_en' => 'science']);
        Category::create(['id' => 2,  'name_ru' => 'искусство',        'name_en' => 'art']);
        Category::create(['id' => 3,  'name_ru' => 'спорт',            'name_en' => 'sport']);
        Category::create(['id' => 4,  'name_ru' => 'развлечение',      'name_en' => 'enjoyment']);
        Category::create(['id' => 5,  'name_ru' => 'мастер-класс',     'name_en' => 'master-class']);
        Category::create(['id' => 6,  'name_ru' => 'разное',           'name_en' => 'other']);
        Category::create(['id' => 7,  'name_ru' => 'семейное занятие', 'name_en' => 'family lessons']);
        Category::create(['id' => 8,  'name_ru' => 'большая группа',   'name_en' => 'group lessons']);
        Category::create(['id' => 9,  'name_ru' => 'онлайн-занятие',   'name_en' => 'online']);
        Category::create(['id' => 10, 'name_ru' => 'вебинар',          'name_en' => 'webinar']);

        Aging::create(['id' => 1, 'name_ru' => '0+', 'name_en' => '0+', 'desc_ru' => 'для детей, не достигших возраста шести лет', 'desc_en' => 'for children under the age of six']);
        Aging::create(['id' => 2, 'name_ru' => '6+', 'name_en' => '6+', 'desc_ru' => 'для детей, достигших возраста шести лет', 'desc_en' => 'recommended for persons 6 and older']);
        Aging::create(['id' => 3, 'name_ru' => '12+', 'name_en' => '12+', 'desc_ru' => 'для детей, достигших возраста двенадцати лет', 'desc_en' => 'suitable for persons 12 and older']);
        Aging::create(['id' => 4, 'name_ru' => '16+', 'name_en' => '16+', 'desc_ru' => 'для детей, достигших возраста шестнадцати лет', 'desc_en' => 'suitable for persons 16 and older']);
        Aging::create(['id' => 5, 'name_ru' => '18+', 'name_en' => '18+', 'desc_ru' => 'для взрослых', 'desc_en' => 'only for persons 18 and older']);
    }
}
