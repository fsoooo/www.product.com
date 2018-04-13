<?php
//前台路由
Route::group(['prefix' => '/', 'namespace'=>'FrontendControllers'],function () {
    Route::get('/', 'IndexController@index');

    Route::get('/question', function () {
        return view('frontend.question.question');
    });
    //问卷调查
    Route::group(['prefix'=>'/question'],function(){
        Route::post('postQuestion','QuestionController@question');
        Route::post('uploadImage','QuestionController@uploadImage');
    });
    Route::get('/mongo', 'MongodbController@index');


});