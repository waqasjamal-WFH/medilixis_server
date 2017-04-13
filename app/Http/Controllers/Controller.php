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
                    "message" => $return['message']
                ));
                return $response;
            }else{
                $response = json_encode(array(
                    "status" => "fail",
                    "message" =>$return['message'] 

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
                    "error" =>array("type"=>"sql", "message"=>"No Such User") ,

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

    //......................................edit selected tranco admin api start here...............................

    public function edit_selected_tranco_admin(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->edit_selected_tranco_admin_model($data);
            // return $return;
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) 
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to edit tranco admin") ,

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

    //......................................edit selected tranco admin api end here.................................

    //......................................add doctor api start here...............................

    public function add_doctor(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->add_doctor_model($data);
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to add doctor") ,

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

    //......................................add doctor api end here.................................


     //..............................list doctor api start here.......................................

    public function get_doctor(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->get_doctor_model($data);
            
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to Get doctor list") ,

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

    //...............................list doctor api end here........................................

    //...........................get selected doctor for edit page api start here...........................
    public function get_selected_doctor(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){
            $return=$model->get_selected_doctor_model($data);
             
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
                    "error" =>array("type"=>"sql", "message"=>"No Such doctor") ,

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

    //...........................get selected doctor for edit page api end here.............................

    //......................................edit selected doctor api start here...............................

    public function edit_selected_doctor(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->edit_selected_doctor_model($data);
            // return $return;
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) 
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to edit doctor") ,

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

    //......................................edit selected doctor api end here.................................
    
    //......................................add transcriber api start here...............................

    public function add_transcriber(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->add_transcriber_model($data);
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to add transcriber") ,

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

    //......................................add transcriber api end here.................................


    //..............................list transcriber api start here.......................................

    public function get_transcriber(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->get_transcriber_model($data);
            
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

    //...............................list  transcriber api end here........................................

    //...........................get selected user for edit page api start here...........................
    public function get_selected_transcriber(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){
            $return=$model->get_selected_transcriber_model($data);
             
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
                    "error" =>array("type"=>"sql", "message"=>"No Such User") ,

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

    //......................................edit selected transcriber api start here...............................

    public function edit_selected_transcriber(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->edit_selected_transcriber_model($data);
            // return $return;
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) 
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to edit transcriber") ,

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

    //......................................edit selected transcriber api end here.................................

    //......................................add qa api start here...............................

    public function add_qa(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->add_qa_model($data);
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to add transcriber") ,

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

    //......................................add qa api end here.................................

     //..............................list qa api start here.......................................

    public function get_qa(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->get_qa_model($data);
            
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to Get Quality Assurance") ,

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

    //...............................list  qa api end here........................................

    //...........................get selected qa for edit page api start here...........................
    public function get_selected_qa(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){
            $return=$model->get_selected_qa_model($data);
             
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
                    "error" =>array("type"=>"sql", "message"=>"No Such User") ,

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

    //...........................get selected qa for edit page api end here.............................

    //......................................edit selected qa api start here...............................

    public function edit_selected_qa(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->edit_selected_qa_model($data);
            // return $return;
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) 
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to edit transcriber") ,

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

    //......................................edit selected qa api end here.................................

    //......................................add nurse api start here...............................

    public function add_nurse(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));

        // print_r($data);
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->add_nurse_model($data);
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to add nurse") ,

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

    //......................................add nurse api end here.................................


    //..............................list nurse api start here.......................................

    public function get_nurse(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->get_nurse_model($data);
            
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to Get Quality Assurance") ,

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

    //...............................list  nurse api end here........................................

    //...........................get selected nurse for edit page api start here...........................
    public function get_selected_nurse(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){
            $return=$model->get_selected_qa_model($data);
             
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
                    "error" =>array("type"=>"sql", "message"=>"No Such User") ,

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

    //...........................get selected nurse for edit page api end here.............................

    //......................................edit selected nurse api start here...............................

    public function edit_selected_nurse(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->edit_selected_nurse_model($data);
            // return $return;
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) 
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to edit nurse") ,

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

    //......................................edit selected nurse api end here.................................


    //......................................add practice admin api start here...............................

    public function add_practice_admin(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->add_practice_admin_model($data);
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to add practice admin") ,

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

    //......................................add practice admin api end here.................................

    //..............................list practice admin api start here.......................................

    public function get_practice_admin(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->get_practice_admin_model($data);
            
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to Get practice admin") ,

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

    //...............................list practice admin api end here........................................


    //...........................get selected user for edit page api start here...........................
    public function get_selected_practice_admin(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){
            $return=$model->get_selected_practice_admin_model($data);
             
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
                    "error" =>array("type"=>"sql", "message"=>"No Such User") ,

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

     //......................................edit selected practice admin api start here...............................

    public function edit_selected_practice_admin(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){


            $return=$model->edit_selected_practice_admin_model($data);
            // return $return;
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) 
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to edit practice admin") ,

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

    //......................................edit selected practice admin api end here.................................

    //......................................add receptionist api start here...............................

    public function add_receptionist(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->add_receptionist_model($data);
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to add receptionist") ,

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

    //......................................add receptionist api end here.................................

    //..............................list receptioniest api start here.......................................

    public function get_receptioniest(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->get_receptionist_model($data);
            
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to Get receptioniest") ,

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

    //...............................list receptioniest api end here........................................

    //...........................get selected receptionist for edit page api start here...........................
    public function get_selected_receptionist(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){
            $return=$model->get_selected_receptionist_model($data);
             
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
                    "error" =>array("type"=>"sql", "message"=>"No Such User") ,

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



    //...........................get selected receptionist for edit page api end here.............................

    //......................................edit selected receptioniest api start here...............................

    public function edit_selected_receptioniest(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->edit_selected_receptionest_model($data);
            // return $return;
            if($return['result']=="true"){
                $response = json_encode(array(
                    "status" => "success",
                    "response" =>array("timestamp"=>date("Y-m-d")." ".date("h:i:sa")) 
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to edit receptioniest") ,

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

    //......................................edit selected receptioniest api end here.................................

    //......................................add patient api start here...............................

    public function add_patient(){
        $model = new restfulModel();
        $data = json_decode(file_get_contents("php://input"));

        // print_r($data);
        
        $authenticate=$this->auth_token($data->token);

        if($authenticate['result']=="true"){

            $return=$model->add_patient_model($data);
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
                    "error" =>array("type"=>"sql", "message"=>"unsuccessful to add patient") ,

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

    //......................................add patient api end here.................................


    // public function checkSession(){
    //     $sess=Session::get('username');
    //     return $sess;
    // }
}
