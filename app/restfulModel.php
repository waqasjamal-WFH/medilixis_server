<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class restfulModel extends Model
{
 //    protected $table = 'product_list';

    public function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    public function userdata_model($data){
        $keyname=[];
        $keyvalue=[];
        $i=0;
        foreach($data as $key=>$va){
            $keyname[$key]=$key;
            $keyvalue[$key]=$va;
            $i++;
        }

        //token generate function
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        for($i=0;$i<32;$i++){
            $token .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))];
        }

       $lastid= DB::table('users')->insertGetId([
            'username' => $keyvalue["username"], 'password' => md5($keyvalue["password"]), 'email' => $keyvalue["email"],'token'=>$token,'role_id' =>"1"
        ]);
        $token =DB::table('users')->where('id', '=', $lastid)->pluck('token');
        unset($keyname['username']);
        unset($keyvalue['username']);
        unset($keyname['password']);
        unset($keyvalue['password']);
        unset($keyname['email']);
        unset($keyvalue['email']);

        foreach($keyname as $kn=>$kv){
            DB::table('userdetails')->insert([
                'key_name' => $kn, 'key_value' => $keyvalue[$kv],'user_id'=>$lastid
            ]);

        }
        return array('result'=>"true", 'token'=>$token);
    }


  public function userlogin_model($data){
    $user_details=[];
    $user = DB::table('users')
    ->select('users.id as userID','users.*', 'roles.id as roleID', 'roles.*')
    ->join('roles', 'users.role_id', '=', 'roles.id')
    ->where('email','=', $data->email)
    ->where('password','=', md5($data->password))
    ->first();

    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for($i=0;$i<32;$i++){
      $token .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))];
    }
      // print_r($user) ;
    if(!empty($user) || isset($user)){
      $updatetoken=  DB::table('users')
      ->where('id', $user->userID)
      ->update(['token' => $token]);
          // fetch data again after updating token
      $newuser = DB::table('users')
      ->select('users.id as userID','users.*', 'roles.id as roleID', 'roles.*')
      ->join('roles', 'users.role_id', '=', 'roles.id')
      // ->join('userdetails', 'userdetails.user_id', '=', 'users.id')
      ->where('email','=', $data->email)
      ->where('password','=', md5($data->password))
      ->first();

      //fetch user complete data from user details table

      $user_detail= DB::table('userdetails')->where('user_id', '=', $user->userID)->get();
      // print_r($user_detail) ;
      foreach ($user_detail as $key => $value) {
        $user_details[$value->key_name]=$value->key_value;

      };
      // print_r($user_details) ; 

      return array('result'=>"true", 'token'=>$token,'data'=> $newuser, 'user_detail'=>$user_details );
    }else{
       return array('result'=>"false");
    }
  }
   
   public function userlogout_model($data){
       $token = $data->token;
       $query=DB::table('users')
            ->where('token', $token)
            ->update(['token' => '']);
//       print_r($user) ;
       if($query){

            return array('result'=>"true");

       }else{
           return array('result'=>"false");
       }
   }
   
   
  public function userUpdate_model($data){
    $keyname=[];
    $keyvalue=[];
    $i=0;
    foreach($data as $key=>$va){
        $keyname[$key]=$key;
        $keyvalue[$key]=$va;
        $i++;
    }
    //updating data in users table
    $lastid= DB::table('users')->where('token' , $data->token)->update([
        'username' => $keyvalue['username']
    ]);

    unset($keyname['username']);
    unset($keyvalue['username']);
    unset($keyname['password']);
    unset($keyvalue['password']);
    unset($keyname['email']);
    unset($keyvalue['email']);

    // fetch data again after updating username
    $newuser = DB::table('users')
    ->where('token','=', $data->token)
    ->first();

    // updating all the remaining data to user details table 
    foreach($keyname as $kn=>$kv){
        DB::table('userdetails')->where('user_id' , $newuser->id)->where('key_name' , $kn)->update([
             'key_value' => $keyvalue[$kv]
        ]);

    }

   

    //fetch user complete data from user details table

    $user_detail= DB::table('userdetails')->where('user_id', '=', $newuser->id)->get();
    // print_r($user_detail) ;
    foreach ($user_detail as $key => $value) {
      $user_details[$value->key_name]=$value->key_value;

    };

    if(isset($user_detail) || isset($newuser) ){
      return array('result'=>"true", 'token'=>$data->token,'userdata'=> $newuser, 'user_detail'=>$user_details );
    }else{
      return array('result'=>"false");
    };
  }
    
    
     // add patient model start
  public function addPatient_model($data){
      $insert= DB::table('patient')->insert([
          'patient_name' => $data->patientname, 'date_of_birth' => $data->dateofbirth, 'date_of_service' => $data->dateofservice,'visit_type'=>$data->visittype
      ]);

      if($insert){
          return array('result'=>"true", 'token'=>$data->token);
      }else{
          return array('result'=>"false", 'token'=>$data->token);
      }

  }

    // add patient model end

    // change pass model api

  public function changePass_model($data){
      $update=DB::table('users')
                  ->where('token', $data->token)
                  ->where('password', md5($data->oldpass))
                  ->update(['password' => md5($data->newpass)]);
      if($update){
          return array('result'=>"true", 'token'=>$data->token);
      }else{
          return array('result'=>"false", 'token'=>$data->token);
      }
  }

  public function add_company_model($data){

    $insert= DB::table('company')->insert([
          'short_name' => $data->data->short_name, 
          'full_name' => $data->data->full_name, 
          'address' => $data->data->address,
          'city' => $data->data->city,
          'state' => $data->data->state,
          'zip_code' => $data->data->zip_code,
          'country' => $data->data->country,
          'phone' => $data->data->phone,
          'fax' => $data->data->fax,
          'e_mail' => $data->data->e_mail,
          'web_address' => $data->data->website,
          'time_zone' => $data->data->timezone,
          'admin_person_name' => $data->data->admin_person_name,
    ]);

    if($insert){
        return array('result'=>"true", 'token'=>$data->token);
    }else{
        return array('result'=>"false", 'token'=>$data->token);
    }
  }

  // ......................get all company list start here ................

  public function get_company_model($data){
    $list = DB::table('company')->get();
    if($list){
        return array('result'=>"true", 'token'=>$data->token , 'data'=> $list);
    }else{
        return array('result'=>"false", 'token'=>$data->token);
    }

    
  }

  // ......................get all company list end here ................

  //........................get selected company model start here....................
  public function get_select_company_model($data){
    $company = DB::table('company')->where('id',$data->companyid)->get();
    if($company){
        return array('result'=>"true", 'token'=>$data->token , 'data'=> $company);
    }else{
        return array('result'=>"false", 'token'=>$data->token);
    }
  }

  //........................get selected company model end here....................

  //.....................edit selected company model start here....................
  public function edit_selected_company_model($data){
     $update=DB::table('company')->where('id', $data->companyid)->update([
          'short_name' =>$data->data->short_name,
          'full_name' =>$data->data->full_name,
          'address' =>$data->data->address,
          'city' =>$data->data->city,
          'state' =>$data->data->state,
          'zip_code' =>$data->data->zip_code,
          'country' =>$data->data->country,
          'phone' =>$data->data->phone,
          'fax' =>$data->data->fax,
          'e_mail' =>$data->data->e_mail,
          'web_address' =>$data->data->website,
          'time_zone' =>$data->data->timezone,
          'admin_person_name' =>$data->data->admin_person_name

        ]);
    if($data){
        return array('result'=>"true", 'token'=>$data->token);
    }else{
        return array('result'=>"false", 'token'=>$data->token);
    }
    
  }

  //......................add tranco admin model start here.........................
  public function add_tranco_admin_model($data){
    $keyname=[];
    $keyvalue=[];
    $i=0;
    foreach($data as $key=>$va){
        $keyname[$key]=$key;
        $keyvalue[$key]=$va;
        $i++;
    };

    // //token generate function
    // $token = "";
    // $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    // $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    // $codeAlphabet.= "0123456789";
    // for($i=0;$i<32;$i++){
    //     $token .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))];
    // }

    $lastid= DB::table('users')->insertGetId([
        'username' => $keyvalue["username"], 'password' => md5($keyvalue["password"]), 'email' => $keyvalue["email"],'role_id' =>$keyvalue["role_id"]       ]);
    


    $accessright_id= DB::table('nav_permission')->insertGetId([
        'user_id' => $lastid]);
    
    $col=array();
    foreach ($data->access_rights as $access_right ) {
      
     $col[$access_right->column_name]= $access_right->status;
    };

    DB::table('nav_permission')->where('id' , $accessright_id)->update($col);
    // print_r($col);
    foreach ($data->associate_company as $associate_companies ) {
      
      DB::table('user_company')->insert([
          'user_id' => $lastid, 'company_id' => $associate_companies->id,'company_short_name'=>$associate_companies->short_name
      ]);
    };
   


    $token =DB::table('users')->where('id', '=', $lastid)->pluck('token');
    unset($keyname['username']);
    unset($keyvalue['username']);
    unset($keyname['password']);
    unset($keyvalue['password']);
    unset($keyname['email']);
    unset($keyvalue['email']);
    unset($keyname['token']);
    unset($keyvalue['token']);
    unset($keyname['role_id']);
    unset($keyvalue['role_id']);
    unset($keyname['access_rights']);
    unset($keyvalue['access_rights']);
    unset($keyname['associate_company']);
    unset($keyvalue['associate_company']);

    foreach($keyname as $kn=>$kv){
        DB::table('userdetails')->insert([
            'key_name' => $kn, 'key_value' => $keyvalue[$kv],'user_id'=>$lastid
        ]);
    };

    if($lastid){
      return array('result'=>"true");
    }else{
      return array('result'=>"false");
    };
  }

  //.......................add tranco admin model end here............................



  //.......................get tranco admin model start here............................

  public function get_tranco_admin_model($data){
   
    $user = DB::table('users')
    ->select('users.id as userID','users.*')
    
    ->where('role_id','=', 9)
    
    ->get();
      foreach ($user as $users) {
      $user_detail = DB::table('userdetails')->where('user_id','=', $users->userID)->get();
      foreach ($user_detail as $value) {
        if($value->key_name=="last_name"){
          $users->last_name=$value->key_value;
        };
        if($value->key_name=="address"){
          $users->address=$value->key_value;
        };
        if($value->key_name=="phone_number"){
          $users->phone_number=$value->key_value;
        };
        if($value->key_name=="state"){
          $users->state=$value->key_value;
        };
        if($value->key_name=="country"){
          $users->country=$value->key_value;
        };
        if($value->key_name=="city"){
          $users->city=$value->key_value;
        };
      };

      // $users->user_details=$user_detail;
    };

$arr=array();
    foreach ($user as $users) {
      $user_comapanies = DB::table('user_company')->where('user_id','=', $users->userID)->get();
      $arr=array();
      foreach ($user_comapanies as $company) {
        $arr[] = $company->company_short_name;
        $users->companies=implode(">>", $arr);

      };
    };

    foreach ($user as $users) {
      $nav_permission = DB::table('nav_permission')->where('user_id','=', $users->userID)->get();
      $arrr=array();
      
      if(isset($nav_permission[0])){
        $nav_array=get_object_vars($nav_permission[0]);
        // $users->permission=implode(">>>", $nav_permission);
        foreach ($nav_array as $key => $value) {
         
          if($value== "1"){
            $arrr[]=$key;
            $users->permission=implode(">>>", $arrr);
          };
        };
      }  
    }
    if($user){
        return array('result'=>"true", 'token'=>$data->token , 'data'=> $user);
    }else{
        return array('result'=>"false", 'token'=>$data->token);
    }
  }


  //.......................get tranco admin model end here..............................

  //.....................edit selected company model end here....................

  //.................................forget password model api start here.........................

  public function forgot_password_model($data){
    $user = DB::table('users')
    ->select('users.id as userID','users.*')
    
    ->where('token','=', $data->token)
    
    ->get();

    print_r($user) ;

    // $to= $data->email;
    // $subject="Forget Password";
    // $message="this is a mail for forget password api";
    // $headers = "From: webmaster@example.com" ;


    // mail($to,$subject,$message,$headers);
  }
  //..............................forget password model api end here.............................
}
