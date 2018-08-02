<?php

namespace App\Http\Controllers\backendControllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Product;
use App\Models\StatusRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\Status;
use Excel;

class BaseController extends Controller
{
    protected $request;
    protected $id;


    public function __construct(Request $request)
    {
        $this->request = $request;
//        $this->id=$_COOKIE["id"];
    }
}