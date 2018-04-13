<?php

namespace App\Http\Controllers\BackendControllers;
use App\Models\Special;
use App\Models\SmsInfo;
use App\Models\EmailInfo;

class IndexController extends BaseController
{

    public function index()
    {
        return view('backend.index.index');
    }

}