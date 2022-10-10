<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buyers extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('ApiModel');
    }



    // SignUp with email address, password
    public function Login() {
    	
    	$datas=json_decode(file_get_contents('php://input'),1); 

    	if(!empty($datas['email']) && (!empty($datas['password']))) {

    		$type = 'buyer';
    		$user = $this->ApiModel->login($datas['email'],md5($datas['password']),$type);

    		if(!empty($user)) {
    			$data = array('id' => $user[0]['id'], 'email' => $user[0]['email'], 'type' => 'buyer', 'profile_pic' => $user[0]['profile_pic'], 'first_name' => $user[0]['first_name']);

                date_default_timezone_set('Asia/Kolkata');
    		    $date = date('j M, Y');
    		    
    			$where = 'id';
    			$table = 'user_details';
    			$id = $user[0]['id'];
    			$device_token = $datas['device_token'];
    			//$datas = array('last_login' => $date );
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
        public function SignUp() {
    	
    	$datas=json_decode(file_get_contents('php://input'),1); 

    	if(!empty($datas['email']) && (!empty($datas['password']))) {
    	    
    	    $exist = $this->ApiModel->list_common_where('user_details','email',$datas['email']);
    	    
    	    if(empty($exist)) {

        		date_default_timezone_set('Asia/Kolkata');
        		$date = date('j M, Y');
    
        		$data = array('email' => $datas['email'], 
        		              'password' => md5($datas['password']), 
        		              'first_name' => $datas['first_name']." ".$datas['last_name'], 
        		              'last_name' => $datas['last_name'],
        		              'business_type' => $datas['business_type'],
        		              'department' => $datas['department'],
        		              'contact_number' => $datas['contact_number'], 
        		              'company' => $datas['company'], 
        		              'address' => $datas['address'], 
        		              'city' => $datas['city'], 
        		              'state' => $datas['state'], 
        		              'pincode' => $datas['pincode'], 
        		              'company_contact' => $datas['company_contact'],
        		              'area_cover' => $datas['area_cover'],
        		              'latitute' => $datas['latitute'],
        		              'longitute' => $datas['longitute'],
        		              'type' => 'buyer',
        		              'last_login' => $date,
        		              'joining_date' => $date);
        		$table = 'user_details';
    
        		$id = $this->ApiModel->insert_common($table,$data);
    
                $to = $datas['email'];
                $subject = "Congratulations";
                $txt = "Congratulations, For Signup Successfully.";
                $headers = "From: noreply@replistinc.com";            
                mail($to,$subject,$txt,$headers);
    
        		if(!empty($id)) {
        			$data = array('id' => $id, 'email' => $datas['email'], 'type' => 'buyer');
    
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



    // signup 2nd page 
  public function UpdatePhotos() {
        
         $user_id = !empty($this->input->post('user_id'))?trim($this->input->post('user_id')):'';
        $profilePic  = !empty($_FILES['profile_pic'])?$_FILES['profile_pic']:'';
        $companyLogo  = !empty($_FILES['company_logo'])?$_FILES['company_logo']:'';
        $customerApplication  = !empty($_FILES['customer_application'])?$_FILES['customer_application']:''; 

    	if(!empty($user_id)) {
    	    
            $table = 'user_details';
            $where = 'id';
            $id = $datas['user_id'];
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
                                          'profile_pic' => $list[0]['profile_pic'],
                                          'address' => $list[0]['address']); 
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
                                          'profile_pic' => $list[0]['profile_pic'],
                                          'address' => $list[0]['address']); 
    
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
            {
            
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
            {
            $data = array('user_id' => $datas['user_id'],
                          'friend_id' => $datas['friend_id'],
                          'message' => $datas['message'],
                          'date' => $date);
            $table = 'invitation';
            $saved = $this->ApiModel->insert_common($table,$data);
                
            }
            
          /*  $data = array('user_id' => $datas['user_id'],
                          'friend_id' => $datas['friend_id'],
                           'message' => $datas['message'],
                          'date' => $date);
            $table = 'invitation';*/

                $list = $this->ApiModel->list_buyer( $datas['friend_id']);
                
                $deviceToken=$list[0]['device_token'];
                $message="You have a new friend request.";
                $mode='FriendRequest';
                
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



    public function FetchInvitation() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $where = 'friend_id';
            $id = $datas['user_id'];
            $table = 'invitation';
            $dat = $this->ApiModel->list_common_where($table,$where,$id);
            
            if(!empty($dat))
                foreach($dat as $value) {
                    $id = $value['user_id'];
                    $where = 'id';
                       $message=$value['message'];
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

            if(!empty($data)) {
                $meta = array('code' => '200', 'message' => 'Success');
                $response = ['response' => $meta, 'data' =>$data];
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

    
    public function FetchDocumentInfo() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['document_id'])) {

            $table = 'documents';
            $where = 'id';
            $id = $datas['document_id'];
            $info = $this->ApiModel->list_common_where2($table,$where,$id);
            
            if(!empty($info)) {
                $data = array('id' => $info[0]['id'], 'heading' => $info[0]['heading'], 'image' => $info[0]['files'], 'created_date' => $info[0]['created_date']);
            }else {
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
                        'state' => $info[0]['state'],
                         'business_type' => $info[0]['business_type'],
                         'rem_day'=>$info[0]['rem_day'],
                            'fb' => $info[0]['fb'],
                        'insta' => $info[0]['insta'],
                        'website' => $info[0]['website'],
                        'linkedin' => $info[0]['linkedin'],
                        'company_logo' => $info[0]['company_logo'],
                        'area_cover' => $info[0]['area_cover'],
                        'latitute' => $info[0]['latitute'],
                        'longitute' => $info[0]['longitute'],
                        'last_login' => $info[0]['last_login'],
                        'company_contact' => $info[0]['company_contact'],
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

            $data = array('email' => $datas['email'],
                            'first_name' => $datas['first_name'], 
                          'last_name' => $datas['last_name'], 
                          'company' => $datas['company'],
                          'address' => $datas['address'],
                          'pincode' => $datas['pincode'],
                          'city' => $datas['city'], 
                          'state' => $datas['state'], 
                          'company_contact' => $datas['company_contact'],
                          'contact_number' => $datas['contact_number'],
                           'business_type' => $datas['business_type'],
                        'area_cover' => $datas['area_cover'],
    		              'latitute' => $datas['latitute'],
    		              'longitute' => $datas['longitute'],
    		              'rem_day'=>$datas['rem_day'],
    		                 'insta' => $datas['insta'], 
                          'linkedin' => $datas['linkedin'], 
                          'website' => $datas['website'], 
                          'fb' => $datas['fb'], 
                           'device_token' => $datas['device_token'],
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
            
            $id = $datas['email'];
            $where = 'email';
            $table = 'user_details';
            $info = $this->ApiModel->list_common_where($table,$where,$id);
            
            if(!empty($info)) {
                
                if($info[0]['type'] == 'buyer') {

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
                    $meta = array('code' => '400', 'message' => 'User not Registered as Buyer');
                    $response = ['response' => $meta];
                    echo json_encode( $response );
                    exit(); 
                }    
            
            }else {
                $meta = array('code' => '400', 'message' => 'User not Found');
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
                $txt = $datas['message'];
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
            $advertise = $this->ApiModel->list_common_where($table,'role','buyer');

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

                    if($value['type'] == 'rep') {
                        
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
                }
            }else {
                $data = array();
            }    
            
            if(empty($data)) {
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



    public function AddToCart() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {
            
            $cartdetail = $this->ApiModel->list_common_where('order_details','user_id',$datas['user_id']);
            
            date_default_timezone_set('Asia/Kolkata');
            $date = date('d M, Y');
            
            if(empty($cartdetail)) {
    
                $data = array('user_id' => $datas['user_id'],
                              'rep_id' => $datas['rep_id'],
                              'product_id' => $datas['product_id'],
                              'quantity' => $datas['quantity'],
                              'price' => $datas['price'],
                              'date' => $date);
            }
            else {
                if($cartdetail[0]['rep_id'] == $datas['rep_id']) {
                    
                    $data = array('user_id' => $datas['user_id'],
                              'rep_id' => $datas['rep_id'],
                              'product_id' => $datas['product_id'],
                              'quantity' => $datas['quantity'],
                              'price' => $datas['price'],
                              'date' => $date);
                    
                }else {
                    
                    $meta = array('code' => '201', 'message' => 'Please remove items from old rep');
                    $response = ['response' => $meta];
                    echo json_encode( $response );
                    exit(); 
                }
            }    

            $table = 'order_details';
            $saved = $this->ApiModel->insert_common($table,$data);
            
            $this->ApiModel->update_stock($datas['product_id'],$datas['quantity']);

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

    public function CleanCart() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $table = 'order_details';
            $where = 'user_id';
            $id = $datas['user_id'];
            $data = $this->ApiModel->delete_common($table,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => []];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 
    
    
    public function DeleteFromCart() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['cart_id'])) {

            $table = 'order_details';
            $where = 'id';
            $id = $datas['cart_id'];
            $data = $this->ApiModel->delete_common($table,$where,$id);

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => []];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 
    

    public function FetchCart() {

        $datas=json_decode(file_get_contents('php://input'),1); 
         $TotalAmountValue = 0;
         $rep_idcheck=0;
        if(!empty($datas['user_id'])) {

            $table = 'order_details';
            $where = 'user_id';
            $id = $datas['user_id'];
            $data = $this->ApiModel->list_common_where($table,$where,$id);
            if(!empty($data)) {
                foreach($data as $values) {
                    
                    $table = 'product';
                    $where = 'id';
                    $id = $values['product_id'];
                    $pro = $this->ApiModel->list_common_where($table,$where,$id);
                   $rep_idcheck=$values['rep_id'];
                    $TotalAmountValue = $TotalAmountValue + ($values['price'] * $values['quantity']);
                    $result[] = array('id' => $values['id'], 'product_image' => $pro[0]['image'], 'product_name' => $pro[0]['name'], 'price' => $values['price'],'packsize' => $pro[0]['packsize'], 'quantity' => $values['quantity']);
                }
            }else {
                       $result[] = array();
                   $TotalAmountValue = 0;
                     $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta,'total_amount' =>$TotalAmountValue , 'data' => [] ];
            echo json_encode( $response );
            exit();   

           // $response = ['code' => '200','data'=>$cart];
           // echo json_encode( $response );
        
            }

            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta,'total_amount' =>$TotalAmountValue  ,'rep_id'=>$rep_idcheck,'data' => $result ];
            echo json_encode( $response );
            exit();   

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 



    public function PlaceOrder() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            date_default_timezone_set('Asia/Kolkata');
            $date = date('d M, Y');

            $order_id = "ORD".round(microtime(true));
            
            if($datas['payment_mode'] == "COD") {
                $val = 1;
            }else {
                $val = 0;
            }

            $data = array('order_id' => $order_id, 'date' => $date, 'flag' => '1', 'payment_mode' => $datas['payment_mode'], 'total_price' => $datas['total_price'], 'payment_done' => $val);
            $id = $datas['user_id'];
            $where = 'user_id';
            $table = 'order_details';
            $saved = $this->ApiModel->update_common_where($table,$data,$where,$id); 
            $table = 'user_details';
            $where = 'id';
            $id = $datas['user_id'];
            $data = $this->ApiModel->list_common_where($table,$where,$id);
// to buyer
            $to = $data[0]['email'];
            $subject = "Your replist order #".$order_id." | Replist Inc";
            $txt = "Thank you for using Replist for your orders";
            $headers = "From: noreply@replistinc.com";            
            mail($to,$subject,$txt,$headers);

// to rep   
            $rep_id=$this->ApiModel->get_rep_id($order_id);
            
              $id2 = isset($rep_id[0]['rep_id'])?$rep_id[0]['rep_id']:'';
            $data2 = $this->ApiModel->list_common_where($table,$where,$id2);
            $to = isset($data2[0]['email'])?$data2[0]['email']:'';
            $subject = "You received order #".$order_id." | Replist Inc";
            $txt = "Congratulations, Please check app you have received order.";
            $headers = "From: noreply@replistinc.com";            
            mail($to,$subject,$txt,$headers);

                $list = $this->ApiModel->list_buyer($id2);
                
                $deviceToken=$list[0]['device_token'];
                $message="You received an order ".$order_id;
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




    public function Reports() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $where = 'user_id';
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
            $where = 'user_id';
            $search = $this->ApiModel->order_by_month($where, $datas['user_id'],$year);
            
            $ytd = $this->ApiModel->order_details2($where, $datas['user_id'],$year);
            
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
            $where = 'user_id';
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

            $where = 'user_id';
            $search = $this->ApiModel->order_details($where,$datas['user_id']);

            foreach ($search as $value) {
                
                $rep = $this->ApiModel->list_common_where('user_details','id',$value['rep_id']);
                
                if($value['flag'] == '1') {
                    $order_status = 'Completed';
                }else if($value['flag'] == '2') {
                    $order_status = 'Cancelled';
                }
                
                $data[] = array('order_id' => $value['order_id'], 'price' => $value['total_price'], 'date' => $value['date'], 'company_logo' => $rep[0]['company_logo'],
                                'ordered_by' => $rep[0]['first_name'], 'rep_id' => $rep[0]['id'], 'order_status' => $order_status); 
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
            

            $expected_date = date('d M, Y', strtotime($search[0]['date']. ' + 5 days'));
               $pmethod="";
         // $pmethod =  $search[0]['payment_mode'];
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

            $rep_id = $search[0]['rep_id']; 
            $rep_detail = $this->ApiModel->list_common_where2('user_details','id',$rep_id);
            
            $output = array('date' => $search[0]['date'], 'expected_date' => $expected_date, 'total_price' => $search[0]['total_price'], 'ordered_from' => $rep_detail[0]['first_name']." (".$rep_detail[0]['email'].")"
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



    public function FetchProductInfo() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['product_id'])) {

            $table = 'product';
            $where = 'id';
            $id = $datas['product_id'];
            $list = $this->ApiModel->list_common_where($table,$where,$id);

            if(!empty($list)) {
                $data = array('name' => $list[0]['name'],
                          'price' => $list[0]['price'],
                          'packsize' => $list[0]['packsize'],
                          'description' => $list[0]['description'],
                          'stock' => $list[0]['stock'],
                          'files' => $list[0]['image'],
                          'date' => $list[0]['date']);
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
    


    public function FetchProduct() {

        $datas=json_decode(file_get_contents('php://input'),1); 
            $rep_id = $datas['rep_id'];
            $rep_id_url = $this->uri->segment('3');
        if(!empty($rep_id) ) {

            $table = 'product';
            $where = 'user_id';
            $id = $rep_id;
            $data = $this->ApiModel->list_common_product($table,$where,$id);
            
            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'data' => $data];
            echo json_encode( $response );
            exit(); 

        }
        else if(!empty($rep_id_url) ) {

            $table = 'product';
            $where = 'user_id';
            $id = $rep_id_url;
            $data = $this->ApiModel->list_common_product_url($table,$where,$id);
            
            
            $table = 'user_details';
            $where = 'id';
            $id = $rep_id_url;
            $userdata = $this->ApiModel->list_common_where_url($table,$where,$id);
            
            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['data' => $data , 'user'=>$userdata];
            echo json_encode( $response );
            exit(); 

        }else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); 
        }
    } 




    public function OnlinePaymentSetting() {
   $datas=json_decode(file_get_contents('php://input'),1); 

             if(!empty($datas['rep_id'])) {
              $rep = $this->ApiModel->list_common_where('user_details','id',$datas['rep_id']);
            
           /* $table = 'setting';
            $datas = $this->ApiModel->list_common($table);
*/                        //$convinience = $datas[0]['conveniece_tax'];
                          //$delieveryfee = $datas[0]['delivery_fee'];
                          $discount = $rep[0]['cod_discount'];
                         
            $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta, 'cod_discount' => $discount];
            echo json_encode( $response );
            exit(); 
             }
                else {
            $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
            echo json_encode( $response );
            exit(); }

       
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



    public function RepeatOrder() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['order_id'])) {
            
            $data = $this->ApiModel->list_common_where3('order_details','order_id',$datas['order_id']);
            
            date_default_timezone_set('Asia/Kolkata');
            $date = date('d M, Y');
            
            foreach($data as $value) {
                
                $data = array('user_id' => $value['user_id'], 'product_id' => $value['product_id'], 'rep_id' => $value['rep_id'], 'quantity' => $value['quantity'], 'price' => $value['price']
                              , 'total_price' => $value['total_price'], 'date' => $date);
                $table = 'order_details';
                $saved = $this->ApiModel->insert_common($table,$data);
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
            
            $getBuyer = $this->ApiModel->get_rep_id($datas['order_id']);    
            $u_id=$getBuyer[0]['user_id'];
            $list = $this->ApiModel->list_buyer($u_id);
            
            $deviceToken=$list[0]['device_token'];
            $message="One Order has been Cancelled.";
            $mode='OrderCancelled';
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
    
    
    
    
    public function RepOrderDetails() {

        $datas=json_decode(file_get_contents('php://input'),1); 

        if(!empty($datas['user_id'])) {

            $id = $datas['user_id'];
            $rep_id = $datas['rep_id'];
            $search = $this->ApiModel->individual_order_details_info2($id,$rep_id);
            
            if(!empty($search)) {
                foreach ($search as $value) {
                    
                    if($value['flag'] == '1') {
                        $order_status = 'Completed';
                    }else if($value['flag'] == '2') {
                        $order_status = 'Cancelled';
                    }
                    
                    $buyer = $this->ApiModel->list_common_where('user_details','id',$value['rep_id']);
                    
                    $data[] = array('order_id' => $value['order_id'], 'price' => $value['total_price'], 'date' => $value['date'], 'company_logo' => $buyer[0]['company_logo'],
                                    'ordered_by' => $buyer[0]['first_name'], 'rep_id' => $buyer[0]['id'], 'order_status' => $order_status); 
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

    public function SendNotification($deviceToken,$message,$mode,$type) {
        
        $passphrase = '123456';
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);
        if($type=='rep'){
            stream_context_set_option($ctx, 'ssl', 'local_cert', '/home/alexptech/public_html/pushcert.pem');    
        }else{
            stream_context_set_option($ctx, 'ssl', 'local_cert', '/home/alexptech/public_html/CertificateLive.pem');
        }
        
       
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx); if(!$fp){
            return;
        } else {
       //     echo "<p>Sending notification!</p>";    
        }
        
        
        $body['aps'] = array('alert' => $message,'sound' => 'default','extra1'=>$mode,'extra2'=>'value');
        $output = $body['aps'];
        $payload = json_encode($body);
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        //var_dump($msg);
        $result = fwrite($fp, $msg, strlen($msg));
        if (!$result)
        {
        //    echo '<p>Message not delivered ' . PHP_EOL . '!</p>';
           $meta = array('code' => '400', 'message' => 'Invalid Request');
            $response = ['response' => $meta];
           // echo json_encode( $response );
        //    exit();
        }
        else
        {
         //   echo '<p>Message successfully delivered ' . PHP_EOL . '!</p>';
             $meta = array('code' => '200', 'message' => 'Success');
            $response = ['response' => $meta,'data' => $output];
          //  echo json_encode( $response );
            //exit();
        }
        fclose($fp);   
     
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