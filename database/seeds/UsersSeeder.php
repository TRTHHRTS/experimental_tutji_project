<?php

use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\UserDetails;
use \App\Models\UserRole;
use \App\Models\UserSettings;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $admin = new User();
        $admin->id = 1;
        $admin->name = 'Администрация';
        $admin->email = 'admin@tutji.corp';
        $admin->email_verified = true;
        $admin->phone = "9105193655";
        $admin->is_phone_confirmed = true;
        $admin->password = bcrypt('G4Qj1ikq9h8Z7MbKIHeX');
        $admin->save();
        $admin->userDetails()->save(new UserDetails([
            'gender' => 1,
            'birthday' => date("Y-m-d H:i:s", strtotime("1991-11-29 00:00:00"))
        ]));
        $admin->rights()->save(new UserRole([
            'moder_rights' => true,
            'admin_rights' => true
        ]));
        $admin->settings()->save(new UserSettings());

        $test = new User();
        $test->id = 6;
        $test->name = 'Модератор';
        $test->email = 'operator@tutji.corp';
        $test->email_verified = true;
        $test->password = bcrypt('`7(Z(42w$:\LJ');
        $test->save();
        $test->userDetails()->save(new UserDetails([
            'gender' => 1
        ]));
        $test->rights()->save(new UserRole([
            'moder_rights' => true,
            'admin_rights' => false
        ]));
        $test->settings()->save(new UserSettings());
    }
}
