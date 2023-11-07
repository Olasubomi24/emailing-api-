<?php
use Restserver\Libraries\REST_Controller;

require_once APPPATH . 'controllers/v1/Utility.php'; 
require_once("application/libraries/Format.php");
require(APPPATH.'/libraries/REST_Controller.php');

//

class Api extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
	    header('Access-Control-Allow-Headers: Content-Type, x-api-key');
        header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Allow-Origin: *');
	   	if ( "OPTIONS" === $_SERVER['REQUEST_METHOD'] ) {
		  	die();
			}
    }


    public function user_creation_post() {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $user_name = $this->input->post('user_name');
        $user_type_id = $this->input->post('user_type_id');
        
        // Check if each input is set and not null before trimming

        if ($email !== null) {
            $email = trim($email);
        }
        if ($password !== null) {
            $password = md5(trim($password));
        }
        if ($user_name !== null) {
            $user_name = ucfirst(trim($user_name));
        }
        if ($user_type_id !== null) {
            $user_type_id = trim($user_type_id);
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->response(array('status_code' => '1', 'message' => 'Provide a valid email address'));
        }
        
        if (empty($password)) {
            $this->response(array('status_code' => '1', 'message' => 'Password cannot be empty'));
        }
        
        if (empty($user_type_id)) {
            $this->response(array('status_code' => '1', 'message' => 'User type id  cannot be empty'));
        }

        if (empty($user_type_id) ) {
            $this->response(array('status_code' => '1', 'message' => 'User type id  cannot be empty'));
        }

    
        if (empty($user_name)) {
            $this->response(array('status_code' => '1', 'message' => 'User name cannot be empty'));
        }
        $utility = new Utility();
        $check_user = $utility->is_user_exist($email);
         if( $check_user['status_code'] != '0'){
            $this->response(array('status_code'=>$check_user['status_code'] ,  'message'=>$check_user['message']));
        }
       try {

         return  $this->response($utility->create_user($email,$password,$user_type_id,$user_name));

       } catch (Exception $e) {
       $this->response(array('status_code' => '1' ,'message' =>'Registration error '.$e->getMessage()));
   }   
    }


    
    public function user_lists_get(){
        $utility = new Utility();
        return $this->response( $utility->user_list());
    }
    



    public function user_login_post(){
        $email = trim($this->input->post('email'));
        $password = md5($this->input->post('password'));
        if($email == ''){
            $this->response(array('status_code'=>'1',  'message'=>'Provide correct email address'));
        }
     

        $utility = new Utility();
        try { 
            $response = $utility->user_login($email,$password); 
           
            $this->response($response);
            
         } catch (Exception $e) {
              $this->response(array('status_code' => '1' ,'message' =>' Login Error '.$e->getMessage()));
       
          }
    }

 public function user_type_get(){
    $utility = new Utility();
    return $this->response( $utility->user_type_details());
 }

 public function update_users_post() {
     // Retrieve department and description from the request

     $email = $this->input->post('email');
     $password = $this->input->post('password');
     $user_name = $this->input->post('user_name');
     $user_type_id = $this->input->post('user_type_id');
 
     // Trim and capitalize department and description if they exist
     if ($email !== null) {
        $email = trim($email);
    }
    if ($password !== null) {
        $password = md5(trim($password));
    }
    if ($user_name !== null) {
        $user_name = ucfirst(trim($user_name));
    }
    if ($user_type_id !== null) {
        $user_type_id = trim($user_type_id);
    }
 
     // Check if department and description are empty
     if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->response(array('status_code' => '1', 'message' => 'Provide a valid email address'));
    }
    
    if (empty($password)) {
        $this->response(array('status_code' => '1', 'message' => 'Password cannot be empty'));
    }
    
    if (empty($user_type_id)) {
        $this->response(array('status_code' => '1', 'message' => 'User type id  cannot be empty'));
    }

    if (empty($user_type_id) ) {
        $this->response(array('status_code' => '1', 'message' => 'User type id  cannot be empty'));
    }


    if (empty($user_name)) {
        $this->response(array('status_code' => '1', 'message' => 'User name cannot be empty'));
    }
 
     // Instantiate your Utility class
     $utility = new Utility();
 
     // Check if the department name already exists

     $check_department_name = $utility->is_users_exist($email);
 
     if ($check_department_name['status_code'] != '1') {
         return $this->response(array('status_code' => $check_department_name['status_code'], 'message' => $check_department_name['message']));
     }
 
     try {
         // Update the department using the Utility class
         $result = $utility->update_users($email, $password, $user_type_id, $user_name);
 
         // Return a response
         return $this->response($result);
     } catch (Exception $e) {
         return $this->response(array('status_code' => '1', 'message' => 'Department Update error' . $e->getMessage()));
     }
 }
     
