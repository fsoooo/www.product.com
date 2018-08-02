<?php

use Illuminate\Database\Seeder;
use App\Models\AdminUser;

class AdminUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //工厂填充(需先在 /database/factories/ModelFactory.php 中定义)
//        factory('App\Models\AdminUser',3)->create([
//            'password' => bcrypt('123456')
//        ]);
        $admin_id = DB::table('admin_users')->insertGetId(
            [
                'name' => 'admin',
                'display_name' => '系统管理员',
                'email' => 'admin'.'@admin.com',
                'password' => bcrypt('123qwe'),
            ]
        );
        $boss_id = DB::table('admin_users')->insertGetId(
            [
                'name' => 'boss',
                'display_name' => '系统所属者',
                'email' => 'boss'.'@boss.com',
                'password' => bcrypt('123qwe'),
            ]
        );
        $worker_id = DB::table('admin_users')->insertGetId(
            [
                'name' => 'manager',
                'display_name' => '业管专员',
                'email' => 'manager'.'@manager.com',
                'password' => bcrypt('123qwe'),
            ]
        );

        $admin_role_id = DB::table('roles')->insertGetId(
            [
                'name' => 'admin',
                'display_name' => '管理员权限',
                'description' => '管理员相关权限',
            ]
        );

        $boss_role_id = DB::table('roles')->insertGetId(
            [
                'name' => 'owner',
                'display_name' => '所属者权限',
                'description' => '所属者相关权限',
            ]
        );

        $worker_role_id = DB::table('roles')->insertGetId(
            [
                'name' => 'worker',
                'display_name' => '业管专员权限',
                'description' => '业管专员相关权限',
            ]
        );

        DB::table('role_user')->insert(
            [
                'user_id' => $admin_id,
                'role_id' => $admin_role_id,
            ]
        );

        DB::table('role_user')->insert(
            [
                'user_id' => $boss_id,
                'role_id' => $boss_role_id,
            ]
        );

        DB::table('role_user')->insert(
            [
                'user_id' => $worker_id,
                'role_id' => $worker_role_id,
            ]
        );
    }
}
