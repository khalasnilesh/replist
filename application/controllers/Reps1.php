<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

class Reps extends CI_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('ApiModel');
        $this->load->library('email');
    }
    
    // SignUp with email address, password
    public function Login() 
    {
        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['email']) && (!empty($datas['password'])) ) 
        {
            $type = 'rep';
            $user = $this->ApiModel->login($datas['email'],md5($datas['password']),$type);

            if(!empty($user)) {
                $data = array('id' => $user[0]['id'], 'email' => $user[0]['email'], 'type' => 'rep', 'profile_pic' => $user[0]['profile_pic'], 'first_name' => $user[0]['first_name']);
                
                date_default_timezone_set('Asia/Kolkata');
                $date = date('j M, Y');
                
                $where = 'id';
                $table = 'user_details';
                $id = $user[0]['id'];   
                $device_token = $datas['device_token'];
            //  $datas = array('last_login' => $date );
                $datas = array('last_login' => $date , 'device_token' => $device_token);
                $saved = $this->ApiModel->update_common($table,$datas,$where,$id); 

                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta, 'data' => $data];
                echo json_encode( $response );
                exit();
            
            }else {
                $meta = array('code' => '400', 'message' => 'User Not Exist');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }
        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit();
        }
    }

    public function EmailExist() {
        
        $datas=json_decode(file_get_contents('php://input'),1); 
        if(!empty($datas['email'])) {
            
            $exist = $this->ApiModel->list_common_where('user_details','email',$datas['email']);
            
            if(empty($exist)) {
                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }else {
                $meta = array('code' => '400', 'message' => 'User Aready Exist');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }
        }
    }
    

    // SignUp with email address, password
    public function SignUp() 
    {
        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['email']) && (!empty($datas['password']))) 
        {
            $exist = $this->ApiModel->list_common_where('user_details','email',$datas['email']);
            
            if(empty($exist))
            {
                date_default_timezone_set('Asia/Kolkata');
                $date = date('j M, Y');
    
                $data = array('email' => $datas['email'], 
                              'password' => md5($datas['password']), 
                              'first_name' => $datas['first_name']." ".$datas['last_name'], 
                              'last_name' => $datas['last_name'],
                              'sales_position' => $datas['sales_position'], 
                              'area_cover' => $datas['area_cover'], 
                              'latitute' => $datas['latitute'], 
                              'longitute' => $datas['longitute'], 
                              'contact_number' => $datas['contact_number'], 
                              'paypal_id' => $datas['paypal_id'],
                              'company' => $datas['company'], 
                              'address' => $datas['address'], 
                              'city' => $datas['city'], 
                              'pincode' => $datas['pincode'], 
                              'company_contact' => $datas['company_contact'],
                              'type' => 'rep',
                              'last_login' => $date,
                              'joining_date' => $date);
                $table = 'user_details';
    
                $id = $this->ApiModel->insert_common($table,$data);
    
                $to = $datas['email'];
                $subject = "Congratulations";
                $txt = "Congratulations, For Signup Successfully.";
                $headers = "From: noreply@replistinc.com";            
                mail($to,$subject,$txt,$headers);
                
                   
                if(!empty($id)) 
                {
                    $data = array('id' => $id, 'email' => $datas['email'], 'type' => 'rep');
    
                    $meta = array('code' => '200', 'message' => 'Success');
                    $response = ['response' => $meta, 'data' => $data];
                    echo json_encode( $response );
                    exit();
                
                }else {
                    $meta = array('code' => '400', 'message' => 'Failure');
                    $response = ['response' => $meta];
                    echo json_encode( $response );
                    exit();
                }
            }else {
                $meta = array('code' => '400', 'message' => 'User Already Exist');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }   
        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit();
        }
    }
    
    public function mail() {
        
        $config['protocol']    = 'smtp';
        $config['smtp_host']    = 'ssl://smtp.gmail.com';
        $config['smtp_port']    = '465';
        $config['smtp_timeout'] = '7';
        $config['smtp_user']    = 'worlddigitalstore@gmail.com';
        $config['smtp_pass']    = 'Softwrgrg479shgrg';
        $config['charset']    = 'utf-8';
        $config['newline']    = "\r\n";
        $config['mailtype'] = 'text'; // or html
        $config['validation'] = TRUE; // bool whether to validate email or not      
 
        $this->email->initialize($config);

        $this->email->from('worlddigitalstore@gmail.com', 'Vikrant Sharma');
        $this->email->to('worlddigitalstore@gmail.com'); 

        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');  

        $this->email->send();
        
        echo $this->email->print_debugger();
    }


    // signup 2nd page 
    public function UpdatePhotos() {
        
        $user_id = !empty($this->input->post('user_id'))?trim($this->input->post('user_id')):'';
        $profilePic  = !empty($_FILES['profile_pic'])?$_FILES['profile_pic']:'';
        $companyLogo  = !empty($_FILES['company_logo'])?$_FILES['company_logo']:'';
        $customerApplication  = !empty($_FILES['customer_application'])?$_FILES['customer_application']:'';
        
        //$datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($user_id)) {
            
            $table = 'user_details';
            $where = 'id';
            $id = $user_id;
            $images = $this->ApiModel->list_common_where($table,$where,$id);

            if(!empty($profilePic)) {
                
                $upload_path = "uploads/profile_images/";
                $config['upload_path']          = './'.$upload_path;
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['file_name'] = time();
                $this->load->library('upload', $config);
                $profile_image = '';
                if(!$this->upload->do_upload('profile_pic')){
                    $error = array('error' => $this->upload->display_errors());
                }else{
                    $upload_data = $this->upload->data(); 
                    $profile_pic = $this->url.$upload_path.$upload_data['file_name'];
                }
            }else {
                $profile_pic = $images[0]['profile_pic'];
            }
            
            if(!empty($companyLogo)) {
                $upload_path = "uploads/company_logo/";
                $config['upload_path']          = './'.$upload_path;
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['file_name'] = time();
                $this->load->library('upload', $config);
                $profile_image = '';
                if(!$this->upload->do_upload('company_logo')){
                    $error = array('error' => $this->upload->display_errors());
                }else{
                    $upload_data = $this->upload->data(); 
                    $company_logo = $this->url.$upload_path.$upload_data['file_name'];
                }
            }else {
                $company_logo = $images[0]['company_logo'];
            }
            
            if(!empty($customerApplication)) {
                $upload_path = "uploads/customer_application/";
                $config['upload_path']          = './'.$upload_path;
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['file_name'] = time();
                $this->load->library('upload', $config);
                $profile_image = '';
                if(!$this->upload->do_upload('customer_application')){
                    $error = array('error' => $this->upload->display_errors());
                }else{
                    $upload_data = $this->upload->data(); 
                    $customer_application = $this->url.$upload_path.$upload_data['file_name'];
                }
            }else {
                $customer_application = $images[0]['customer_application'];
            }

            $data = array('profile_pic' => $profile_pic, 'company_logo' => $company_logo, 'customer_application' => $customer_application);
            $id = $user_id;
            $where = 'id';
            $table = 'user_details';
            $saved = $this->ApiModel->update_common($table,$data,$where,$id);   

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit();
            
        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }


    public function BuyersList() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {
            $buyers = $this->ApiModel->list_connected($datas['user_id']);

            if(!empty($buyers)) {
                foreach ($buyers as $value) {
    
                    if($value['user_id'] != $datas['user_id']) {
    
                        $list = $this->ApiModel->list_buyer($value['user_id']);
    
                        if($list[0]['type'] == 'buyer') {
                            $buyerslist[] = array('id' => $list[0]['id'], 
                                          'email' => $list[0]['email'],
                                          'first_name' => $list[0]['first_name'],
                                          'last_name' => $list[0]['last_name'],
                                          'contact_number' => $list[0]['contact_number'],
                                          'company' => $list[0]['company'],
                                          'profile_pic' => $list[0]['profile_pic']); 
                        }                   
                    }
    
    
                    if($value['friend_id'] != $datas['user_id']) {
    
                        $list = $this->ApiModel->list_buyer($value['friend_id']);
        
                        if(@$list[0]['type'] == 'buyer') {
                            $buyerslist[] = array('id' => $list[0]['id'], 
                                          'email' => $list[0]['email'],
                                          'first_name' => $list[0]['first_name'],
                                          'last_name' => @$list[0]['last_name'],
                                          'contact_number' => $list[0]['contact_number'],
                                          'company' => $list[0]['company'],
                                          'profile_pic' => $list[0]['profile_pic']); 
                        }                 
                    }               
                }
            }else {
                $buyerslist = array();
            }    

            if(empty($buyerslist)) {
                $buyerslist = array();
            }

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $buyerslist];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }


    public function RepsList() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {
            $reps = $this->ApiModel->list_connected($datas['user_id']);
            
            if(!empty($reps)) {

                foreach ($reps as $value) {
    
                    if($value['user_id'] != $datas['user_id']) {
    
                        $list = $this->ApiModel->list_buyer($value['user_id']);
    
                        if($list[0]['type'] == 'rep') {
                            $repslist[] = array('id' => $list[0]['id'], 
                                          'email' => $list[0]['email'],
                                          'first_name' => $list[0]['first_name'],
                                          'last_name' => $list[0]['last_name'],
                                          'contact_number' => $list[0]['contact_number'],
                                          'company' => $list[0]['company'],
                                          'profile_pic' => $list[0]['profile_pic']); 
                        }                   
                    }
    
                    if($value['friend_id'] != $datas['user_id']) {
    
                        $list = $this->ApiModel->list_buyer($value['friend_id']);
    
                        if($list[0]['type'] == 'rep') {
                            $repslist[] = array('id' => $list[0]['id'], 
                                          'email' => $list[0]['email'],
                                          'first_name' => $list[0]['first_name'],
                                          'last_name' => $list[0]['last_name'],
                                          'contact_number' => $list[0]['contact_number'],
                                          'company' => $list[0]['company'],
                                          'profile_pic' => $list[0]['profile_pic']); 
    
                        }                 
                    }               
                }
            }else {
                $repslist = array();
            }    

            if(empty($repslist)) {
                $repslist = array();
            }

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $repslist];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }
    


    public function SendInvitation() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id']) && !empty($datas['friend_id'])) {

            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M, Y');
        if($datas['message']=='QR')
            {        $message="You made a new friend";
            
            $mode='FriendRequestAcceptedRep';
            //check if already frient 
            $checkalreadyfriend = $this->ApiModel->list_not_friends($datas['user_id'],$datas['friend_id']);
                if(count($checkalreadyfriend) ==0){
                
                $data = array('user_id' => $datas['user_id'],
                              'friend_id' => $datas['friend_id'],
                              'message' => $datas['message'],
                              'date' => $date,
                              'flag' => '1');
                $table = 'invitation';
                $saved = $this->ApiModel->insert_common($table,$data);
                }else{
                    $saved ='';
                }
            }
            else
            {        $message="You have a new friend request";
            $mode='FriendRequest';
            $data = array('user_id' => $datas['user_id'],
                          'friend_id' => $datas['friend_id'],
                          'message' => $datas['message'],
                          'date' => $date);
            $table = 'invitation';
            $saved = $this->ApiModel->insert_common($table,$data);
            }

            

            if(!empty($saved)) {
                
                $list = $this->ApiModel->list_buyer($datas['friend_id']);
                $deviceToken=$list[0]['device_token'];
                
                $this->SendNotification($deviceToken,$message,$mode,$list[0]['type']);
                
                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit(); 

            }else {
                $meta = array('code' => '400', 'message' => 'Failure');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }            

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }



    public function FetchInvitation() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $table = 'invitation';
            $where = 'friend_id';
            $id = $datas['user_id'];
            $dat = $this->ApiModel->list_common_where($table,$where,$id);
            
            if(!empty($dat))
                foreach($dat as $value) {
                    $id = $value['user_id'];
                    $message=$value['message'];
                    $where = 'id';
                    $table = 'user_details';
                    $rep = $this->ApiModel->list_common_where($table,$where,$id);    
                    
                    $data[] = array('id' => $value['id'], 'first_name' => $rep[0]['first_name'], 'type' => $rep[0]['type'], 'email' => $rep[0]['email'], 'user_id' => $rep[0]['id'], 
                                    'profile_pic' => $rep[0]['profile_pic'], 'contact_number' => $rep[0]['contact_number'], 'company' => $rep[0]['company'], 'message'=>$message);
                }
            else {
                $data = array();
            } 

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' =>$data];
            echo json_encode( $response );
            exit();           

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 



    public function AcceptInvitation() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['invitation_id'])) {

            $data = array('flag' => '1');
            $table = 'invitation';
            $id = $datas['invitation_id'];
            $where = 'id';
            $saved = $this->ApiModel->update_common($table,$data,$where,$id);
                
                $getId=$this->ApiModel->list_by_id($table,$where,$id);
                $list = $this->ApiModel->list_buyer( $getId[0]['friend_id']);
                $deviceToken=$list[0]['device_token'];
                $message="You made a new friend";
                
                if($list[0]['type']=="rep"){
                $mode='FriendRequestAcceptedRep';
                }
                else if($list[0]['type']=="buyer"){
                      $mode='FriendRequestAcceptedbuyer';
                }else{
                       $mode='home';
                }
                
                
                $this->SendNotification($deviceToken,$message,$mode,$list[0]['type']);
                
            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        
        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }   


    public function DeleteInvitation() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['invitation_id'])) {

            $table = 'invitation';
            $id = $datas['invitation_id'];
            $where = 'id';
            $saved = $this->ApiModel->delete_common($table,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }   



    public function AddNotes() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M, Y');

            $data = array('user_id' => $datas['user_id'],
                          'heading' => $datas['heading'],
                          'note' => $datas['note'],
                          'created_date' => $date);
            $table = 'notes';

            $saved = $this->ApiModel->insert_common($table,$data);

            if(!empty($saved)) {
                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit(); 

            }else {
                $meta = array('code' => '400', 'message' => 'Failure');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }            

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }


    public function DeleteNotes() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['note_id'])) {

            $table = 'notes';
            $id = $datas['note_id'];
            $where = 'id';

            $saved = $this->ApiModel->delete_common($table,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 



    public function UpdateNotes() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['note_id'])) {

            $data = array('heading' => $datas['heading'], 'note' => $datas['note']);
            $table = 'notes';
            $id = $datas['note_id'];
            $where = 'id';

            $saved = $this->ApiModel->update_common($table,$data,$where,$id);
            
            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }   




    public function AddDocuments() {
        
        $user_id  = !empty($this->input->post('user_id'))?trim($this->input->post('user_id')):'1';
        $heading  = !empty($this->input->post('heading'))?trim($this->input->post('heading')):'1';
        $image  = !empty($_FILES['image'])?$_FILES['image']:'';
        
        if(!empty($user_id)) {

            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M, Y');
            
            $upload_path = "uploads/doc_files/";
                $config['upload_path']          = './'.$upload_path;
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['file_name'] = time();
                $this->load->library('upload', $config);
                $profile_image = '';
                if(!$this->upload->do_upload('image')){
                    $error = array('error' => $this->upload->display_errors());
                }else{
                    $upload_data = $this->upload->data(); 
                    $doc_files = $this->url.$upload_path.$upload_data['file_name'];
                }
                
            $data = array('user_id' => $user_id,
                          'heading' => $heading,
                          'files' => $doc_files,
                          'created_date' => $date);
            $table = 'documents';

            $saved = $this->ApiModel->insert_common($table,$data);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }



    public function FetchDocuments() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $table = 'documents';
            $where = 'user_id';
            $id = $datas['user_id'];
            $data = $this->ApiModel->list_common_where2($table,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' =>$data];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '200', 'message' => 'Empty Data');
                $response = ['response' => $meta,'data' =>[]];
                echo json_encode( $response );
            exit(); 
        }
    } 


    public function FetchDocumentInfo() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['document_id'])) {

            $table = 'documents';
            $where = 'id';
            $id = $datas['document_id'];
            $info = $this->ApiModel->list_common_where2($table,$where,$id);

            $data = array('id' => $info[0]['id'], 'heading' => $info[0]['heading'], 'image' => $info[0]['files'], 'created_date' => $info[0]['created_date']);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' =>$data];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 

    public function DeleteDocuments() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['document_id'])) {

            $table = 'documents';
            $id = $datas['document_id'];
            $where = 'id';

            $saved = $this->ApiModel->delete_common($table,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 
    
    
    public function Unfriend() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $search = $this->ApiModel->list_not_friends($datas['user_id'],$datas['friend_id']);
            
            $this->ApiModel->delete_common2('invitation','id',$search[0]['id']);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 

    public function UpdateDocuments() {
        
        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['document_id'])) {

            $data = array('heading' => $datas['heading']);
            $table = 'documents';
            $id = $datas['document_id'];
            $where = 'id';

            $saved = $this->ApiModel->update_common($table,$data,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }   

    public function ProfileInfo() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $id = $datas['user_id'];
            $where = 'id';
            $table = 'user_details';
            $info = $this->ApiModel->list_common_where($table,$where,$id);

            $data = array('id' => $info[0]['id'], 
                        'email' => $info[0]['email'],
                        'first_name' => $info[0]['first_name'],
                        'last_name' => $info[0]['last_name'],
                        'contact_number' => $info[0]['contact_number'],
                        'company' => $info[0]['company'],
                        'profile_pic' => $info[0]['profile_pic'],
                        'address' => $info[0]['address'],
                        'pincode' => $info[0]['pincode'],
                        'city' => $info[0]['city'],
                        'company_logo' => $info[0]['company_logo'],
                        'sales_position' => $info[0]['sales_position'],
                        'area_cover' => $info[0]['area_cover'],
                        'company_contact' => $info[0]['company_contact'],
                        'paypal_id' => $info[0]['paypal_id'],
                        'cod_discount' => $info[0]['cod_discount'],
                        'fb' => $info[0]['fb'],
                        'insta' => $info[0]['insta'],
                        'website' => $info[0]['website'],
                        'linkedin' => $info[0]['linkedin'],
                        'QRurl' => "https://www.replistinc.com/catalogue/index.php?id=".$info[0]['id'] ,
                        'last_modified' => $info[0]['last_modified'],
                        'deactivate_date' => $info[0]['deactivate_date']); 

            if(!empty($data)) {
                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta, 'data' => $data];
                echo json_encode( $response );
                exit(); 

            }else {
                $meta = array('code' => '400', 'message' => 'Failure');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }            

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }



    public function DeleteProfile() {

        $datas=json_decode(file_get_contents('php://input'),1);

        if(!empty($datas['user_id'])) {

            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M, Y');

            $data = array('flag' => '1', 'deactivate_date' => $date);
            $table = 'user_details';
            $id = $datas['user_id'];
            $where = 'id';

            $saved = $this->ApiModel->update_common($table,$data,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 

    public function UpdateProfile() {

        $datas=json_decode(file_get_contents('php://input'),1);

        if(!empty($datas['user_id'])) {

            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M, Y');

            $data = array('first_name' => $datas['first_name'], 
                          'last_name' => $datas['last_name'], 
                          'sales_position' => $datas['sales_position'], 
                          'area_cover' => $datas['area_cover'], 
                          'latitute' => $datas['latitute'], 
                          'longitute' => $datas['longitute'], 
                          'company' => $datas['company'], 
                          'address' => $datas['address'], 
                          'company_contact' => $datas['company_contact'],
                          'city' => $datas['city'], 
                          'pincode' => $datas['pincode'], 
                          'contact_number' => $datas['contact_number'],
                          'paypal_id' => $datas['paypal_id'], 
                          'device_token' => $datas['device_token'],
                           'cod_discount' => $datas['cod_discount'],
                          'insta' => $datas['insta'], 
                          'linkedin' => $datas['linkedin'], 
                          'website' => $datas['website'], 
                          'fb' => $datas['fb'], 
                          'last_modified' => $date);
            $table = 'user_details';
            $id = $datas['user_id'];
            $where = 'id';
            $saved = $this->ApiModel->update_common($table,$data,$where,$id);

            $id = $datas['user_id'];
            $where = 'id';
            $table = 'user_details';
            $info = $this->ApiModel->list_common_where($table,$where,$id);

            $to = $info[0]['email'];
            $subject = "Profile Update";
            $txt = "Profile updated Successfully.";
            $headers = "From: noreply@replistinc.com";            
            mail($to,$subject,$txt,$headers);
            
                // $deviceToken=$info[0]['device_token'];
                // $message="Profile updated Successfully.";
                // $mode='HomePage';
                
                // $this->SendNotification($deviceToken,$message,$mode);
                
            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 

    public function AddProducts() {
        
        $user_id  = !empty($this->input->post('user_id'))?trim($this->input->post('user_id')):' ';
        $name  = !empty($this->input->post('name'))?trim($this->input->post('name')):'Test Product';
        $price  = !empty($this->input->post('price'))?trim($this->input->post('price')):'100';
        $stock  = !empty($this->input->post('stock'))?trim($this->input->post('stock')):'100';
        $packsize  = !empty($this->input->post('packsize'))?addslashes(trim($this->input->post('packsize'))):'packsize';
        $description  = !empty($this->input->post('description'))?addslashes(trim($this->input->post('description'))):'description';
        $image  = !empty($_FILES['image'])?$_FILES['image']:''; 
        
        
        if(!empty($user_id)) {

            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M, Y');
            
            $upload_path = "uploads/product_image/";
                $config['upload_path']          = './'.$upload_path;
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['file_name'] = time();
                $this->load->library('upload', $config);
                $profile_image = '';
                if(!$this->upload->do_upload('image')){
                    $error = array('error' => $this->upload->display_errors());
                }else{
                    $upload_data = $this->upload->data(); 
                    $pro_image = $this->url.$upload_path.$upload_data['file_name'];
                }
                

            $data = array('user_id' => $user_id,
                          'name' => $name,
                          'price' => $price,
                          'packsize' => $packsize,
                          'description' => $description,
                          'stock' => $stock,
                          'image' => $pro_image,
                          'date' => $date);
            $table = 'product';
            $saved = $this->ApiModel->insert_common($table,$data);

            if(!empty($saved)) {
                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit(); 

            }else {
                $meta = array('code' => '400', 'message' => 'Failure');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }            

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }


    public function FetchMyProduct() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $table = 'product';
            $where = 'user_id';
            $id = $datas['user_id'];
            $data = $this->ApiModel->list_common_where($table,$where,$id);
            
            if(!empty($data)) {
                foreach($data as $values) {
                    $products[] = array('id' => $values['id'],'name' =>$values['name'] , 'image' => $values['image']);
                }
            }else {
                $products[] = array();
            }
            
            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $products];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 



    public function FetchProductInfo() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['product_id'])) {

            $table = 'product';
            $where = 'id';
            $id = $datas['product_id'];
            $list = $this->ApiModel->list_common_where($table,$where,$id);

            $data = array('name' => $list[0]['name'],
                          'price' => $list[0]['price'],
                          'packsize' => $list[0]['packsize'],
                          'description' => $list[0]['description'],
                          'stock' => $list[0]['stock'],
                          'files' => $list[0]['image'],
                          'date' => $list[0]['date']);
            
            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $data];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 



    public function DeleteProduct() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['product_id'])) {

            $table = 'product';
            $data = array('flag' => '1');
            $id = $datas['product_id'];
            $where = 'id';
            $saved = $this->ApiModel->update_common($table,$data,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 


    public function UpdateProduct() {
        
        $product_id  = !empty($this->input->post('product_id'))?trim($this->input->post('product_id')):' ';
        $name  = !empty($this->input->post('name'))?trim($this->input->post('name')):'Test Product';
        $price  = !empty($this->input->post('price'))?trim($this->input->post('price')):'100';
        $stock  = !empty($this->input->post('stock'))?trim($this->input->post('stock')):'100';
        $packsize  = !empty($this->input->post('packsize'))?addslashes(trim($this->input->post('packsize'))):'packsize';
        $description  = !empty($this->input->post('description'))?addslashes(trim($this->input->post('description'))):'description';
        $image  = !empty($_FILES['image'])?$_FILES['image']:''; 

        if(!empty($product_id)) {

            if(!empty($image)) {
                
                date_default_timezone_set('Asia/Kolkata');
            $date = date('j M, Y');
            
            $upload_path = "uploads/product_image/";
                $config['upload_path']          = './'.$upload_path;
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['file_name'] = time();
                $this->load->library('upload', $config);
                $profile_image = '';
                if(!$this->upload->do_upload('image')){
                    $error = array('error' => $this->upload->display_errors());
                }else{
                    $upload_data = $this->upload->data(); 
                    $pro_image = $this->url.$upload_path.$upload_data['file_name'];
                }
                
                $data = array(
                          'name' => $name,
                          'price' => $price,
                          'packsize' => $packsize,
                          'description' => $description,
                          'stock' => $stock,
                          'image' => $pro_image,
                          'date' => $date);
                

            }else {
                $data = array(
                          'name' => $name,
                          'price' => $price,
                          'packsize' => $packsize,
                          'description' => $description,
                          'stock' => $stock,
                          'date' => $date);
            }
            $table = 'product';
            $id = $datas['product_id'];
            $where = 'id';

            $saved = $this->ApiModel->update_common($table,$data,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 



    public function ResetPassword() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['email'])) {
            
            $exist = $this->ApiModel->list_common_where('user_details','email',$datas['email']);
            
            if(!empty($exist)) {

                $otp = rand(1000,9999);

                $to = $datas['email'];
                $subject = "Password Reset";
                $txt = "DO NOT SHARE: ".$otp." is the OTP for your Replist account. Keep this OTP private.";
                $headers = "From: noreply@replistinc.com";            
                mail($to,$subject,$txt,$headers);
    
                $data = array('otp' => $otp);
    
                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta, 'data' => $data];
                echo json_encode( $response );
                exit();
                
            }else {
                $meta = array('code' => '400', 'message' => 'User not exist');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit(); 
            }    

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 


    public function UpdatePassword() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['email'])) {

            if($datas['new_password'] == $datas['confirm_password']) {
                $data = array('password' => md5($datas['new_password']));
                $table = 'user_details';
                $id = $datas['email'];
                $where = 'email';

                $saved = $this->ApiModel->update_common($table,$data,$where,$id);

                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit(); 
         
            }else {
                $meta = array('code' => '400', 'message' => 'Password not matched');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }    

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }   


    public function SendMail() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['email'])) {
            
            $to_email = explode(",",$datas['to_email']);
            
            foreach($to_email as $values) {
                
                $to = $values;
                $subject = $datas['subject'];
                $txt = stripslashes($datas['message']);
                $headers = $datas['email'];            
                mail($to,$subject,$txt,$headers);
                
            }

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 



    public function HomeScreen() {
        
        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {
            
            $url = "http://www.alexptech.com/replist/";
            
            $base64_code = "data:image/png;base64,";

            $table = 'advertisement';
            $advertise = $this->ApiModel->list_common_where($table,'role','rep');
            
            if(!empty($advertise)) {
                foreach ($advertise as $value) {
                    $ads[] = array('image' => $base64_code.base64_encode(file_get_contents($url.$value['image'])), 'url' => $value['url'], 'id' => $value['id']);
                }
            }else {
                $ads[] = array();
            }

            $table = 'notes';
            $where = 'user_id';
            $id = $datas['user_id'];
            $notes = $this->ApiModel->list_common_where2($table,$where,$id);

            $data = array('advertisement' => $ads, 'notes' => $notes);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' =>$data];
            echo json_encode( $response );
            exit();      
        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }         
    } 



    public function HitsAdvertisement() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['advertisement_id'])) {    

            $id = $datas['advertisement_id'];
            $this->ApiModel->update_ads($id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit();           

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 



    public function ConnectedUsers() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {    

            $id = $datas['user_id'];
            $lists = $this->ApiModel->list_connected($id);

            foreach ($lists as $value) {

                if($value['friend_id'] != $datas['user_id']) {

                    $list = $this->ApiModel->list_buyer($value['friend_id']);
                    $data[] = array('id' => $list[0]['id'], 
                              'email' => $list[0]['email'],
                              'first_name' => $list[0]['first_name'],
                              'last_name' => $list[0]['last_name'],
                              'contact_number' => $list[0]['contact_number'],
                              'company' => $list[0]['company'],
                              'profile_pic' => $list[0]['profile_pic'],
                              'latitute' => $list[0]['latitute'],
                              'longitute' => $list[0]['longitute'],
                              'type' => $list[0]['type'],
                              'address' => $list[0]['address']); 
                }

                if($value['user_id'] != $datas['user_id']) {

                    $list = $this->ApiModel->list_buyer($value['user_id']);
                    $data[] = array('id' => $list[0]['id'], 
                              'email' => $list[0]['email'],
                              'first_name' => $list[0]['first_name'],
                              'last_name' => $list[0]['last_name'],
                              'contact_number' => $list[0]['contact_number'],
                              'company' => $list[0]['company'],
                              'profile_pic' => $list[0]['profile_pic'],
                              'latitute' => $list[0]['latitute'],
                              'longitute' => $list[0]['longitute'],
                              'type' => $list[0]['type'],
                              'address' => $list[0]['address']); 
                }
            }

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $data];
            echo json_encode( $response );
            exit();           

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 

    public function SearchBox() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['value'])) {

            $search = $this->ApiModel->search($datas['value'],$datas['user_id']);
            
            if(!empty($search)) {
                foreach ($search as $value) {
                    
                    $yes = $this->ApiModel->list_not_friends($datas['user_id'], $value['id']);
                    
                    if(empty($yes)) {
                        $flag_invitation = 0;
                    }else {
                        if($yes[0]['flag'] == 1) {
                            $flag_invitation = 2;
                        }else {
                            $flag_invitation = 1;
                        }
                    }
                    
                    if($value['id'] != $datas['user_id']) {
    
                        $data[] = array('id' => $value['id'], 
                                    'email' => $value['email'],
                                    'first_name' => $value['first_name'],
                                    'last_name' => $value['last_name'],
                                    'contact_number' => $value['contact_number'],
                                    'company' => $value['company'],
                                    'profile_pic' => $value['profile_pic'],
                                    'type' => $value['type'],
                                    'invitation_sent' => $flag_invitation,
                                    'address' => $value['address']); 
                    }                
                }
            }else {
                $data = array();
            }    

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $data];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 

    public function Reports() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $where = 'rep_id';
            $search = $this->ApiModel->order_by_year($where, $datas['user_id']);

            if(!empty($search)) {
                foreach ($search as $value) {
                    $data[] = array('price' => $value['total_price'], 'date' => $value['date']); 
                }
            }else {
                $data = array();
            }

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $data];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 


    public function ReportsByYear() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $year = $datas['year'];
            $where = 'rep_id';
            $search = $this->ApiModel->order_by_month($where, $datas['user_id'], $year);
            
            $ytd = $this->ApiModel->order_details2($where, $datas['user_id'], $year);

            if(!empty($search)) {
                foreach ($search as $value) {
                    $amountdata[] = array('price' => $value['total_price'], 'date' => $value['date'], 'monthly_data' => $value['monthreport']); 
                }
            }else {
                $data = array();
            }    
            
            $data = array('ytd_amount' => $ytd[0]['total_price'], 'amount_by_year' => $amountdata);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $data];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 

    public function ReportsByMonth() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {
            $month = $datas['month'];

            $where = 'rep_id';
            $search = $this->ApiModel->order_by_days($where, $datas['user_id'], $month);

            if(!empty($search)) {
                foreach ($search as $value) {
                    $data[] = array('price' => $value['total_price'], 'date' => $value['date']); 
                }
            }else {
                $data = array();
            }  

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $data];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 




    public function OrderDetails() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $where = 'rep_id';
            $search = $this->ApiModel->order_details($where,$datas['user_id']);

            foreach ($search as $value) {
                
                if($value['flag'] == '1') {
                    $order_status = 'Completed';
                }else if($value['flag'] == '2') {
                    $order_status = 'Cancelled';
                }
                
                $buyer = $this->ApiModel->list_common_where('user_details','id',$value['user_id']);
                $data[] = array('order_id' => $value['order_id'], 'price' => $value['total_price'], 'date' => $value['date'], 'company_logo' => $buyer[0]['company_logo'],
                                'ordered_from' => $buyer[0]['first_name'], 'rep_id' => $buyer[0]['id'], 'order_status' => $order_status); 
            }

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $data];
            echo json_encode( $response );
            exit();   

        }
        else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 
    
    
    public function BuyerOrderDetails() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $id = $datas['user_id'];
            $buyer_id = $datas['buyer_id'];
            $search = $this->ApiModel->individual_order_details_info($id,$buyer_id);
            
            if(!empty($search)) {
                foreach ($search as $value) {
                    
                    if($value['flag'] == '1') {
                        $order_status = 'Completed';
                    }else if($value['flag'] == '2') {
                        $order_status = 'Cancelled';
                    }
                    
                    $buyer = $this->ApiModel->list_common_where('user_details','id',$value['user_id']);
                    
                    $data[] = array('order_id' => $value['order_id'], 'price' => $value['total_price'], 'date' => $value['date'], 'company_logo' => $buyer[0]['company_logo'],
                                    'ordered_from' => $buyer[0]['first_name'], 'rep_id' => $buyer[0]['id'], 'order_status' => $order_status); 
                }
            }else {
                $data = array();
            }

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $data];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 



    public function OrderDetailsInfo() {
            
        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['order_id'])) {

            $search = $this->ApiModel->order_details_info($datas['order_id']);

            foreach ($search as $value) {
                $data[] = array('price' => $value['price'], 
                                'quantity' => $value['quantity'],
                                'name' => $value['name'],
                                'image' => $value['image']); 
            }
            
            $expected_date = date('Y-m-d', strtotime($search[0]['date']. ' + 5 days'));
            $pmethod="";
        //    $pmethod =  $search[0]['payment_mode'];
            $cancel_flag = date('Y-m-d H:i:s', strtotime($search[0]['timestamp']. ' + 1 days')); 
            date_default_timezone_set('Asia/Kolkata');
            $date = date('Y-m-d H:i:s'); 
            
            if($search[0]['flag'] == '2') {
                $cancel_flag = 1;   
            }else {
                if(strtotime($date) < strtotime($cancel_flag)) {
                    $cancel_flag = 0;
                }else {
                    $cancel_flag = 1;
                }
            }
            
            $rep_id = $search[0]['user_id']; 
            $rep_detail = $this->ApiModel->list_common_where2('user_details','id',$rep_id);
            
            $output = array('date' => $search[0]['date'], 'expected_date' => $expected_date, 'total_price' => $search[0]['total_price'], 'ordered_by' => $rep_detail[0]['first_name']." (".$rep_detail[0]['email'].")"
                            , 'contact_number' => $rep_detail[0]['contact_number'],'payment_mode' =>$pmethod, 'details' => $data, 'order_id' => $search[0]['order_id'], 'cancel_flag' => $cancel_flag);
                            

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $output];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 


    public function ShareInformation() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {
            
            $table = 'user_details';
            $where = 'email';
            $id = $datas['email_id'];
            $listing = $this->ApiModel->list_common_where($table,$where,$id);
            

            $data = array('share_with' => $listing[0]['id']);
            $table = 'user_details';
            $id = $datas['user_id'];
            $where = 'id';
            $saved = $this->ApiModel->update_common($table,$data,$where,$id);

            $table = 'invitation';
            $where = 'user_id';
            $id = $datas['user_id'];
            $list = $this->ApiModel->list_common_where2($table,$where,$id);

            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M, Y');

            foreach ($list as $value) {
                $data = array('user_id' => $listing[0]['id'], 'friend_id' => $value['friend_id'], 'date' => $date, 'flag' => '1');
                $table = 'invitation';
                $saved = $this->ApiModel->insert_common($table,$data);
            }           

            $table = 'invitation';
            $where = 'friend_id';
            $id = $datas['user_id'];
            $list = $this->ApiModel->list_common_where2($table,$where,$id);

            foreach ($list as $value) {
                $data = array('user_id' => $value['user_id'], 'friend_id' => $listing[0]['id'], 'date' => $date, 'flag' => '1');
                $table = 'invitation';  
                $saved = $this->ApiModel->insert_common($table,$data);
            }            

            if(!empty($saved)) {
                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit(); 

            }else {
                $meta = array('code' => '400', 'message' => 'Failure');
                $response = ['response' => $meta];
                echo json_encode( $response );
                exit();
            }            

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }   
    
    
    
    public function CancelOrder() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['order_id'])) {

            date_default_timezone_set('Asia/Kolkata');
            $date = date('Y-m-d H:i:s');
            
            $data = array('flag' => '2', 'cancel_date' => $date);
            $table = 'order_details';
            $id = $datas['order_id'];
            $where = 'order_id';
            $saved = $this->ApiModel->update_common($table,$data,$where,$id);
            
            $getBuyer = $this->ApiModel->get_buyer_id($datas['order_id']);    
            $u_id=$getBuyer[0]['user_id'];
            $list = $this->ApiModel->list_buyer($u_id);
            
            $deviceToken=$list[0]['device_token'];
            $message="Your ".$id."  has been Cancelled.";
            $mode='OrderPlaced';
            $this->SendNotification($deviceToken,$message,$mode,$list[0]['type']);
                
            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        
        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    }   
    
    public function SendNotification($deviceToken,$message,$mode,$type) {
        //
     //   $deviceToken = '91decf4a476bb69b1efb089b458141ddb0d904b3a90774b086c9ac97c55b5053'; //  iPad 5s Gold prod
        $passphrase = '123456';
      //  $message = 'Hello Push Notification';
      //  $mode = 'OrderPlaced';
        //$ctx = stream_context_create();
        /*echo $deviceToken;
        echo $message;
        echo $mode;
        echo $type;*/
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);
        if($type=='buyer'){
            stream_context_set_option($ctx, 'ssl', 'local_cert', '/home/alexptech/public_html/CertificateLive.pem');    
        }else{
            stream_context_set_option($ctx, 'ssl', 'local_cert', '/home/alexptech/public_html/pushcert.pem');
        }
        
        //Pem file to generated // openssl pkcs12 -in pushcert.p12 -out pushcert.pem -nodes -clcerts // .p12 private key generated from Apple Developer Account
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx); // production
       // $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx); // developement
       // echo "<p>Connection Open</p>";
        if(!$fp){
      //      echo "<p>Failed to connect!<br />Error Number: " . $err . " <br />Code: " . $errstrn . "</p>";
            return;
        } else {
       //     echo "<p>Sending notification!</p>";    
        }
        
        
        $body['aps'] = array('alert' => $message,'sound' => 'default','extra1'=>$mode,'extra2'=>'value');
        $output = $body['aps'];
        $payload = json_encode($body);
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        //var_dump($msg)
        $result = fwrite($fp, $msg, strlen($msg));
        if (!$result)
        {
        //    echo '<p>Message not delivered ' . PHP_EOL . '!</p>';
           $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit();
        }
        else
        {
         //   echo '<p>Message successfully delivered ' . PHP_EOL . '!</p>';
             $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta,'data' => $output];
            echo json_encode( $response );
            exit();
        }
        fclose($fp);   
     }

     public function SendNotificationTest() {
        //$deviceToken,$message,$mode
        $deviceToken = '411f22871e6be63e290a1ca84dcea323f982a79c22820d55d9c26c52c2f77e5d'; //  iPad 5s Gold prod
        $passphrase = '123456';
        $message = 'Hello Push Notification test';
        $mode = 'OrderPlaced';
        $type='buyer';
        $this->SendNotification($deviceToken,$message,$mode,$type);   
     }
    

}
?>    