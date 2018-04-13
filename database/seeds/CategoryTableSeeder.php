<?php

use Illuminate\Database\Seeder;
use App\Models\AdminUser;

class CategoryTableSeeder extends Seeder
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

        //一级分类
        $company_id = DB::table('category')->insertGetId(
            [
                'name' => '公司分类',
                'slug' => 'company',
            ]
        );
        $insurance_id = DB::table('category')->insertGetId(
            [
                'name' => '产品分类',
                'slug' => 'insurance',
            ]
        );
        $clause_id = DB::table('category')->insertGetId(
            [
                'name' => '条款分类',
                'slug' => 'clause',
            ]
        );
        $duty_id = DB::table('category')->insertGetId(
            [
                'name' => '责任分类',
                'slug' => 'duty',
            ]
        );

        //二级分类（公司）
        $company_property_id = DB::table('category')->insertGetId(
            [
                'name' => '财产险公司',
                'slug' => 'company_property',
                'pid' => $company_id,
                'sort' => 1,
                'path' => ',0,' . $company_id . ','
            ]
        );

            DB::table('category')->insert(
                [
                    [
                        'name' => '普通财产险公司',
                        'slug' => 'company_property_common',
                        'pid' => $company_property_id,
                        'sort' => 2,
                        'path' => ',0,' . $company_id. ',' .$company_property_id . ','
                    ],
                    [
                        'name' => '互联网财产险公司',
                        'slug' => 'company_property_internet',
                        'pid' => $company_property_id,
                        'sort' => 2,
                        'path' => ',0,' . $company_id. ',' .$company_property_id . ','
                    ],
                ]
            );
        $company_person_id = DB::table('category')->insertGetId(
            [
                'name' => '人身险公司',
                'slug' => 'company_person',
                'pid' => $company_id,
                'sort' => 1,
                'path' => ',0,' . $company_id . ','
            ]
        );

            DB::table('category')->insert(
                [
                    [
                        'name' => '健康险公司',
                        'slug' => 'company_person_health',
                        'pid' => $company_person_id,
                        'sort' => 2,
                        'path' => ',0,' . $company_id. ',' .$company_person_id . ','
                    ],
                    [
                        'name' => '养老险公司',
                        'slug' => 'company_person_old',
                        'pid' => $company_person_id,
                        'sort' => 2,
                        'path' => ',0,' . $company_id. ',' .$company_person_id . ','
                    ],
                    [
                        'name' => '寿险公司',
                        'slug' => 'company_person_lifetime',
                        'pid' => $company_person_id,
                        'sort' => 2,
                        'path' => ',0,' . $company_id. ',' .$company_person_id . ','
                    ],
                ]
            );

        DB::table('category')->insertGetId(
            [
                'name' => '相互保险公司',
                'slug' => 'company_person',
                'pid' => $company_id,
                'sort' => 1,
                'path' => ',0,' . $company_id . ','
            ]
        );

        DB::table('category')->insert(
            [
                [
                    'name' => '健康险',
                    'slug' => 'insurance_health',
                    'pid' => $insurance_id,
                    'sort' => 1,
                    'path' => ',0,' . $insurance_id . ','
                ],
                [
                    'name' => '意外险',
                    'slug' => 'insurance_accident',
                    'pid' => $insurance_id,
                    'sort' => 1,
                    'path' => ',0,' . $insurance_id . ','
                ],
                [
                    'name' => '旅游险',
                    'slug' => 'insurance_travel',
                    'pid' => $insurance_id,
                    'sort' => 1,
                    'path' => ',0,' . $insurance_id . ','
                ],
                [
                    'name' => '人寿险',
                    'slug' => 'insurance_lifetime',
                    'pid' => $insurance_id,
                    'sort' => 1,
                    'path' => ',0,' . $insurance_id . ','
                ],
                [
                    'name' => '家财险',
                    'slug' => 'insurance_family_property',
                    'pid' => $insurance_id,
                    'sort' => 1,
                    'path' => ',0,' . $insurance_id . ','
                ]
            ]
        );

        DB::table('category')->insert(
            [
                [
                    'name' => '健康险条款',
                    'slug' => 'clause_health',
                    'pid' => $clause_id,
                    'sort' => 1,
                    'path' => ',0,' . $clause_id . ','
                ],
                [
                    'name' => '意外险条款',
                    'slug' => 'clause_accident',
                    'pid' => $clause_id,
                    'sort' => 1,
                    'path' => ',0,' . $clause_id . ','
                ],
                [
                    'name' => '旅游险条款',
                    'slug' => 'clause_travel',
                    'pid' => $clause_id,
                    'sort' => 1,
                    'path' => ',0,' . $clause_id . ','
                ],
                [
                    'name' => '人寿险条款',
                    'slug' => 'clause_lifetime',
                    'pid' => $clause_id,
                    'sort' => 1,
                    'path' => ',0,' . $clause_id . ','
                ],
                [
                    'name' => '家财险条款',
                    'slug' => 'clause_family_property',
                    'pid' => $clause_id,
                    'sort' => 1,
                    'path' => ',0,' . $clause_id . ','
                ]
            ]

        );


        DB::table('category')->insert(
            [
                [
                    'name' => '健康险责任',
                    'slug' => 'duty_health',
                    'pid' => $duty_id,
                    'sort' => 1,
                    'path' => ',0,' . $duty_id . ','
                ],
                [
                    'name' => '意外险责任',
                    'slug' => 'duty_accident',
                    'pid' => $duty_id,
                    'sort' => 1,
                    'path' => ',0,' . $duty_id . ','
                ],
                [
                    'name' => '旅游险责任',
                    'slug' => 'duty_travel',
                    'pid' => $duty_id,
                    'sort' => 1,
                    'path' => ',0,' . $duty_id . ','
                ],
                [
                    'name' => '人寿险责任',
                    'slug' => 'duty_lifetime',
                    'pid' => $duty_id,
                    'sort' => 1,
                    'path' => ',0,' . $duty_id . ','
                ],
                [
                    'name' => '家财险责任',
                    'slug' => 'duty_family_property',
                    'pid' => $duty_id,
                    'sort' => 1,
                    'path' => ',0,' . $duty_id . ','
                ]
            ]
        );



    }
}
