<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
// use Teepluss\Restable\Contracts\Restable;
// use Illuminate\Http\Request;


use App\restfulModel;
// use Illuminate\Foundation\Bus\DispatchesJobs;
// use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use DB;
// use Session;

use Illuminate\Support\Facades\DB;
class Controller extends BaseController
{
   

    public function auth_token($data){
        $id=DB::table('users')->select('id')->where('token','=',$data)->first();
        if($id!=''){
            return array('result'=>"true", 'user_id'=>$id);
        }else{
            return array('result'=>"false");
        }
    }

    // signup function for user
    public function userSignup(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $return=$model->userdata_model($data);
    //        return $return;
        if($return['result']=="true"){
            $response = json_encode(array(
                "status" => "success",
                "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) ,
                "data" => array(
                    "username" => $data->username,
                    "password" => $data->password,
                    "email" =>  $data->email
                )
            ));
            return $response;
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"unsuccessful to signup") ,

            ));

            return $response;
        };
    }


    //user login api

    public function userLogin(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));

        $return=$model->userlogin_model($data);
    //        print_r($return);
        if($return['result']=="true"){
    //            $request->session()->put('username', $return['data']->username);
            // \Session::put('username', $return['data']->username);

    //            $_SESSION['username']=$return['data']->username;
            $response = json_encode(array(
                "status" => "success",
                "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) ,
                "data" => array(    
                       $return['data'],
                    // Session::get('username')
                ),
                      "user_details" => $return['user_detail'],
            ));
            return $response;
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"unsuccessful to login") ,

            ));

            return $response;
        };


    }

    
    public function userLogout(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));

        $return=$model->userlogout_model($data);
    //        print_r($return);
        if($return['result']=="true"){
            $response = json_encode(array(
                "status" => "success",
                "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) ,
                "data" => array(

                )
            ));
            return $response;
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"unsuccessful to logout") ,

            ));

            return $response;
        };


    } // end of logout api



    //user update api
    public function userUpdate(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
    //        print_r($data->token);
        $authenticate=$this->auth_token($data->token);
    //        print_r($authenticate['result']);
        if($authenticate['result']=="true"){
            $return=$model->userUpdate_model($data);
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) ,
                    "userdata" => $return['userdata'],
                    "user_detail" => $return['user_detail']
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"unable to update profile") ,

                ));

                return $response;
            }
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid") ,

            ));

            return $response;
        }

    }


    // add patient api start

    public function addPatient(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));

        $authenticate=$this->auth_token($data->token);
    //        print_r($authenticate['result']);
        if($authenticate['result']=="true"){
            $return=$model->addPatient_model($data);
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) ,
                    "data" => array(

                    )
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"unable to add patient") ,

                ));

                return $response;
            }
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid") ,

            ));

            return $response;
        }


    }

    // add patient api end

    //forget password api

    public function changePass(){
        $model=new restfulModel();
        $data = json_decode(file_get_contents("php://input"));

        $authenticate=$this->auth_token($data->token);
    //        print_r($authenticate['result']);
        if($authenticate['result']=="true"){
            $return=$model->changePass_model($data);
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) ,
                    "data" => array(

                    )
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"unable to change password") ,

                ));

                return $response;
            }
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid") ,

            ));

            return $response;
        }
    }
    //forget password api end

    public function add_company(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));

        $authenticate=$this->auth_token($data->token);
        if($authenticate['result']=="true"){
            $return=$model->add_company_model($data);
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) ,
                    "data" => array(

                    )
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"unable to change password") ,

                ));

                return $response;
            }
            // print_r($return);
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid") ,

            ));

            return $response;
            // print_r("token rejected");
        }
        // print_r($data->token);
    }

//.......................get company api start here........
    public function get_company(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $authenticate=$this->auth_token($data->token);
        
        // $return=$model->get_company_model();
        // print_r($return);

        if($authenticate['result']=="true"){
            $return=$model->get_company_model($data);

            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) ,
                    "data" => $return['data']
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"No Company Added") ,

                ));

                return $response;
            }
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid"),
            ));

            return $response;
        }
    }
    //.......................get company api end here....................

    //.....................get selected company api start here..................................
    public function get_select_company(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){
            $return=$model->get_select_company_model($data);
             
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) ,
                    "data" => $return['data']
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"No Company Added") ,

                ));

                return $response;
            }
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid"),
            ));

            return $response;
        }
    }

    //.....................get selected company api end here..................................
        
    //...............................edit selected company api start here.............................

    public function edit_selected_company(){

        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));

        // return $data;

        // $return=$model-edit_selected_company_model($data);
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){
            $return=$model->edit_selected_company_model($data);

            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) 
                    
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"Company not edit") ,

                ));

                return $response;
            }
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid"),
            ));

            return $response;
        }

    }

    //......................................add tranco admin api start here...............................

    public function add_tranco_admin(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->add_tranco_admin_model($data);
            // return $return;
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) ,
                    // "data" => array(
                    //     "username" => $data->username,
                    //     "password" => $data->password,
                    //     "email" =>  $data->email
                    // )
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to add tranco admin") ,

                ));

                return $response;
            };
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid"),
            ));

            return $response;
        }    
    }

    //......................................add tranco admin api end here.................................

    //..............................list tranco admin api start here.......................................

    public function get_tranco_admin(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->get_tranco_admin_model($data);
            
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) ,
                    "data" => $return['data']
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to Get tranco admin") ,

                ));

                return $response;
            };
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid"),
            ));

            return $response;
        }    
    }

    //...............................list tranco admin api end here........................................


    //....................forget password api for mobile app  start here.....................................
    public function forgot_password(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        // $authenticate=$this->auth_token($data->token);

        // if($authenticate['result']=="true"){

            $return=$model->forgot_password_model($data);
            
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) ,
                    "data" => $return['message'],
                    "pass"=> $return['pass']
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    // "error" =>array("type"=>"sql", "message"=>"unsuccessful to Get tranco admin") ,
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) ,
                    "data" => $return['message']

                ));

                return $response;
            };



        // }else{
        //     $response = json_encode(array(
        //         "status" => "fail",
        //         "error" =>array("type"=>"authentication error", "message"=>"Token Invalid"),
        //     ));

        //     return $response;
        // }    
    }

    //....................forget password api for mobile app  end here.....................................

    //...........................get selected user for edit page api start here...........................
    public function get_selected_tranco_admin(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){
            $return=$model->get_selected_tranco_admin_model($data);
             
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa"), "token"=>$return['token']) ,
                    "data" => $return['data']
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "error" =>array("type"=>"sql", "message"=>"No Company Added") ,

                ));

                return $response;
            }
        }else{
            $response = json_encode(array(
                "status" => "fail",
                "error" =>array("type"=>"sql", "message"=>"Token Invalid"),
            ));

            return $response;
        }

    }

    //...........................get selected user for edit page api end here.............................





    // public function checkSession(){
    //     $sess=Session::get('username');
    //     return $sess;
    // }
}
