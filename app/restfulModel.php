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
    // print_r($data);
    $oldpasswpord = DB::table('users')->where('password',md5($data->oldpass))->get();

    // if($oldpasswpord){
      $update=DB::table('users')
                ->where('token', $data->token)
                ->where('password', md5($data->oldpass))
                ->update(['password' => md5($data->newpass)]);
      if($update){
          return array('result'=>"true", 'token'=>$data->token, 'message'=> "Password successfully updated");
      }else{
          return array('result'=>"false", 'token'=>$data->token, 'message'=> "Current Password is incorrect");
      }
     
    // }else{
    //   return array('result'=>"false", 'token'=>$data->token, 'message'=> "Current Password is incorrect");
    // };

    
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
    if($update){
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


  //.................................forget password model api start here.........................

  public function forgot_password_model($data){
    
    $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                     .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                     .'0123456789!@#$%^&*()'); // and any other characters
    shuffle($seed); // probably optional since array_is randomized; this may be redundant
    $rand = '';
    foreach (array_rand($seed, 5) as $k) $rand .= $seed[$k];
 
    // echo $rand;
    
    $user = DB::table('users')
    // ->select('users.id as userID','users.*')
    
    ->where('email','=', $data->email)
    
    ->get();
    // print_r($user);
    $md5pass=md5($rand);
    if($user){
      $updatepass=DB::table('users')->where('id', $user[0]->id)->update([
        'password' =>$md5pass
      ]);

      if($updatepass){
        $to= $data->email;
        $subject="Forget Password";
        $message="this is your new Password " .$rand;
        $headers = "From: Medilixis@example.com" ;

        // $mail= ;
        if(@mail($to,$subject,$message,$headers) ){
          // echo $mail;
          return array('result'=>"true" ,'message'=>"Mail send successfully" , 'pass' => $rand);
        }else{
          return array('result'=>"false", 'message'=>"Mail not send");
        }
      }else{
        return array('result'=>"false", 'message'=>"Password not updated");
      }
    }else{
      return array('result'=>"false",  'message'=>"Token is incorrect");
    }
  }
  //..............................forget password model api end here.............................

  //.............................get selected tranco admin model start here.....................
  public function get_selected_tranco_admin_model($data){

    // print_r($data);
    $user = DB::table('users')
    ->select('users.id as userID','users.*')
    
    ->where('id','=', $data->uid)
    
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
      $users->comapanies=$user_comapanies;
      // $arr=array();
      // foreach ($user_comapanies as $company) {
      //   $arr[] = $company->company_short_name;
      //   $users->companies=implode(">>", $arr);

      // };
    };

    foreach ($user as $users) {
      $nav_permission = DB::table('nav_permission')->where('user_id','=', $users->userID)->get();
      $arrr=array();
      $users->permission=$nav_permission;
      // if(isset($nav_permission[0])){
      //   $nav_array=get_object_vars($nav_permission[0]);
      //   // $users->permission=implode(">>>", $nav_permission);
      //   foreach ($nav_array as $key => $value) {
         
      //     if($value== "1"){
      //       $arrr[]=$key;
      //       $users->permission=implode(">>>", $arrr);
      //     };
      //   };
      // }  
    }
    if($user){
      // print_r($user);
        return array('result'=>"true", 'token'=>$data->token , 'data'=> $user);
    }else{
        return array('result'=>"false", 'token'=>$data->token);
    }
  }

  //.............................get selected tranco admin model end here.......................



  //......................edit selected tranco admin model start here.........................
  public function edit_selected_tranco_admin_model($data){
    $keyname=[];
    $keyvalue=[];
    $i=0;
    foreach($data as $key=>$va){
        $keyname[$key]=$key;
        $keyvalue[$key]=$va;
        $i++;
    };
    

    $update= DB::table('users')->where('id' , $keyvalue["userID"])->update([
        'username' => $keyvalue["first_name"], 'email' => $keyvalue["email"] ]);
    

    $get_inserted_company = DB::table('user_company')->where('user_id','=', $keyvalue["userID"])->get();
    // print_r($keyname);

    //.... deleting all the previous company of a selected user.........
    if($get_inserted_company){
      foreach ($get_inserted_company as $value) {
         DB::table('user_company')->where('user_id', '=', $keyvalue["userID"])->delete(); 
      };
    };
    // print_r($keyvalue['selected_associate_company']);
    
    //...... inserting all the new companies of a user in useer_company table ... deleting and then inserting done because of key value 
    // structure of a database. It cannot be done only by updating query.
    if($keyvalue['selected_associate_company']){
      foreach ($keyvalue['selected_associate_company'] as $associate_companies ) {
      
        DB::table('user_company')->insert([
            'user_id' => $keyvalue["userID"], 'company_id' => $associate_companies->id,'company_short_name'=>$associate_companies->short_name
        ]);
      };
    };
    


    // .........selecting the already assign rights to delete and the to insert the new rights ............

    $get_inserted_rights = DB::table('nav_permission')->where('user_id','=', $keyvalue["userID"])->get();
    
    //.... deleting all the previous rights of a selected user.........
    if($get_inserted_rights){
      foreach ($get_inserted_rights as $rights) {
         DB::table('nav_permission')->where('user_id', '=', $keyvalue["userID"])->delete(); 
      };
    };
    


    


    //...... inserting all the new rights of a user in nav_permission table ... deleting and then inserting done because of key value 
    // structure of a database. It cannot be done only by updating query.
    
    if($keyvalue['selected_access_right']){
      $col=array();
      foreach ($keyvalue['selected_access_right'] as $access_right ) {
        
       $col[$access_right->column_name]= $access_right->status;
      };
       $col['user_id']=$keyvalue["userID"];
      DB::table('nav_permission')->where('user_id' , $keyvalue["userID"])->insert($col);
    };

    unset($keyname['first_name']);
    unset($keyvalue['first_name']);
    // unset($keyname['password']);
    // unset($keyvalue['password']);
    unset($keyname['email']);
    unset($keyvalue['email']);
    unset($keyname['token']);
    unset($keyvalue['token']);
    //  unset($keyname['userID']);
    // unset($keyvalue['userID']);
    // unset($keyname['role_id']);
    // unset($keyvalue['role_id']);
    unset($keyname['selected_associate_company']);
    unset($keyvalue['selected_associate_company']);
    unset($keyname['selected_access_right']);
    unset($keyvalue['selected_access_right']);



    //.... delete all the old user details .....

    $userdetails_old=DB::table('userdetails')->where('user_id','=', $keyvalue["userID"])->get();
    if($userdetails_old){
      foreach ($userdetails_old as $rights) {
         DB::table('userdetails')->where('user_id', '=', $keyvalue["userID"])->delete(); 
      };
    };



    foreach($keyname as $kn=>$kv){
        DB::table('userdetails')->insert([
            'key_name' => $kn, 'key_value' => $keyvalue[$kv],'user_id'=>$keyvalue["userID"]
        ]);
    };

    if($keyvalue){
      return array('result'=>"true");
    }else{
      return array('result'=>"false");
    };
  }

  //......................edit selected admin model end here............................

  //......................add doctor model start here.........................
  public function add_doctor_model($data){
    $keyname=[];
    $keyvalue=[];
    $i=0;
    foreach($data as $key=>$va){
        $keyname[$key]=$key;
        $keyvalue[$key]=$va;
        $i++;
    };

    $lastid= DB::table('users')->insertGetId([
        'username' => $keyvalue["username"], 'password' => md5($keyvalue["password"]), 'email' => $keyvalue["email"],'role_id' =>$keyvalue["role_id"]       ]);
    


    // $accessright_id= DB::table('nav_permission')->insertGetId([
    //     'user_id' => $lastid]);
    
    // $col=array();
    // foreach ($data->access_rights as $access_right ) {
      
    //  $col[$access_right->column_name]= $access_right->status;
    // };

    // DB::table('nav_permission')->where('id' , $accessright_id)->update($col);
    // // print_r($col);
    // foreach ($data->associate_company as $associate_companies ) {
      
    //   DB::table('user_company')->insert([
    //       'user_id' => $lastid, 'company_id' => $associate_companies->id,'company_short_name'=>$associate_companies->short_name
    //   ]);
    // };
   


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
    // unset($keyname['access_rights']);
    // unset($keyvalue['access_rights']);
    // unset($keyname['associate_company']);
    // unset($keyvalue['associate_company']);

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

  //.......................add doctor model end here............................


  //.......................get doctor model start here............................

  public function get_doctor_model($data){
   
    $user = DB::table('users')
    ->select('users.id as userID','users.*')
    
    ->where('role_id','=', 3)
    
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
        if($value->key_name=="npi"){
          $users->npi=$value->key_value;
        };
      };

      // $users->user_details=$user_detail;
    };

    // $arr=array();
    // foreach ($user as $users) {
    //   $user_comapanies = DB::table('user_company')->where('user_id','=', $users->userID)->get();
    //   $arr=array();
    //   foreach ($user_comapanies as $company) {
    //     $arr[] = $company->company_short_name;
    //     $users->companies=implode(">>", $arr);

    //   };
    // };

    // foreach ($user as $users) {
    //   $nav_permission = DB::table('nav_permission')->where('user_id','=', $users->userID)->get();
    //   $arrr=array();
      
    //   if(isset($nav_permission[0])){
    //     $nav_array=get_object_vars($nav_permission[0]);
    //     // $users->permission=implode(">>>", $nav_permission);
    //     foreach ($nav_array as $key => $value) {
         
    //       if($value== "1"){
    //         $arrr[]=$key;
    //         $users->permission=implode(">>>", $arrr);
    //       };
    //     };
    //   }  
    // }

    if($user){
        return array('result'=>"true", 'token'=>$data->token , 'data'=> $user);
    }else{
        return array('result'=>"false", 'token'=>$data->token);
    }
  }


  //.......................get doctor model end here..............................

  //.............................get selected tranco admin model start here.....................
  public function get_selected_doctor_model($data){

    // print_r($data);
    $user = DB::table('users')
    ->select('users.id as userID','users.*')
    
    ->where('id','=', $data->uid)
    
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
        if($value->key_name=="npi"){
          $users->npi=$value->key_value;
        };
      };

      // $users->user_details=$user_detail;
    };

    // $arr=array();
    // foreach ($user as $users) {
    //   $user_comapanies = DB::table('user_company')->where('user_id','=', $users->userID)->get();
    //   $users->comapanies=$user_comapanies;
    //   // $arr=array();
    //   // foreach ($user_comapanies as $company) {
    //   //   $arr[] = $company->company_short_name;
    //   //   $users->companies=implode(">>", $arr);

    //   // };
    // };

    // foreach ($user as $users) {
    //   $nav_permission = DB::table('nav_permission')->where('user_id','=', $users->userID)->get();
    //   $arrr=array();
    //   $users->permission=$nav_permission;
    //   // if(isset($nav_permission[0])){
    //   //   $nav_array=get_object_vars($nav_permission[0]);
    //   //   // $users->permission=implode(">>>", $nav_permission);
    //   //   foreach ($nav_array as $key => $value) {
         
    //   //     if($value== "1"){
    //   //       $arrr[]=$key;
    //   //       $users->permission=implode(">>>", $arrr);
    //   //     };
    //   //   };
    //   // }  
    // }

    if($user){
      // print_r($user);
        return array('result'=>"true", 'token'=>$data->token , 'data'=> $user);
    }else{
        return array('result'=>"false", 'token'=>$data->token);
    }
  }

  //.............................get selected tranco admin model end here.......................


  //......................edit selected doctor model start here.........................
  public function edit_selected_doctor_model($data){
    $keyname=[];
    $keyvalue=[];
    $i=0;
    foreach($data as $key=>$va){
        $keyname[$key]=$key;
        $keyvalue[$key]=$va;
        $i++;
    };
    

    $update= DB::table('users')->where('id' , $keyvalue["userID"])->update([
        'username' => $keyvalue["first_name"], 'email' => $keyvalue["email"] ]);
    

    // $get_inserted_company = DB::table('user_company')->where('user_id','=', $keyvalue["userID"])->get();
    // print_r($keyname);

    //.... deleting all the previous company of a selected user.........
    
    // if($get_inserted_company){
    //   foreach ($get_inserted_company as $value) {
    //      DB::table('user_company')->where('user_id', '=', $keyvalue["userID"])->delete(); 
    //   };
    // };
    // print_r($keyvalue['selected_associate_company']);
    
    //...... inserting all the new companies of a user in useer_company table ... deleting and then inserting done because of key value 
    // structure of a database. It cannot be done only by updating query.
    
    // if($keyvalue['selected_associate_company']){
    //   foreach ($keyvalue['selected_associate_company'] as $associate_companies ) {
      
    //     DB::table('user_company')->insert([
    //         'user_id' => $keyvalue["userID"], 'company_id' => $associate_companies->id,'company_short_name'=>$associate_companies->short_name
    //     ]);
    //   };
    // };
    


    // .........selecting the already assign rights to delete and the to insert the new rights ............

    // $get_inserted_rights = DB::table('nav_permission')->where('user_id','=', $keyvalue["userID"])->get();
    
    // //.... deleting all the previous rights of a selected user.........
    // if($get_inserted_rights){
    //   foreach ($get_inserted_rights as $rights) {
    //      DB::table('nav_permission')->where('user_id', '=', $keyvalue["userID"])->delete(); 
    //   };
    // };
    


    


    //...... inserting all the new rights of a user in nav_permission table ... deleting and then inserting done because of key value 
    // structure of a database. It cannot be done only by updating query.
    
    // if($keyvalue['selected_access_right']){
    //   $col=array();
    //   foreach ($keyvalue['selected_access_right'] as $access_right ) {
        
    //    $col[$access_right->column_name]= $access_right->status;
    //   };
    //    $col['user_id']=$keyvalue["userID"];
    //   DB::table('nav_permission')->where('user_id' , $keyvalue["userID"])->insert($col);
    // };

    unset($keyname['first_name']);
    unset($keyvalue['first_name']);
    // unset($keyname['password']);
    // unset($keyvalue['password']);
    unset($keyname['email']);
    unset($keyvalue['email']);
    unset($keyname['token']);
    unset($keyvalue['token']);
    //  unset($keyname['userID']);
    // unset($keyvalue['userID']);
    // unset($keyname['role_id']);
    // unset($keyvalue['role_id']);
    // unset($keyname['selected_associate_company']);
    // unset($keyvalue['selected_associate_company']);
    // unset($keyname['selected_access_right']);
    // unset($keyvalue['selected_access_right']);



    //.... delete all the old user details .....

    $userdetails_old=DB::table('userdetails')->where('user_id','=', $keyvalue["userID"])->get();
    if($userdetails_old){
      foreach ($userdetails_old as $rights) {
         DB::table('userdetails')->where('user_id', '=', $keyvalue["userID"])->delete(); 
      };
    };


    //...........inserting again after deleting all old record ........
    foreach($keyname as $kn=>$kv){
        DB::table('userdetails')->insert([
            'key_name' => $kn, 'key_value' => $keyvalue[$kv],'user_id'=>$keyvalue["userID"]
        ]);
    };

    if($keyvalue){
      return array('result'=>"true");
    }else{
      return array('result'=>"false");
    };
  }

  //......................edit selected doctor model end here............................


  //......................add transcriber model start here.........................
  public function add_transcriber_model($data){
    $keyname=[];
    $keyvalue=[];
    $i=0;
    foreach($data as $key=>$va){
        $keyname[$key]=$key;
        $keyvalue[$key]=$va;
        $i++;
    };


    $lastid= DB::table('users')->insertGetId([
      'username' => $keyvalue["username"], 'password' => md5($keyvalue["password"]), 'email' => $keyvalue["email"],'role_id' =>$keyvalue["role_id"]       
    ]);
    


    $accessright_id= DB::table('nav_permission')->insertGetId([
      'user_id' => $lastid
    ]);
    
    $col=array();
    foreach ($data->access_rights as $access_right ) {
      
     $col[$access_right->column_name]= $access_right->status;
    };

    DB::table('nav_permission')->where('id' , $accessright_id)->update($col);
    // print_r($col);
    foreach ($data->associate_doctors as $associate_doctor ) {
      
      DB::table('user_doctor')->insert([
          'user_id' => $lastid, 'doctor_id' => $associate_doctor->id,'doctor_name'=>$associate_doctor->username
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
    unset($keyname['associate_doctors']);
    unset($keyvalue['associate_doctors']);

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

  //.......................add transcriber model end here...........................


  //.......................get transcriber model start here............................

  public function get_transcriber_model($data){
   
    $user = DB::table('users')
    ->select('users.id as userID','users.*')
    
    ->where('role_id','=', 2)
    
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
      $user_doctor = DB::table('user_doctor')->where('user_id','=', $users->userID)->get();
      $arr=array();
      if($user_doctor){
        foreach ($user_doctor as $doctor) {
          $arr[] = $doctor->doctor_name;
          $users->doctors=implode(">>", $arr);
        };
      }
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


  //.......................get transcriber model end here..............................

  //.............................get selected transcriber model start here.....................
  public function get_selected_transcriber_model($data){

    // print_r($data);
    $user = DB::table('users')
    ->select('users.id as userID','users.*')
    
    ->where('id','=', $data->uid)
    
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
      $user_doctors = DB::table('user_doctor')->where('user_id','=', $users->userID)->get();
      $users->doctor=$user_doctors;
      // $arr=array();
      // foreach ($user_comapanies as $company) {
      //   $arr[] = $company->company_short_name;
      //   $users->companies=implode(">>", $arr);

      // };
    };

    foreach ($user as $users) {
      $nav_permission = DB::table('nav_permission')->where('user_id','=', $users->userID)->get();
      $arrr=array();
      $users->permission=$nav_permission;
      // if(isset($nav_permission[0])){
      //   $nav_array=get_object_vars($nav_permission[0]);
      //   // $users->permission=implode(">>>", $nav_permission);
      //   foreach ($nav_array as $key => $value) {
         
      //     if($value== "1"){
      //       $arrr[]=$key;
      //       $users->permission=implode(">>>", $arrr);
      //     };
      //   };
      // }  
    }
    if($user){
      // print_r($user);
        return array('result'=>"true", 'token'=>$data->token , 'data'=> $user);
    }else{
        return array('result'=>"false", 'token'=>$data->token);
    }
  }

  //.............................get selected transcriber model end here.......................

  //......................edit selected transcriber model start here.........................
  public function edit_selected_transcriber_model($data){
    $keyname=[];
    $keyvalue=[];
    $i=0;
    foreach($data as $key=>$va){
        $keyname[$key]=$key;
        $keyvalue[$key]=$va;
        $i++;
    };
    

    $update= DB::table('users')->where('id' , $keyvalue["userID"])->update([
        'username' => $keyvalue["first_name"], 'email' => $keyvalue["email"] ]);
    

    $get_inserted_company = DB::table('user_doctor')->where('user_id','=', $keyvalue["userID"])->get();
    // print_r($keyname);

    //.... deleting all the previous company of a selected user.........
    if($get_inserted_company){
      foreach ($get_inserted_company as $value) {
         DB::table('user_doctor')->where('user_id', '=', $keyvalue["userID"])->delete(); 
      };
    };
    // print_r($keyvalue['selected_associate_company']);
    
    //...... inserting all the new companies of a user in useer_company table ... deleting and then inserting done because of key value 
    // structure of a database. It cannot be done only by updating query.
    if($keyvalue['selected_associate_doctors']){
      foreach ($keyvalue['selected_associate_doctors'] as $associate_companies ) {
      
        DB::table('user_doctor')->insert([
            'user_id' => $keyvalue["userID"], 'doctor_id' => $associate_companies->id,'doctor_name'=>$associate_companies->username
        ]);
      };
    };
    


    // .........selecting the already assign rights to delete and the to insert the new rights ............

    $get_inserted_rights = DB::table('nav_permission')->where('user_id','=', $keyvalue["userID"])->get();
    
    //.... deleting all the previous rights of a selected user.........
    if($get_inserted_rights){
      foreach ($get_inserted_rights as $rights) {
         DB::table('nav_permission')->where('user_id', '=', $keyvalue["userID"])->delete(); 
      };
    };
    


    


    //...... inserting all the new rights of a user in nav_permission table ... deleting and then inserting done because of key value 
    // structure of a database. It cannot be done only by updating query.
    
    if($keyvalue['selected_access_right']){
      $col=array();
      foreach ($keyvalue['selected_access_right'] as $access_right ) {
        
       $col[$access_right->column_name]= $access_right->status;
      };
       $col['user_id']=$keyvalue["userID"];
      DB::table('nav_permission')->where('user_id' , $keyvalue["userID"])->insert($col);
    };

    unset($keyname['first_name']);
    unset($keyvalue['first_name']);
    // unset($keyname['password']);
    // unset($keyvalue['password']);
    unset($keyname['email']);
    unset($keyvalue['email']);
    unset($keyname['token']);
    unset($keyvalue['token']);
    //  unset($keyname['userID']);
    // unset($keyvalue['userID']);
    // unset($keyname['role_id']);
    // unset($keyvalue['role_id']);
    unset($keyname['selected_associate_doctors']);
    unset($keyvalue['selected_associate_doctors']);
    unset($keyname['selected_access_right']);
    unset($keyvalue['selected_access_right']);



    //.... delete all the old user details .....

    $userdetails_old=DB::table('userdetails')->where('user_id','=', $keyvalue["userID"])->get();
    if($userdetails_old){
      foreach ($userdetails_old as $rights) {
         DB::table('userdetails')->where('user_id', '=', $keyvalue["userID"])->delete(); 
      };
    };



    foreach($keyname as $kn=>$kv){
        DB::table('userdetails')->insert([
            'key_name' => $kn, 'key_value' => $keyvalue[$kv],'user_id'=>$keyvalue["userID"]
        ]);
    };

    if($keyvalue){
      return array('result'=>"true");
    }else{
      return array('result'=>"false");
    };
  }

  //......................edit selected transcriber model end here............................

  //......................add qa model start here.........................
  public function add_qa_model($data){
    $keyname=[];
    $keyvalue=[];
    $i=0;
    foreach($data as $key=>$va){
        $keyname[$key]=$key;
        $keyvalue[$key]=$va;
        $i++;
    };


    $lastid= DB::table('users')->insertGetId([
      'username' => $keyvalue["username"], 'password' => md5($keyvalue["password"]), 'email' => $keyvalue["email"],'role_id' =>$keyvalue["role_id"]       
    ]);
    


    $accessright_id= DB::table('nav_permission')->insertGetId([
      'user_id' => $lastid
    ]);
    
    $col=array();
    foreach ($data->access_rights as $access_right ) {
      
     $col[$access_right->column_name]= $access_right->status;
    };

    DB::table('nav_permission')->where('id' , $accessright_id)->update($col);
    // print_r($col);
    foreach ($data->associate_doctors as $associate_doctor ) {
      
      DB::table('user_doctor')->insert([
          'user_id' => $lastid, 'doctor_id' => $associate_doctor->id,'doctor_name'=>$associate_doctor->username
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
    unset($keyname['associate_doctors']);
    unset($keyvalue['associate_doctors']);

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

  //.......................add qa model end here...........................


  //.......................get qa model start here............................

  public function get_qa_model($data){
   
    $user = DB::table('users')
    ->select('users.id as userID','users.*')
    
    ->where('role_id','=', 5)
    
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
      $user_doctor = DB::table('user_doctor')->where('user_id','=', $users->userID)->get();
      $arr=array();
      if($user_doctor){
        foreach ($user_doctor as $doctor) {
          $arr[] = $doctor->doctor_name;
          $users->doctors=implode(">>", $arr);
        };
      }
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


  //.......................get qa model end here..............................
}
