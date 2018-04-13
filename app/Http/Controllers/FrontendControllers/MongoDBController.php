<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2017/12/26
 * Time: 16:41
 */
namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Models\Mongodb;

class MongodbController extends Controller{

    public function Index(Request $request)
    {
        $users = Mongodb::connectionMongodb('users');
        Mongodb::connectionMongodb('users')
            ->insert([ //插入数据
            'name' => 'tom3',
            'age' => 19
            ]);
        $res =  Mongodb::connectionMongodb('users')->get();
        #添加一条数据 ture 添加成功
        dump($users->insert(['title' => 'email', 'article' => 'john@example.com','time' => time()]));
        #添加多条数据 ture 添加成功
        dump($users->insert([
            [
                'title' => 'email',
                'article' => 'john@example.com',
                'time' => time()
            ],
            [
                'title' => 'title1',
                'article' => 'lichuang@example.com',
                'time' => time()
            ],
            [
                'title' => 'title2',
                'article' => 'lili@example.com',
                'time' => time()
            ]
        ]));
        #修改一条数据 0 修改成功
        dump($users->where('name', 'tom3')->update(['age' => '93']));
        #删除一条数据 1 为删除成功
        dump($users->where('name', 'tom2')->delete());
        #删除集合所有数据 返回几删除几条
        dump($users->delete());
        #查询集合所有数据
        dump($users->get());
        #按条件查询
        dump($users->where('name', 'tom1')->get());
        #模糊查询
        dump($users->where('age', 'like', '19%')->get());
        #按条件排序
        dump($users->orderBy('age', 'desc')->get());
        dd($res);
    }
}