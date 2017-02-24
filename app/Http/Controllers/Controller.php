<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
// use Teepluss\Restable\Contracts\Restable;
// use Illuminate\Http\Request;


// use App\restfulModel;
// use Illuminate\Foundation\Bus\DispatchesJobs;
// use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use DB;
// use Session;

use Illuminate\Support\Facades\DB;
class Controller extends BaseController
{
    public function hello(){
    	$results = DB::select("SELECT * FROM users");
    	 print_r( json_encode($results));

    }
}