// public function generate_token_post(){
//     $email  = $this->input->post('email'); 
//     $user_type_id  =  trim($this->input->post('user_type_id'));  // SECURITY-ANSWER , PIN, UNLOCK, BVN-UPDATE  -->
//     //$operation_type  =  'FORGET-PASSWORD'; 
//     if ($email !== null) {
//         $email = trim($email);
//     }
//     $email_pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
      
//     if ($email === '' || !preg_match($email_pattern, $email)) {
//       $this->response(array('status_code' => '1', 'message' => 'Provide a valid email address'));
//     }

  
//     if ($user_type_id == '' )  {
//         $this->response(array('status_code' => '1' , 'message' =>'Provide Request Parameters ( user_type_id )'));
//     }

//     $utility = new Utility();
//  //     $check_email = $utility->is_emailanduser_type_exist($email,$user_type_id);
//  //     if( $check_email['status_code'] != '1'){
//  //        $this->response(array('status_code'=>$check_email['status_code'] ,  'message'=>$check_email['message']));
//  //    }
//     try { 
//         $response = $utility->generate_token($email,$user_type_id);
       
//         $this->response($response);
        
//      } catch (Exception $e) {
//           $this->response(array('status_code' => '1' ,'message' =>'Generate Token Error '.$e->getMessage()));
   
//       }

// }
     
// public function forget_password_post(){
//     $email  = $this->input->post('email'); 
//     $password =  $this->input->post('password'); 
//     $token =  trim($this->input->post('token')); 
//     $user_type_id =  trim($this->input->post('user_type_id')); 

//     if ($email !== null) {
//         $email = trim($email);
//     }
    
//     if ($user_type_id !== null) {
//         $user_type_id = trim($user_type_id);
//     }
//     if ($password !== null) {
//         $password = trim($password);
//     }
//     if ($token !== null) {
//         $phonenumber = trim($token);
//     }
  

//     $utility = new Utility();

//     if ($email == '')  {
//         $this->response(array('status_code' => '1' , 'message' =>'Provide Request Parameters ( Email )'));
//     }
//     if ($user_type_id == '')  {
//         $this->response(array('status_code' => '1','message' =>'Provide the User type '));
//     }
      
//     if ($token == '')  {
//         $this->response(array('status_code' => '1','message' =>'Provide the security_token'));
//     }
  
//     if ($password == '')  {
//         $this->response(array('status_code' => '1' , 'message' =>'Provide Request Parameters ( password )'));
//     }
//     $val =  $utility->confirm_token($email, $token, $user_type_id);
    
//     if (  $val['status_code']  !== '0')  {
//         $this->response(array('status_code' => '1' , 'message' =>$val['message']));
//     }

//      $response = $utility->forget_password($email, $password, $user_type_id ); 
          
//      if ($response){
              
               
//          $this->response(array('status_code' => '0', 'message' => 'Account Updated Successfully'));
//      }else{
              
//        $this->response(array('status_code' => '1', 'message' => 'Account Update Failed'));
    
//     }          
// }
   
    

    
    
    
}


?>