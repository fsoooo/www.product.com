<?php

use Illuminate\Database\Seeder;
use App\Models\AdminUser;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insertGetId(
            [
                'name' => 'www.company1.com',
                'email' => 'test@admin.com',
                'password' => bcrypt('123qwe'),
                'account_id' => 123456789,
                'sign_key' => 'testSignKey',
                'call_back_url' => 'http://dev312.inschos.com'
            ]
        );

    }
}
