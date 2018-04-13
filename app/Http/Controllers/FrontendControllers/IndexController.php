<?php

namespace App\Http\Controllers\FrontendControllers;


use Mail, DB;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function index()
    {
        return redirect('/backend');
    }

}
