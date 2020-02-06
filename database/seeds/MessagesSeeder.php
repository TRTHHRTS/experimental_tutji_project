<?php

use Illuminate\Database\Seeder;

class MessagesSeeder extends Seeder
{
    public function run()
    {
        DB::table('messages')->insert([
            'sender_id' => 2,
            'rcpt_id' => 1,
            'message' => 'Привет!',
            'readed' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('messages')->insert([
            'sender_id' => 2,
            'rcpt_id' => 1,
            'message' => 'Как дела?',
            'readed' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('messages')->insert([
            'sender_id' => 1,
            'rcpt_id' => 2,
            'message' => 'Привет!!!!!',
            'readed' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('messages')->insert([
            'sender_id' => 3,
            'rcpt_id' => 1,
            'message' => 'Приветнк!',
            'readed' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('messages')->insert([
            'sender_id' => 1,
            'rcpt_id' => 3,
            'message' => 'Прив',
            'readed' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('messages')->insert([
            'sender_id' => 3,
            'rcpt_id' => 1,
            'message' => 'уо!',
            'readed' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('messages')->insert([
            'sender_id' => 3,
            'rcpt_id' => 1,
            'message' => 'гугл!',
            'readed' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('messages')->insert([
            'sender_id' => 2,
            'rcpt_id' => 1,
            'message' => 'гагол!',
            'readed' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

    }
}
