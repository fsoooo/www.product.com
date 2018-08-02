<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
}