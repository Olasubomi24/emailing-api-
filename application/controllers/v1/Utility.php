<?php

class Utility extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api_model');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, x-api-key,client-id');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Origin: *');
        if ("OPTIONS" === $_SERVER['REQUEST_METHOD']) {
            die();
        }
    }

    // CALL API
    public function call_api($method, $url, $header, $data = false)
    {
        
        $curl = curl_init();
        // return $response = array('status' => FALSE,'response' => $urlll ,'message' => $data );
        // return $data;
        switch ($method) {
            case "POST":
                //   return $response = array('status' => FALSE,'response' => $url ,'message' => $data );
                curl_setopt($curl, CURLOPT_POST, true);
                if ($data) {
                   
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case "Q_POST":
                curl_setopt($curl, CURLOPT_POST, true);
                if ($data) {
                    $data = http_build_query($data);
                   
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                     $url = sprintf("%s?%s", $url, http_build_query($data));

                if ($data) {
                    $url = $url . "/$data";
                }

        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $response = "cURL Error #:" . $err;
        } else {
            $response = $result;
        }

        return $response;
    }


    //To check if exist
    function is_user_exist($email){
        $response = array("status_code" => "0" , "message" => "Users not found");
        $query = $this->db->query("select email from user_accounts where email = '$email'")->result_array();
        if ( sizeof($query ) > 0){
            $response = array("status_code" => "1" , "message" => "User details already exist");  
        }
        return $response;
    }
    function is_email_exist($email){
        $response = array("status_code" => "0" , "message" => "Email not found");
        $query = $this->db->query("select email from user_accounts where email = '$email'")->result_array();
        if ( sizeof($query ) > 0){
            $response = array("status_code" => "1" , "message" => "Email already exist");  
        }
        return $response;
    }


    public function document_details_list(){
         $document =$this->db->query("SELECT COUNT(document_owner)doc_count FROM document_logs;")->result();
         $department =$this->db->query("SELECT COUNT(id)dep_count FROM departments;")->result();
         $document_owner =$this->db->query("SELECT COUNT( DISTINCT document_owner)doc_owner_count FROM document_logs")->result();
        if(count($document)>0){
        $response = array('status_code' => '0',  'message' =>'Successfull','document' => $document, 'department' => $department,'document_owner' => $document_owner);
        }
        else{
        $response = array('status_code' => '1', 'message'=>'Campaign does not Exist');
        }
        return $response;
    }
    public function department(){
        $sqlQuery =$this->db->query('SELECT department_id, department from departments')->result();
        $response = array('status_code' => '0',  'result' => $sqlQuery);
        return $response;
    }

    public function document_counts(){
        $sqlQuery =$this->db->query('SELECT a.document_owner , COUNT(a.document_owner)txn_count, d.department , a.email
        FROM document_logs a LEFT JOIN units b ON a.unit_id = b.unit_id RIGHT JOIN document_types c ON a.document_id = c.document_id INNER JOIN departments d
        ON a.department_id = d.department_id  GROUP BY  a.document_owner,d.department, a.email')->result();
        $response = array('status_code' => '0',  'result' => $sqlQuery);
        return $response;
    }


    //For Random Key 
    function get_operation_id($type){
        $value =  date('YmdHis');
        if($type == "NU"){
          $value = $value.$this->random_number(10);
        }elseif($type == "AN"){
          $value = $value.$this->random_alphanumeric(10);
        }elseIf($type == "AL"){
          $value = $value.$this->random_alphabet(10);
        }
        return $value;
      }

      function random_alphanumeric($maxlength = 17) {
        $chart = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "m", "n", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z",
                         "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N" , "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        $return_str = "";
        for ( $x=0; $x<=$maxlength; $x++ ) {
            $return_str .= $chart[rand(0, count($chart)-1)];
        }
        return $return_str;
    }
    function random_alphabet($maxlength = 17) {
        $chart = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z",
                       "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N" , "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        $return_str = "";
        for ( $x=0; $x<=$maxlength; $x++ ) {
            $return_str .= $chart[rand(0, count($chart)-1)];
        }
        return $return_str;
      }
      function random_number($maxlength = 17) {
        $chart = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $return_str = "";
        for ( $x=0; $x<=$maxlength; $x++ ) {
            $return_str .= $chart[rand(0, count($chart)-1)];
        }
        return $return_str;
      }
      
      public function create_user($email,$password,$user_type_id,$user_name){
        $dt = date('Y-m-d H:i:s');
        $ref_id = $this->get_operation_id('NU');
        $response = array();
        $query1 = "INSERT into user_accounts( ref_id,email,password,user_name,user_type_id,inserted_dt,status)
                    VALUES ('$ref_id','$email','$password','$user_name', '$user_type_id','$dt','0')";

        $this->db->query($query1);
        $this->db->trans_commit();

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $response =   array('status_code' => '1','message' => "User Account Creation Unsuccessful");
        } else {
            $this->db->trans_commit();
             $response =  array('status_code' => '0' ,'message' => 'User Account Creation Successful');
        }
        return $response;
      }
      public function  create_document($document_owner,$document_id,$email,$phonenumber,$department_id,$unit_id,$image,$purpose,$create_by){
        $dt = date('Y-m-d H:i:s');
        $ref_id = $this->get_operation_id('NU');
        $response = array();
        $query1 = "INSERT into document_logs(ref_id ,document_owner,document_id,department_id,unit_id,email,phonenumber,image,create_by,purpose,uploaded_dt,status)
                    VALUES ('$ref_id ','$document_owner','$document_id','$department_id','$unit_id','$email','$phonenumber','$image','$create_by','$purpose','$dt','0')";

        $this->db->query($query1);
        $this->db->trans_commit();

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $response =   array('status_code' => '1','message' => "User Document Creation Unsuccessful");
        } else {
            $this->db->trans_commit();
             $response =  array('status_code' => '0' ,'message' => 'User Document Creation Successful');

        }
        return $response;
      }

    public function get_document($document_id,$document_type,$description){
       // $insert_dt = date('Y-m-d H:i:s');
       $document_id = sprintf('%04d', rand(1, 9999));
        $response = array();
        $query1 = "INSERT into document_types(document_id,document_type,description)
                    VALUES ('$document_id','$document_type','$description')";

        $this->db->query($query1);
        $this->db->trans_commit();

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $response =   array('status_code' => '1','message' => "Document  Creation Unsuccessful");
        } else {
            $this->db->trans_commit();
             $response =  array('status_code' => '0' ,'message' => 'Document Creation Successful');
        }
        return $response;
    }

    public function get_department($department_id,$department,$description){
         $response = array();
         $query1 = "INSERT into departments(department_id,department,description)
                     VALUES ('$department_id','$department','$description')";
 
         $this->db->query($query1);
         $this->db->trans_commit();
 
         if ($this->db->trans_status() === FALSE){
             $this->db->trans_rollback();
             $response =   array('status_code' => '1','message' => "Department  Creation Unsuccessful");
         } else {
             $this->db->trans_commit();
              $response =  array('status_code' => '0' ,'message' => 'Department Creation Successful');
         }
         return $response;
    }

    
    public function get_unit( $department_id,$unit_id,$unit,$description){
        // $insert_dt = date('Y-m-d H:i:s');
         $response = array();
         $query1 = "INSERT into units(department_id,unit_id,unit,description)
                     VALUES ('$department_id','$unit_id','$unit','$description')";
 
         $this->db->query($query1);
         $this->db->trans_commit();
 
         if ($this->db->trans_status() === FALSE){
             $this->db->trans_rollback();
             $response =   array('status_code' => '1','message' => "Unit  Creation Unsuccessful");
         } else {
             $this->db->trans_commit();
              $response =  array('status_code' => '0' ,'message' => 'Unit Creation Successful');
         }
         return $response;
    }

    public function user_login($email, $password)
    {
        $query1 = $this ->db ->query("SELECT email , password from user_accounts WHERE email='$email' AND password='$password'")->result();
        $query2 = $this ->db ->query("SELECT ref_id,email ,user_name, a.user_type_id, inserted_dt,
        STATUS FROM user_accounts a
        LEFT JOIN user_types b
        ON a.user_type_id = b.user_type_id
        WHERE email='$email'AND STATUS = '0'")->result();
    
        if(count($query1) > 0){
            $response =   array('status_code' => '0','message' => "Login Successful", 'user_details' => $query2);
        }
        else{
            $response =   array('status_code' => '1','message' => "Incorrect User details");
        }
        return $response;
    }

    public function user_list(){
        $query = "SELECT a.id,ref_id,email ,user_name,b.user_type, inserted_dt,
        STATUS FROM user_accounts a LEFT JOIN user_types b
        ON a.user_type_id = b.user_type_id WHERE STATUS = '0'";
        $result = $this->db->query($query)->result();
        $response = array('status_code' => 0, 'message'=>'Successful', 'result' => $result);
        return $response;
    }


    public function is_users_exist($email){
        $response = array("status_code" => "0" , "message" => "user email does not exist");
        $query = $this->db->query("select email from user_accounts where email = '$email'")->result_array();
        if ( sizeof($query ) > 0){
            $response = array("status_code" => "1" , "message" => "User email already exist, Kindly choose another name");  
        }
        return $response;
    }

    public function update_users($email, $password, $user_type_id, $user_name){
        // $dt = date('Y-m-d H:i:s');
    
        $query = "UPDATE user_accounts SET email= '$email', password ='$password',user_type_id= '$user_type_id', user_name ='$user_name'
        WHERE email= '$email'";

          $this->db->query($query);
          $this->db->trans_commit();

          if ($this->db->trans_status() === FALSE){
              $this->db->trans_rollback();
              $response =   array('status_code' => '1','message' => 'Department Name cannot be updated');
          } else {
              $this->db->trans_commit();
              $response =  array('status_code' => '0' ,'message' => 'Department Name updated succesful');
          }
          return $response;
    }


    public function user_type_details(){
        $query = "SELECT user_type_id,user_type, description FROM user_types";
        $result = $this->db->query($query)->result();
        $response = array('status_code' => 0, 'message'=>'Successful', 'result' => $result);
        return $response;
    }
    function is_user_image_exist($profile_img){
        $response = array('status_code'=>'0', 'message'=>'Campaign Image not found');

        $sqlQuery = $this->db->query("select user_image from user_accounts where user_image = '$profile_img'") ->result_array();
        if (sizeof($sqlQuery)>0) {
                        $response = array('status_code' => '1', 'message' => 'Image File Name Exist, Kindly rename your file or choose another ');
        }
        return $response;
    }

    public function generate_token($email,$user_type_id){
         $token = $this->random_alphanumeric(6);   
         $user_name = explode('@', $email)[0];
         $encode_token = strtoupper(hash('SHA512' , $token));
         $dt = date('Y-m-d H:i:s');
         $this->db->query("insert into token_managers(email,token,operation_type,token1,inserted_dt) values('$email','$encode_token','$user_type_id','$token','$dt')");   
         return array('status_code' => '0', 'message'=>'Successful', 'message' => "Token Generated Successfully");
         $message_content = 'You have requested to reset your password for your MosquePay account. To reset your password, please click the following link:
            <a href="https://docmanage.mosquepay.org/forgotPassword/' . $encode_token|$user_type_id|$email . '
            
            If you did not request this password reset, you can safely ignore this message. Your password will not be changed unless you click the link above.
            
            Thank you for using MosquePay!';
         $subject = 'Rest Password';
        $this->sendgrid($email,$user_name,$subject,$message_content);
    
     }

    public function get_current_datetime(){
        return date('Y-m-d H:i:s');
    }

    
   public function confirm_token($email , $token, $user_type_id ){

    $response = array();
  // $token = strtoupper(hash('SHA512' , $token));
   $response = array('status_code' =>  '1' , 'message' => 'Token Invalid.');

   $result = $this->db->query("select inserted_dt con from token_managers where email = '$email' and  token = '$token' and operation_type = '$user_type_id' order by id desc limit 1")->result_array();

   if (sizeof($result) > 0){
   $close_at = new DateTime($result[0]['con']);
   $cdate = new DateTime($this->get_current_datetime());
   $interval = $close_at->diff($cdate);
   // return $interval->format('%y years %m months %a days %h hours %i minutes %s seconds');
     if(($interval->format('%y') > 0) || ($interval->format('%m') > 0)|| ($interval->format('%a') > 0) || ($interval->format('%h') > 0)){
       $response = array('status_code' =>  '1' , 'message' => 'Token Expired');
     }else{
        if ($interval->format('%i') <= 5){
        $response = array('status_code' =>  '0' , 'message' => 'Successful');
        }
      }        
   }else{    
     $response = array('status_code' =>  '1' , 'message' => 'Token Invalid');    
   }    
  return $response;
  }

    public function  forget_password($email, $password, $user_type_id ){
        $password = md5($password);
        $response =TRUE;
          $this->db->query("update user_accounts set password='$password'  where email = '$email'AND user_type_id ='$user_type_id' ");
        $db_error = $this->db->error();
        if( $db_error['message'] != ""){
         
          $response =FALSE;
          
        }
        return $response;
    
       }

       public function sendgrid($user_email,$user_name,$subject,$message_content){
        $message = "Hi ".$user_name.",

              ".$message_content."
             ";
     $this->load->library('email');
     $this->email->initialize(array(
     'protocol' => 'smtp',
     'smtp_host' => 'smtp.sendgrid.net',
     'smtp_user' => 'apikey',
     'smtp_pass' => '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',
     'smtp_port' => 587,
     'crlf' => "\r\n",
     'newline' => "\n"
     ));
   
     $this->email->from('support@mosquepay.org', 'Mosquepay');
     $this->email->to($user_email);
     // $this->email->cc('another@another-example.com');
     // $this->email->bcc('them@their-example.com');
     $this->email->subject($subject);
     $this->email->message($message);
     $this->email->send();

     }

public function sendgrids($user_email,$user_name,$subject,$message_content){
        // print_r($subject); 
        // print_r($message_content);
        // print_r($user_email);
        // die;
        $message = "Hi ".$user_name.",

              ".$message_content."
             
             ";
     $this->load->library('email');
     $this->email->initialize(array(
     'protocol' => 'smtp',
     'smtp_host' => 'smtp.sendgrid.net',
     'smtp_user' => 'apikey',
     'smtp_pass' => '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',
     'smtp_port' => 587,
     'crlf' => "\r\n",
     'newline' => "\n"
     ));
   
     $this->email->from('support@mosquepay.org', 'Mosquepay');
     $this->email->to($user_email);
 
     // $this->email->cc('another@another-example.com');
     // $this->email->bcc('them@their-example.com');
     $this->email->subject($subject);
     $this->email->message($message);
     $this->email->send();
     }

     public function sendgridss($user_email, $user_name, $subject, $message_content) {
        $message = "Hi " . $user_name . ",
        
    " . $message_content . "
        
    ";
        $this->load->library('email');
        $this->email->initialize(array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.sendgrid.net',
            'smtp_user' => 'apikey',
            'smtp_pass' => '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',
            'smtp_port' => 587,
            'crlf' => "\r\n",
            'newline' => "\n"
        ));
    
        $this->email->from('support@mosquepay.org', 'Mosquepay');
        $this->email->to($user_email);
        print_r($this->email->to($user_email)); die;
        $this->email->subject($subject);
        $this->email->message($message);
    
        // Enable SMTP debugging for debugging purposes
        $this->email->smtp_debug = 2;
    
        try {
            $this->email->send();
            echo'bbbbb';
        } catch (Exception $e) {
        echo 'ddddd';
        }
    }



}