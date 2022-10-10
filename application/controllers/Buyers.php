<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
use Twilio\Rest\Client;
    
class Buyers extends CI_Controller 
{    
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Home_Model','HM');
		$this->load->library('Authorization_Token');
		$this->load->library('email'); 
		$this->load->library('common');
		$this->load->config('twilio'); 
	} 

	public function verify_token()
	{
		$headers = $this->input->request_headers();
		
		if(array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) 
		{
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

			if ($decodedToken['status'] === true) 
			{
				return $decodedToken;
			}
		}

		echo json_encode(array("status" => '0',"message" => 'Token is mismatched !'));
		exit();
	}

	public function sendsms($code,$mobile,$msg) 
	{
		if($this->input->server('REQUEST_METHOD') == 'POST')
	 	{
	 		try
	 		{
				$sid = $this->config->item('sid'); // Twilio SID
				$token = $this->config->item('token'); // Twilio Token
				$phone = $this->config->item('phone_number'); // Twilio Phone number

				$twilio_client = new Client($sid, $token);

				$twilio_client->messages->create($code.$mobile,[
					'from'=> $phone,
					'body'=> $msg
				]);
			}
			catch(exception $ex)
			{
				echo json_encode(array("status" => '0',"message" => $ex->getMessage()));
			}
		}
	}

	public function sendpushnotification() 
	{   
		$load = array();
 
		$a=array();
		$a['title'] = 'App Open';
        $a['msg'] = 'App Open';

        $load['data']['title'] = 'App Open';
        $load['data']['is_background'] = false;
        $load['data']['message'] = 'App Open';
        $load['data']['image'] = 'https://phpstack-659985-2283418.cloudwaysapps.com/public/admin/images/logo.png';
        $load['data']['payload'] = $a;
        $load['data']['timestamp'] = date('Y-m-d h:i:s'); 

        $token[] = 'f4bMhzQzQaW93pjxA1ffrW:APA91bFT9WI9r27onbqMYZ7nT_IljnaLG47naH4cGZRckVgzLuJVfbIh5EipVKZCD3wQUK0BxkZWccTmSOTR-Oc0zT9WCgPAOgkDJEVldOeZUkQgYg6iWv9XpSExuPOe2IBr47tXAMh_';

        $this->common->android_push($token,$load,'AAAAkyClyO4:APA91bEgg_4H05w2_C56aGzb83iyQDqNTXhligpYj4rrBUHXzCubmtuKtJtKqGtxvqHejg2JLz7oMlSVQlv57ZVYZAHWNZLkx5lJ11Fj0k5grkv__th9AyoEsOYUyrnqONUSEyeLvhNM');

        echo json_encode($load);
	} 
 
	public function SignUp()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$register_post = file_get_contents("php://input");
			$decode_post = json_decode($register_post);

			if(count((array)$decode_post)==9)
			{
				if(!empty($decode_post->first_name) && !empty($decode_post->last_name) && !empty($decode_post->email) && !empty($decode_post->mobile) && !empty($decode_post->password) && !empty($decode_post->device_id) && !empty($decode_post->firebase_token) && !empty($decode_post->message_key))
				{
					// Check user email address already exist or not
					$this->db->select('*');
					$this->db->where('u_email',$decode_post->email);
					$select1 = $this->db->get('tbl_users');
					$num1 = $select1->num_rows(); 

					// Check user mobile number already exist or not
					$this->db->select('*');
					$this->db->where('u_mobile',$decode_post->mobile);
					$select = $this->db->get('tbl_users');
					$num = $select->num_rows(); 

					if($num1==0)
					{ 
						if($num==0)
						{ 	
							if(!empty($decode_post->referal_code))
							{
								$referal_code = $this->HM->check_referal_code($decode_post->referal_code);

								if($referal_code!=0)
								{
									$char = substr($decode_post->first_name, -2);
									$num = random_int(00,99);

									$user_profile = $this->HM->get_parent_id($decode_post->referal_code);

									$data = array();
									$data['u_user_code'] = strtoupper('RP'.$char.$num);
									$data['u_first_name'] = $decode_post->first_name;
									$data['u_last_name'] = $decode_post->last_name;
									$data['u_email'] = $decode_post->email;
									$data['u_mobile'] = $decode_post->mobile;
									$data['u_password'] = md5($decode_post->password);
									$data['u_decrypt_password'] = $decode_post->password;
									$data['u_device_id'] = $decode_post->device_id;
									$data['u_firebase_token'] = $decode_post->firebase_token;
									$data['u_type'] = 'Buyer';
									$data['u_joining_date'] = date('m-d-Y');
									$data['u_message_key'] = $decode_post->message_key;
									$data['u_verified'] = '0'; 
									$data['u_referal_code'] = $decode_post->referal_code;
									$data['u_parent_id'] = $user_profile->u_id; 

									$insert_id = $this->HM->add_user($data);

									// Send otp to user mobile number
									$data=array();
									$data['o_user_id'] = $insert_id; 
									$data['o_mobile_number'] = $decode_post->mobile;
									$data['o_otp'] = '123456';
									$data['o_source'] = 'Mobile';
									$data['o_type'] = 'Verify';

									$otp_id = $this->HM->send_otp($data);

									// Reward Points
									$reward = $this->HM->manage_reward_points($user_profile->u_id);

									if(!empty($reward->rp_total_points))
									{
										$data = array();
										$data['rp_user_id'] = $user_profile->u_id;
										$data['rp_points'] = '50';
										$data['rp_type'] = 'Credit';
										$data['rp_total_points'] = $reward->rp_total_points+50;
										$data['rp_transaction_history'] = '50 points credited in your account';
												
										$this->HM->add_reward_points($data);
									}
									else
									{
										$data = array();
										$data['rp_user_id'] = $user_profile->u_id;
										$data['rp_points'] = '50';
										$data['rp_type'] = 'Credit';
										$data['rp_total_points'] = '50';
										$data['rp_transaction_history'] = '50 points credited in your account';

										$this->HM->add_reward_points($data);
									}

									if($insert_id)
									{  
										echo json_encode(array("status" => '1',"message" => 'OTP has been sent to your phone number !',"mobile_number"=>$decode_post->mobile));
									} 
									else
									{
										echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
									}
								}
								else
								{
									echo json_encode(array("status" => '0',"message" => 'The referal code is not match with our database !'));
								}
							}
							else
							{
								$char = substr($decode_post->first_name, -2);
								$num = random_int(00,99);

								$user_profile = $this->HM->get_parent_id($decode_post->referal_code);

								$data = array();
								$data['u_user_code'] = strtoupper('RP'.$char.$num);
								$data['u_first_name'] = $decode_post->first_name;
								$data['u_last_name'] = $decode_post->last_name;
								$data['u_email'] = $decode_post->email;
								$data['u_mobile'] = $decode_post->mobile;
								$data['u_password'] = md5($decode_post->password);
								$data['u_decrypt_password'] = $decode_post->password;
								$data['u_device_id'] = $decode_post->device_id;
								$data['u_firebase_token'] = $decode_post->firebase_token;
								$data['u_type'] = 'Buyer';
								$data['u_message_key'] = $decode_post->message_key;
								$data['u_verified'] = '0'; 
								$data['u_referal_code'] = '';
								$data['u_parent_id'] = ''; 

								$insert_id = $this->HM->add_user($data);

								// Send otp to user mobile number
								$data=array();
								$data['o_user_id'] = $insert_id; 
								$data['o_mobile_number'] = $decode_post->mobile;
								$data['o_otp'] = '123456';
								$data['o_source'] = 'Mobile';
								$data['o_type'] = 'Verify';

								$otp_id = $this->HM->send_otp($data);

								if($insert_id)
								{  
									echo json_encode(array("status" => '1',"message" => 'OTP has been sent to your phone number !',"mobile_number"=>$decode_post->mobile));
								} 
								else
								{
									echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
								}
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'The mobile number is already exist in our database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The email address is already exist in our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The name, email, mobile, password device_id, token and mesage key are required fileds !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function Login()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$login_post = file_get_contents("php://input");
			$decode_post = json_decode($login_post);

			if(count((array)$decode_post)==5)
			{
				if(!empty($decode_post->email) && !empty($decode_post->password) && !empty($decode_post->device_id) && !empty($decode_post->firebase_token) && !empty($decode_post->message_key))
				{
					$this->db->select('*');
					$this->db->where('u_type','Buyer');
					$this->db->where('u_email',$decode_post->email);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$this->db->select('*');
						$this->db->where('u_email',$decode_post->email);
						$this->db->where('u_password',md5($decode_post->password));
						$select = $this->db->get('tbl_users');
						$num1 = $select->num_rows();

						if($num1==1)
						{
							$this->db->select('*');
							$this->db->where('u_email',$decode_post->email);
							$this->db->where('u_password',md5($decode_post->password));
							$select = $this->db->get('tbl_users');
							$num2 = $select->num_rows();

							if($num2==1)
							{		         
								$token=array();
								$token['device_id'] = $run->u_device_id;
								$token['id'] = $run->u_id;
								$token['time'] = time();

								$jwt_token = $this->authorization_token->generateToken($token);	

						        // Update user detail
								$data=array();
								$data['u_device_id'] = $decode_post->device_id;
								$data['u_firebase_token'] = $decode_post->firebase_token;
								$data['u_token'] = $jwt_token;

								$this->HM->update_profile($run->u_id,$data);

								//Get otp detail
								$this->db->select('*');
								$this->db->where('o_mobile_number',$run->u_mobile);
								$select_otp = $this->db->get('tbl_otp');
								$data_otp = $select_otp->row();
								$num_otp = $select_otp->num_rows();
							
								if($run->u_verified=='1')
								{
									$token = $jwt_token;
								}
								else
								{
									$token= '';	

									if($num_otp==0)
									{
										// Send otp to user mobile number
										$data=array();
										$data['o_user_id'] = $run->u_id; 
										$data['o_mobile_number'] = $run->u_mobile;
										$data['o_otp'] = '123456';
										$data['o_source'] = 'Mobile';
										$data['o_type'] = 'Verify';

										$otp_id = $this->HM->send_otp($data);
									}

									// Send otp to user mobile number
									$data=array();
									$data['o_otp'] = '123456';
									$update = $this->HM->update_otp($run->u_mobile,$data);
								}
									
								if($run->u_verified=='1')
								{
									echo json_encode(array("status" => '1',"message" => 'Login successfully !',"token"=>$token,"otp_status"=>'1',"user_id"=>$run->u_id));
								}
								else
								{
									echo json_encode(array("status" => '1',"message" => 'An OTP has been send on your registered mobile number  !',"otp_status"=>'0',"mobile_number"=>$run->u_mobile));
								}
							}
							else
							{
								echo json_encode(array("status" => '0',"message"=>'The password you entered is incorrect !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'You entered wrong credentials !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The email you entered is incorrect !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The email, password, device_id, firebase token and message key are required fileds !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON!'));
		}
	}

	public function logout()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$logout_post = file_get_contents("php://input");
			$decode_post = json_decode($logout_post);

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_type','Buyer');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num!=0)
					{
						$data=array();
						$data['u_token'] = '';

						$update = $this->HM->update_profile($run->u_id,$data);

						if($update)
						{
							echo json_encode(array("status" => '1',"message" => 'You successfully logout !'));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database.!'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{  
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function verify_otp()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$otp_post = file_get_contents("php://input");
			$decode_post = json_decode($otp_post);

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->mobile) && !empty($decode_post->otp))
				{
					$this->db->select('*');
					$this->db->order_by('u_id','desc');
					$this->db->where('u_mobile',$decode_post->mobile);
					$this->db->join('tbl_otp','tbl_otp.o_user_id = tbl_users.u_id');
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num>0)
					{
						if($decode_post->otp==$run->o_otp)
						{
							$data['o_status'] = 1;
							$update = $this->HM->verify_otp($run->u_id,$data);

							$token=array();
							$token['device_id'] = $run->u_device_id;
							$token['id'] = $run->u_id;
							$token['time'] = time();

							$jwt_token = $this->authorization_token->generateToken($token);		

					        // Update user detail
							$data1=array();
							$data1['u_token'] = $jwt_token;
							$data1['u_verified'] = "1";

							$this->HM->update_profile($run->u_id,$data1);

							if(!empty($update))
							{
								echo json_encode(array("status" => '1',"message"=>'Welcome to REPLIST .Your Registration has been successful !',"token"=>$jwt_token,"user_id"=>$run->u_id));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'Please verify your mobile number.'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'You entered wrong OTP.'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The mobile number is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The mobile and otp fields are required!'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function resend_otp()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$otp_post = file_get_contents("php://input");
			$decode_post = json_decode($otp_post);
			$verify_otp = random_int(100000,999999);
			$id = '';

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->mobile) && !empty($decode_post->message_key))
				{
					$this->db->select('*');
					$this->db->where('u_mobile',$decode_post->mobile);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num>0)
					{
						// Send otp to user mobile number
						$data['o_otp'] = $verify_otp;

						$update = $this->HM->update_otp($decode_post->mobile,$data);	
						$last_otp = $this->HM->last_send_otp($run->u_id,$id);

						//Update message key in tbl_users table start
						$data1['u_message_key'] = $decode_post->message_key;
						$this->HM->update_profile($run->u_id,$data1);

						if($update)
						{
							echo json_encode(array("status" => '1',"message" => 'OTP has been send to your registered mobile number.'));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The mobile number is not match with our database.!'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The mobile and message key are required fields!'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function profile() 
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$profile_post = file_get_contents("php://input");
			$decode_post = json_decode($profile_post);

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$data = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$user_detail=array();
					    $user_detail['user_id'] = $data->u_id;  
					    $user_detail['user_code'] = $data->u_user_code;
						$user_detail['first_name'] = $data->u_first_name;
						$user_detail['last_name'] = $data->u_last_name;
						$user_detail['sales_position'] = $data->u_sales_position;
						$user_detail['area_cover'] = $data->u_area_cover;
						$user_detail['mobile'] = $data->u_mobile;
						$user_detail['gender'] = $data->u_gender;
						$user_detail['company'] = $data->u_company;
						$user_detail['department'] = $data->u_department;
						$user_detail['street_address'] = $data->u_street_address;
						$user_detail['email'] = $data->u_email;
						$user_detail['country'] = $data->u_country;
						$user_detail['state'] = $data->u_state;
						$user_detail['city'] = $data->u_city;
						$user_detail['postalcode'] = $data->u_postalcode;
						$user_detail['customer_application'] = $data->u_customer_application;
						$user_detail['company_contact'] = $data->u_company_contact;
						$user_detail['paypal_id'] = $data->u_paypal_id;
						$user_detail['joining_date'] = $data->u_joining_date;

						if(!empty($data->u_business_type))
						{
							$user_detail['business_type'] = $data->u_business_type;
						}
						else
						{
							$user_detail['business_type'] = '';
						}

						if(!empty($data->u_fb))
						{
							$user_detail['fb'] = $data->u_fb;
						}
						else
						{
							$user_detail['fb'] = '';
						}

						if(!empty($data->u_insta))
						{
							$user_detail['insta'] = $data->u_insta;
						}
						else
						{
							$user_detail['insta'] = '';
						}

						if(!empty($data->u_linkedin))
						{
							$user_detail['linkedin'] = $data->u_linkedin;
						}
						else
						{
							$user_detail['linkedin'] = '';
						}

						if(!empty($data->u_website))
						{
							$user_detail['website'] = $data->u_website;
						}
						else
						{
							$user_detail['website'] = '';
						}
		
						if(!empty($data->u_gender))
						{
							$user_detail['gender'] = $data->u_gender;
						}
						else
						{
							$user_detail['gender'] = '';
						}
								
						if(!empty($data->u_image))
						{
							$user_detail['image'] = base_url('public/profile_images/').$data->u_image;
						}
						else
						{
							$user_detail['image'] = '';
						}

						if(!empty($data->u_company_logo))
						{
							$user_detail['company_logo'] = base_url('public/profile_images/').$data->u_company_logo;
						}
						else
						{
							$user_detail['company_logo'] = '';
						}

						if(!empty($data->u_referal_code))
						{
							$user_detail['referal_code'] = $data->u_referal_code;
						}
						else
						{
							$user_detail['referal_code'] = '';
						}

						$user_detail['reminder_date'] = $data->u_reminder_date;

						if(!empty($user_detail))
						{
							echo json_encode(array("status" => '1',"message" => 'User profile details !',"user_details" => $user_detail));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id is required field !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    }

    public function UpdateProfile() 
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$register_post = file_get_contents("php://input");
			$decode_post = json_decode($register_post);

			if(count((array)$decode_post)==22)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$data = array();

						if(!empty($decode_post->first_name))
						{
							$data['u_first_name'] = $decode_post->first_name;
						}
						else
						{
							$data['u_first_name'] = $run->u_first_name;
						}

						if(!empty($decode_post->last_name))
						{
							$data['u_last_name'] = $decode_post->last_name;
						}
						else
						{
							$data['u_last_name'] = $run->u_last_name;
						}

						if(!empty($decode_post->gender))
						{
							$data['u_gender'] = $decode_post->gender;
						}
						else
						{
							$data['u_gender'] = $run->u_gender;
						}

						if(!empty($decode_post->country))
						{
							$data['u_country'] = $decode_post->country;
						}
						else
						{
							$data['u_country'] = $run->u_country;
						}
						
						if(!empty($decode_post->state))
						{
							$data['u_state'] = $decode_post->state;
						}
						else
						{
							$data['u_state'] = $run->u_state;
						}
						
						if(!empty($decode_post->city))
						{
							$data['u_city'] = $decode_post->city;
						}
						else
						{
							$data['u_city'] = $run->u_city;
						}
						
						if(!empty($decode_post->city))
						{
							$data['u_city'] = $decode_post->city;
						}
						else
						{
							$data['u_city'] = $run->u_city;
						}

						if(!empty($decode_post->street_address))
						{
							$data['u_street_address'] = $decode_post->street_address;
						}
						else
						{
							$data['u_street_address'] = $run->u_street_address;
						}
						
						if(!empty($decode_post->postalcode))
						{
							$data['u_postalcode'] = $decode_post->postalcode;
						}
						else
						{
							$data['u_postalcode'] = $run->u_postalcode;
						}

						if(!empty($decode_post->sales_position))
						{
							$data['u_sales_position'] = $decode_post->sales_position;
						}
						else
						{
							$data['u_sales_position'] = $run->u_sales_position;
						}

						if(!empty($decode_post->area_cover))
						{
							$data['u_area_cover'] = $decode_post->area_cover;
						}
						else
						{
							$data['u_area_cover'] = $run->u_area_cover;
						}
						
						if(!empty($decode_post->business_type))
						{
							$data['u_business_type'] = $decode_post->business_type;
						}
						else
						{
							$data['u_business_type'] = $run->u_business_type;
						}

						if(!empty($decode_post->department))
						{
							$data['u_department'] = $decode_post->department;
						}
						else
						{
							$data['u_department'] = $run->u_department;
						}
						
						if(!empty($decode_post->customer_application))
						{
							$data['u_customer_application'] = $decode_post->customer_application;
						}
						else
						{
							$data['u_customer_application'] = $run->u_customer_application;
						}

						if(!empty($decode_post->company))
						{
							$data['u_company'] = $decode_post->company;
						}
						else
						{
							$data['u_company'] = $run->u_company;
						}

						if(!empty($decode_post->company_contact))
						{
							$data['u_company_contact'] = $decode_post->company_contact;
						}
						else
						{
							$data['u_company_contact'] = $run->u_company_contact;
						}

						if(!empty($decode_post->paypal_id))
						{
							$data['u_paypal_id'] = $decode_post->paypal_id;
						}
						else
						{
							$data['u_paypal_id'] = $run->u_paypal_id;
						}

						if(!empty($decode_post->fb))
						{
							$data['u_fb'] = $decode_post->fb;
						}
						else
						{
							$data['u_fb'] = $run->u_fb;
						}

						if(!empty($decode_post->insta))
						{
							$data['u_insta'] = $decode_post->insta;
						}
						else
						{
							$data['u_insta'] = $run->u_insta;
						}

						if(!empty($decode_post->linkedin))
						{
							$data['u_linkedin'] = $decode_post->linkedin;
						}
						else
						{
							$data['u_linkedin'] = $run->u_linkedin;
						}

						if(!empty($decode_post->website))
						{
							$data['u_website'] = $decode_post->website;
						}
						else
						{
							$data['u_website'] = $run->u_website;
						}

						if(!empty($decode_post->reminder_date))
						{
							$data['u_reminder_date'] = $decode_post->reminder_date;
						}
						else
						{
							$data['u_reminder_date'] = $run->u_reminder_date;
						}

						$update = $this->HM->update_profile($run->u_id,$data);

						if($update)
						{  
							echo json_encode(array("status" => '1',"message" => 'Your profile updated successfully !'));
						} 
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The name, email, mobile, password device_id, token and mesage key are required fileds !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    }

    public function update_profile_image()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$user_id = strip_tags($this->input->post('user_id'));
			$upload='';

			if(!empty($user_id))
			{
				$this->db->select('*');
				$this->db->where('u_id',$user_id);
				$this->db->where('u_type','Buyer');
				$select = $this->db->get('tbl_users');
				$run = $select->row();
				$num = $select->num_rows();

				if($num==1)
				{
					$config['upload_path']          = './public/profile_images';
					$config['allowed_types']        = 'gif|jpg|png|jpeg';
					$config['max_size']             = "";
					$config['max_width']            = "";
					$config['max_height']           = "";

					$this->load->library('upload',$config);

					if(!empty($_FILES['image']['name']))          
				    {
						if($this->upload->do_upload('image'))
						{
							$image = $this->upload->data();
							$profile_image = $image['file_name'];
						}
						else
						{
							$error = array('error' => $this->upload->display_errors());
							echo json_encode(array("status" => '0',"message" => 'User Image should be gif, jpg, png, jpeg format !',"error"=>$error));
							exit();
						}
					}
					else
					{
						$profile_image = $run->u_image;
					}

					if(!empty($_FILES['company_logo']['name']))          
					{
					    if(!$this->upload->do_upload('company_logo'))
					    {
					        $error = array('error' => $this->upload->display_errors());
							echo json_encode(array("status" => '0',"message" => 'Company Logo should be gif, jpg, png, jpeg format !',"error"=>$error));
							exit();
					    }
					    else
					    {
						    $image1 = $this->upload->data();
							$company_logo = $image1['file_name'];
						}
					}
					else
					{
						$company_logo = $run->u_company_logo;
					}

					$data=array();
					$data['u_image'] = $profile_image;
					$data['u_company_logo'] = $company_logo;

					$update = $this->HM->update_profile($run->u_id,$data);

					if(!empty($update))
					{
						echo json_encode(array("status" => '1',"message"=>'Your profile image updated successfully !'));
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'The user id is required field !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	} 

    public function BuyersList()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$buyer_post = file_get_contents("php://input");
			$decode_post = json_decode($buyer_post);
			$buyers_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$this->db->where('u_type','Buyer');
					$select = $this->db->get('tbl_users');
					$num = $select->num_rows();

					if($num!=0)
					{
						$this->db->select('*');
						$this->db->where('u_type','Buyer');
						$select1 = $this->db->get('tbl_users');
						$data1 = $select1->result_array();
						$num1 = $select1->num_rows();

						if($num1!=0)
						{
							foreach($data1 as $values)
							{	
								$buyers['id'] = $values['u_id'];
								$buyers['name'] = $values['u_first_name'].$values['u_last_name'];
								$buyers['contact_number'] = $values['u_mobile'];
								$buyers['company'] = $values['u_company'];

								if(!empty($values['u_image']))
								{
									$buyers['image'] = base_url('public/profile_images/').$values['u_image'];
								}
								else
								{
									$buyers['image'] = '';
								}

								array_push($buyers_list,$buyers);
							}

							if($buyers_list)
							{
								echo json_encode(array("status" => '1',"message" => 'Buyers List !',"buyers_list" => $buyers_list));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is required field !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'User id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

    public function RepsList()
	{
    	$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$rep_post = file_get_contents("php://input");
			$decode_post = json_decode($rep_post);
			$reps_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->search))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$data = $select->result_array();
					$num = $select->num_rows();

					if($num!=0)
					{
						if(!empty($decode_post->search))
						{
							$this->db->select('*');
							$this->db->where('u_type','Reps');
							$this->db->like('u_first_name',$decode_post->search);
							$this->db->or_like('u_email',$decode_post->search);
							$select1 = $this->db->get('tbl_users');
							$data1 = $select1->result_array();
							$num1 = $select1->num_rows();
						}

						if($num1!=0)
						{
							foreach($data1 as $values)
							{	
								$reps['id'] = $values['u_id']; 
								$reps['first_name'] = $values['u_first_name']; 
								$reps['last_name'] = $values['u_last_name']; 
								$reps['email'] = $values['u_email'];
								$reps['contact_number'] = $values['u_mobile'];
								$reps['company'] = $values['u_company'];
								$reps['state'] = $values['u_state'];
								$reps['city'] = $values['u_city'];
								$reps['street_address'] = $values['u_street_address'];
								$reps['postalcode'] = $values['u_postalcode'];
								$reps['sales_position'] = $values['u_sales_position'];
								$reps['area_cover'] = $values['u_area_cover'];
								$reps['department'] = $values['u_department'];

								if(!empty($values['u_']))
								{
									$reps['facebook'] = $values['u_fb'];
								}
								else
								{
									$reps['facebook'] = '';
								}
								
								if(!empty($values['u_insta']))
								{
									$reps['instagram'] = $values['u_insta'];
								}
								else
								{
									$reps['instagram'] = '';
								}
								
								if(!empty($values['u_linkedin']))
								{
									$reps['linkedin'] = $values['u_linkedin'];
								}
								else
								{
									$reps['linkedin'] = '';
								}

								if(!empty($values['u_website']))
								{
									$reps['website'] = $values['u_website'];
								}
								else
								{
									$reps['website'] = '';
								}

								if(!empty($values['u_paypal_id']))
								{
									$reps['paypal'] = $values['u_paypal_id'];
								}
								else
								{
									$reps['paypal'] = '';
								}

								if(!empty($values['u_image']))
								{
									$reps['image'] = base_url('public/profile_images/').$values['u_image'];
								}
								else
								{
									$reps['image'] = '';
								}

								if(!empty($values['u_company_logo']))
								{
									$reps['company_logo'] = base_url('public/profile_images/').$values['u_company_logo'];
								}
								else
								{
									$reps['company_logo'] = '';
								}

								array_push($reps_list,$reps);
							}

							if($reps_list)
							{
								echo json_encode(array("status" => '1',"message" => 'reps List !',"reps_list" => $reps_list));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is required field !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'User id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    }

    public function SendInvitation() 
    {
  	    $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
            $send_invitation_post = file_get_contents("php://input");
            $decode_post = json_decode($send_invitation_post);
            $date = date('Y-m-d h:i:s');  $message = " You have a new friend request";

            if(count((array)$decode_post)==3)
            {
                if(!empty($decode_post->user_id) && !empty($decode_post->friend_id)) 
                {
                    $this->db->select('*');
                    $this->db->where('u_id',$decode_post->user_id);
                    $select = $this->db->get('tbl_users');
                    $user_run = $select->row();
                    $num = $select->num_rows();

                    if($num==1)
                    {
                    	$this->db->select('*');
	                    $this->db->where('u_id',$decode_post->friend_id);
	                    $select1 = $this->db->get('tbl_users');
	                    $friend_run = $select1->row();
	                    $num1 = $select1->num_rows();

	                    if($num1==1)
	                    {
	                    	$check_friend = $this->HM->check_friend_exist_or_not($decode_post->user_id,$decode_post->friend_id,);

	                    	if($check_friend==0)
	                    	{
		                        $data = array();
		                        $data['i_sender_id'] = $user_run->u_id;
		                        $data['i_receiver_id'] = $friend_run->u_id;
		                        $data['i_message'] = $message;
		                        $data['i_date'] = $date;
		                        $data['i_flag'] = '0';

		                        $insert_id = $this->HM->add_invitation($data);

		                        if($insert_id)
		                        {  
		                            echo json_encode(array("status" => '1',"message" => 'You send friend request successfully !',"invitation_id"=>(string)$insert_id));
		                        } 
		                        else
		                        {
		                            echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
		                        }
		                    }
		                    else
		                    {
		                    	echo json_encode(array("status" => '0',"message" => 'You have already a connection with him !'));
		                    }
	                    }
	                    else
                    	{
                        	echo json_encode(array("status" => '0',"message" => 'The friend id is not match with our database !'));
                    	}
                    }
                    else
                    {
                        echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
                    }
                }
                else
                {
                    echo json_encode(array("status" => '0',"message" => 'The user id and friend id fields are required !'));
                }
            }
            else
            {
                echo json_encode(array("status" => '0',"message" => 'Bad request !'));
            }
        }
        else
        {
            echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
        }  
    } 

    public function AcceptInvitation() 
    {
        $authentication = $this->verify_token();
  		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
	 		$accept_invitation_post = file_get_contents("php://input");
            $decode_post = json_decode($accept_invitation_post);

            if(count((array)$decode_post)==1)
            {
                if(!empty($decode_post->invitation_id)) 
                {	
                	$this->db->select('*');
                    $this->db->where('i_id',$decode_post->invitation_id);
                    $select = $this->db->get('tbl_invitation');
                    $num = $select->num_rows();

                    if($num==1)
                    {
                    	$data=array();
                    	$data['i_flag'] = '1';

                    	$update = $this->HM->update_invitation($decode_post->invitation_id,$data);

                    	if($update)
	                    {  
	                        echo json_encode(array("status" => '1',"message" => 'You friend request accepted successfully !'));
	                    } 
	                    else
	                    {
	                        echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
	                    }
                    }
                    else
                    {
                        echo json_encode(array("status" => '0',"message" => 'The invitation id is not match with our database !'));
                    }
                }
                else
                {
                    echo json_encode(array("status" => '0',"message" => 'The invitation id field is required !'));
                }
            }
            else
            {
                echo json_encode(array("status" => '0',"message" => 'Bad request !'));
            }
 		}
      	else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    }

    public function RejectInvitation() 
    {
        $authentication = $this->verify_token();
  		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
	 		$reject_invitation_post = file_get_contents("php://input");
            $decode_post = json_decode($reject_invitation_post);

            if(count((array)$decode_post)==1)
            {
                if(!empty($decode_post->invitation_id)) 
                {	
                	$this->db->select('*');
                    $this->db->where('i_id',$decode_post->invitation_id);
                    $select = $this->db->get('tbl_invitation');
                    $num = $select->num_rows();

                    if($num==1)
                    {
                    	$data=array();
                    	$data['i_flag'] = '2';

                    	$update = $this->HM->update_invitation($decode_post->invitation_id,$data);

                    	if($update)
	                    {  
	                        echo json_encode(array("status" => '1',"message" => 'You friend request rejected successfully !'));
	                    } 
	                    else
	                    {
	                        echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
	                    }
                    }
                    else
                    {
                        echo json_encode(array("status" => '0',"message" => 'The invitation id is not match with our database !'));
                    }
                }
                else
                {
                    echo json_encode(array("status" => '0',"message" => 'The invitation id field is required !'));
                }
            }
            else
            {
                echo json_encode(array("status" => '0',"message" => 'Bad request !'));
            }
 		}
      	else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    }  

	public function FetchInvitation() 
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
  			$fetch_invitation_post = file_get_contents("php://input");
        	$decode_post = json_decode($fetch_invitation_post);
        	$user_details = array();
            
        	if(count((array)$decode_post)==1)
        	{
	            if(!empty($decode_post->friend_id)) 
	            {
	                $this->db->select('*');
	                $this->db->where('u_id',$decode_post->friend_id);
	                $select = $this->db->get('tbl_users');
	                $run = $select->row();
	                $num = $select->num_rows();

	                if($num!=0)
	                {
		           	 	$this->db->select('*');
		           	 	$this->db->where('i_flag','0');
	                	$this->db->where('i_receiver_id',$run->u_id);
	                	$select1 = $this->db->get('tbl_invitation');
	                	$num1 = $select1->num_rows();
	                	$data1 = $select1->result_array();

		            	if($num1!=0)
		            	{
		                	foreach($data1 as $values) 
		                	{
		                		$sender_data = $this->HM->edit_profile($values['i_sender_id']);

		                    	$user_detail=array();
		                    	$user_detail['invitation_id'] = $values['i_id'];
		                    	$user_detail['sender_id'] = $values['i_sender_id'];
		                    	$user_detail['receiver_id'] = $values['i_receiver_id'];
		                    	$user_detail['user_id'] = $sender_data->u_id;
		                    	$user_detail['first_name'] = $sender_data->u_first_name;
		                    	$user_detail['last_name'] = $sender_data->u_last_name;
		                    	$user_detail['email'] = $sender_data->u_email;
		                    	$user_detail['mobile'] = $sender_data->u_mobile;
								$user_detail['company'] = $sender_data->u_company;
								$user_detail['state'] = $sender_data->u_state;
								$user_detail['city'] = $sender_data->u_city;
								$user_detail['street_address'] = $sender_data->u_street_address;
								$user_detail['postalcode'] = $sender_data->u_postalcode;
								$user_detail['sales_position'] = $sender_data->u_sales_position;
								$user_detail['area_cover'] = $sender_data->u_area_cover;
								$user_detail['department'] = $sender_data->u_department;
		                    	$user_detail['message'] = $values['i_message'];

		                    	if(!empty($sender_data->u_image))
								{
									$user_detail['image'] = base_url('public/profile_images/').$sender_data->u_image;
								}
								else
								{
									$user_detail['image'] = '';
								}

								if(!empty($sender_data->u_company_logo))
								{
									$user_detail['company_image'] = base_url('public/profile_images/').$sender_data->u_company_logo;
								}
								else
								{
									$user_detail['company_image'] = '';
								}

		                    	array_push($user_details,$user_detail);
		                	}

		                	if($user_details)
	                        {  
	                            echo json_encode(array("status" => '1',"message" => 'Invitation List !',"invitation_list"=>$user_details));
	                        } 
	                        else
	                        {
	                            echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
	                        }
		                }
		            	else 
		            	{
		            	    echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
		            	} 
		            }
                    else
                    {
                        echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
                    }         
	        	}
	        	else 
	        	{
	           	 	echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
	        	}
	        } 
	        else
	        {
	        	echo json_encode(array("status" => '0',"message" => 'Bad request !'));
	        }
    	} 
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    }

    public function AcceptInvitationlist()
	{
		$this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$accept_invitation_list_post = file_get_contents("php://input");
        	$decode_post = json_decode($accept_invitation_list_post);
        	$accept_list = array();
            
        	if(count((array)$decode_post)==1)
        	{
	            if(!empty($decode_post->user_id)) 
	            {
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$num = $select->num_rows();
					$run = $select->row();
					
					if($num!=0)
					{
						$this->db->select('*');
						$this->db->order_by('i_id','desc');
						$this->db->where('i_flag','1');
						$this->db->where('i_sender_id',$run->u_id);
						$this->db->or_where('i_receiver_id',$run->u_id);
						$select = $this->db->get('tbl_invitation');
						$data = $select->result_array();
						$num1 = $select->num_rows();
						
						if($num1>0)
						{
							foreach($data as $values)
							{		
								$list['id'] = $values['i_id'];
								$list['user_id']  = $values['i_sender_id'];
								$list['friend_id']  = $values['i_receiver_id'];
								$list['flag'] = $values['i_flag'];
								$list['message']  = $values['i_message'];

								if($values['i_sender_id']==$decode_post->user_id)
								{
									$receiver_details = $this->HM->edit_profile($values['i_receiver_id']);

									$list['user_id'] = $receiver_details->u_id;
			                    	$list['first_name'] = $receiver_details->u_first_name;
		                    		$list['last_name'] = $receiver_details->u_last_name;
			                    	$list['email'] = $receiver_details->u_email;
			                    	$list['mobile'] = $receiver_details->u_mobile;
									$list['company'] = $receiver_details->u_company;
									$list['state'] = $receiver_details->u_state;
									$list['city'] = $receiver_details->u_city;
									$list['street_address'] = $receiver_details->u_street_address;
									$list['postalcode'] = $receiver_details->u_postalcode;
									$list['sales_position'] = $receiver_details->u_sales_position;
									$list['area_cover'] = $receiver_details->u_area_cover;
									$list['department'] = $receiver_details->u_department;

									if(!empty($receiver_details->u_company_logo))
									{
										$list['receiver_company_logo'] = base_url('public/profile_images/').$receiver_details->u_company_logo;
									}
									else
									{
										$list['receiver_company_logo'] = '';
									}

									if(!empty($receiver_details->u_image))
									{
										$list['image'] = base_url('public/profile_images/').$receiver_details->u_image;
									}
									else
									{
										$list['image'] = '';
									}
								}

								if($values['i_receiver_id']==$decode_post->user_id)
								{
									$sender_details = $this->HM->edit_profile($values['i_sender_id']);

									$list['user_id'] = $sender_details->u_id;
			                    	$list['first_name'] = $sender_details->u_first_name;
		                    		$list['last_name'] = $sender_details->u_last_name;
			                    	$list['email'] = $sender_details->u_email;
			                    	$list['mobile'] = $sender_details->u_mobile;
									$list['company'] = $sender_details->u_company;
									$list['state'] = $sender_details->u_state;
									$list['city'] = $sender_details->u_city;
									$list['street_address'] = $sender_details->u_street_address;
									$list['postalcode'] = $sender_details->u_postalcode;
									$list['sales_position'] = $sender_details->u_sales_position;
									$list['area_cover'] = $sender_details->u_area_cover;
									$list['department'] = $sender_details->u_department;

									if(!empty($sender_details->u_company_logo))
									{
										$list['sender_company_logo'] = base_url('public/profile_images/').$sender_details->u_company_logo;
									}
									else
									{
										$list['sender_company_logo'] = '';
									}

									if(!empty($sender_details->u_image))
									{
										$list['image'] = base_url('public/profile_images/').$sender_details->u_image;
									}
									else
									{
										$list['image'] = '';
									}
								}

								array_push($accept_list,$list);	
							}

							if($accept_list)
							{  
	                            echo json_encode(array("status" => '1',"message" => 'Accept Invitation List !',"invitation_list"=>$accept_list));
	                        } 
	                        else
	                        {
	                            echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
	                        }
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else 
	        	{
	           	 	echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
	        	}
	        } 
	        else
	        {
	        	echo json_encode(array("status" => '0',"message" => 'Bad request !'));
	        }
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	} 

    public function Rejectinvitationlist()
	{
		$this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$reject_invitation_list_post = file_get_contents("php://input");
        	$decode_post = json_decode($reject_invitation_list_post);
        	$reject_list = array();
            
        	if(count((array)$decode_post)==1)
        	{
	            if(!empty($decode_post->user_id)) 
	            {
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$num = $select->num_rows();
					$run = $select->row();
					
					if($num!=0)
					{
						$this->db->select('*');
						$this->db->order_by('i_id','desc');
						$this->db->where('i_flag','2');
						$this->db->where('i_sender_id',$run->u_id);
						$this->db->or_where('i_receiver_id',$run->u_id);
						$select = $this->db->get('tbl_invitation');
						$data = $select->result_array();
						$num1 = $select->num_rows();

						if($num1>0)
						{ 
							foreach($data as $values)
							{			
								if($values['i_flag']=='2')
								{
									$list['id'] = $values['i_id'];
									$list['user_id']  = $values['i_sender_id'];
									$list['friend_id']  = $values['i_receiver_id'];
									$list['flag'] = $values['i_flag'];
									$list['message']  = $values['i_message'];

									if($values['i_sender_id']==$decode_post->user_id)
									{
										$receiver_details = $this->HM->edit_profile($values['i_receiver_id']);

										$list['user_id'] = $receiver_details->u_id;
										$list['first_name'] = $receiver_details->u_first_name;
		                    			$list['last_name'] = $receiver_details->u_last_name;
				                    	$list['email'] = $receiver_details->u_email;
				                    	$list['mobile'] = $receiver_details->u_mobile;
										$list['company'] = $receiver_details->u_company;
										$list['state'] = $receiver_details->u_state;
										$list['city'] = $receiver_details->u_city;
										$list['street_address'] = $receiver_details->u_street_address;
										$list['postalcode'] = $receiver_details->u_postalcode;
										$list['sales_position'] = $receiver_details->u_sales_position;
										$list['area_cover'] = $receiver_details->u_area_cover;
										$list['department'] = $receiver_details->u_department;

										if(!empty($receiver_details->u_company_logo))
										{
											$list['receiver_company_logo'] = base_url('public/profile_images/').$receiver_details->u_company_logo;
										}
										else
										{
											$list['receiver_company_logo'] = '';
										}

										if(!empty($receiver_details->u_image))
										{
											$list['image'] = base_url('public/profile_images/').$receiver_details->u_image;
										}
										else
										{
											$list['image'] = '';
										}
									}

									if($values['i_receiver_id']==$decode_post->user_id)
									{
										$sender_details = $this->HM->edit_profile($values['i_sender_id']);

										$list['user_id'] = $sender_details->u_id;
				                    	$list['first_name'] = $sender_details->u_first_name;
		                    			$list['last_name'] = $sender_details->u_last_name;
				                    	$list['email'] = $sender_details->u_email;
				                    	$list['mobile'] = $sender_details->u_mobile;
										$list['company'] = $sender_details->u_company;
										$list['state'] = $sender_details->u_state;
										$list['city'] = $sender_details->u_city;
										$list['street_address'] = $sender_details->u_street_address;
										$list['postalcode'] = $sender_details->u_postalcode;
										$list['sales_position'] = $sender_details->u_sales_position;
										$list['area_cover'] = $sender_details->u_area_cover;
										$list['department'] = $sender_details->u_department;

										if(!empty($sender_details->u_company_logo))
										{
											$list['sender_company_logo'] = base_url('public/profile_images/').$sender_details->u_company_logo;
										}
										else
										{
											$list['sender_company_logo'] = '';
										}

										if(!empty($sender_details->u_image))
										{
											$list['image'] = base_url('public/profile_images/').$sender_details->u_image;
										}
										else
										{
											$list['image'] = '';
										}
									}
										
									array_push($reject_list,$list);	
								}
							}

							if($reject_list)
							{  
	                            echo json_encode(array("status" => '1',"message" => 'Reject Invitation List !',"invitation_list"=>$reject_list));
	                        } 
	                        else
	                        {
	                            echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
	                        }
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else 
	        	{
	           	 	echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
	        	}
	        } 
	        else
	        {
	        	echo json_encode(array("status" => '0',"message" => 'Bad request !'));
	        }
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function DeleteInvitation()
	{
		$authentication = $this->verify_token();
  		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
	 		$delete_invitation_post = file_get_contents("php://input");
            $decode_post = json_decode($delete_invitation_post);

            if(count((array)$decode_post)==1)
            {
                if(!empty($decode_post->invitation_id)) 
                {	
                	$this->db->select('*');
                    $this->db->where('i_id',$decode_post->invitation_id);
                    $select = $this->db->get('tbl_invitation');
                    $num = $select->num_rows();

                    if($num==1)
                    {
                    	$data=array();
                    	$data['i_flag'] = '0';

                    	$update = $this->HM->update_invitation($invitation_id,$data);

                    	if($update)
	                    {  
	                        echo json_encode(array("status" => '1',"message" => 'You friend request deleted successfully !'));
	                    } 
	                    else
	                    {
	                        echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
	                    }
                    }
                    else
                    {
                        echo json_encode(array("status" => '0',"message" => 'The invitation id is not match with our database !'));
                    }
                }
                else
                {
                    echo json_encode(array("status" => '0',"message" => 'The invitation id field is required !'));
                }
            }
            else
            {
                echo json_encode(array("status" => '0',"message" => 'Bad request !'));
            }
 		}
      	else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
	} 

 	public function addNotes() 
    {
  	    $authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
            $notes_post = file_get_contents("php://input");
            $decode_post = json_decode($notes_post);
            $date = date('Y-m-d h:i:s');

            if(count((array)$decode_post)==3)
            {
                if(!empty($decode_post->user_id) && !empty($decode_post->heading) && !empty($decode_post->notes)) 
                {
                    $this->db->select('*');
                    $this->db->where('u_id',$decode_post->user_id);
                    $select = $this->db->get('tbl_users');
                    $user_run = $select->row();
                    $num = $select->num_rows();

                    if($num==1)
                    {
	                    $data = array();
	                    $data['n_user_id'] = $user_run->u_id;
                        $data['n_heading'] = $decode_post->heading;
                        $data['n_note'] = $decode_post->notes;
                        $data['n_flag'] = '0';

	                    $insert_id = $this->HM->add_notes($data);

	                    if($insert_id)
	                    {  
	                        echo json_encode(array("status" => '1',"message" => 'Your note added successfully !',"notes_id"=>(string)$insert_id));
	                    } 
	                    else
	                    {
	                        echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
	                    }
                    }
                    else
                    {
                        echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
                    }
                }
                else
                {
                    echo json_encode(array("status" => '0',"message" => 'The user id, notes, heading fields are required !'));
                }
            }
            else
            {
                echo json_encode(array("status" => '0',"message" => 'Bad request !'));
            }
        }
        else
        {
            echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
        }  
    }

    public function FetchAllNotes()
    {
    	$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
  			$notes_post = file_get_contents("php://input");
        	$decode_post = json_decode($notes_post);
        	$notes_list = array();
            
        	if(count((array)$decode_post)==1)
        	{
	            if(!empty($decode_post->user_id)) 
	            {
	                $this->db->select('*');
	                $this->db->where('u_id',$decode_post->user_id);
	                $select = $this->db->get('tbl_users');
	                $run = $select->row();
	                $num = $select->num_rows();

	                if($num!=0)
	                {
	                	$manage_notes = $this->HM->manage_notes($run->u_id);

		                foreach($manage_notes as $values) 
		                {
		                    $notes=array();
		                    $notes['notes_id'] = $values['n_id'];
		                    $notes['notes'] = $values['n_note'];
		                    $notes['heading'] = $values['n_heading'];
		                    array_push($notes_list,$notes);
		                }

		                if($notes_list)
	                    {  
	                        echo json_encode(array("status" => '1',"message" => 'Notes List !',"notes_list"=>$notes_list));
	                    } 
	                    else
	                    {
	                        echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
	                    }
		            }
                    else
                    {
                        echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
                    }         
	        	}
	        	else 
	        	{
	           	 	echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
	        	}
	        } 
	        else
	        {
	        	echo json_encode(array("status" => '0',"message" => 'Bad request !'));
	        }
    	} 
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    }

    public function EditNotes()
    {
    	$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
  			$notes_post = file_get_contents("php://input");
        	$decode_post = json_decode($notes_post);
        	$notes_list = array();
            
        	if(count((array)$decode_post)==2)
        	{
	            if(!empty($decode_post->user_id) && !empty($decode_post->notes_id)) 
	            {
	                $this->db->select('*');
	                $this->db->where('u_id',$decode_post->user_id);
	                $select = $this->db->get('tbl_users');
	                $run = $select->row();
	                $num = $select->num_rows();

	                if($num!=0)
	                {
	                	$edit_notes = $this->HM->edit_notes($decode_post->notes_id);

		                $notes=array();
		                $notes['notes_id'] = $edit_notes->n_id;
		                $notes['notes'] = $edit_notes->n_note;
		                $notes['heading'] = $edit_notes->n_heading;
		                array_push($notes_list,$notes);

		                if($notes_list)
	                    {  
	                        echo json_encode(array("status" => '1',"message" => 'Edit Note !',"edit_note"=>$notes_list));
	                    } 
	                    else
	                    {
	                        echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
	                    }
		            }
                    else
                    {
                        echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
                    }         
	        	}
	        	else 
	        	{
	           	 	echo json_encode(array("status" => '0',"message" => 'The user id and notes id fields are required !'));
	        	}
	        } 
	        else
	        {
	        	echo json_encode(array("status" => '0',"message" => 'Bad request !'));
	        }
    	} 
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    }

    public function UpdateNotes() 
    {
  	    $authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
            $notes_post = file_get_contents("php://input");
            $decode_post = json_decode($notes_post);
            $date = date('Y-m-d h:i:s');

            if(count((array)$decode_post)==4)
            {
                if(!empty($decode_post->user_id) && !empty($decode_post->heading) && !empty($decode_post->notes) && !empty($decode_post->notes_id)) 
                {
                    $this->db->select('*');
                    $this->db->where('u_id',$decode_post->user_id);
                    $select = $this->db->get('tbl_users');
                    $user_run = $select->row();
                    $num = $select->num_rows();

                    if($num==1)
                    {
	                    $data = array();
	                    $data['n_user_id'] = $user_run->u_id;
                        $data['n_heading'] = $decode_post->heading;
                        $data['n_note'] = $decode_post->notes;
                        $data['n_flag'] = '0';

	                    $update = $this->HM->update_notes($decode_post->user_id,$decode_post->notes_id,$data);

	                    if($update)
	                    {  
	                        echo json_encode(array("status" => '1',"message" => 'You notes updated successfully !'));
	                    } 
	                    else
	                    {
	                        echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
	                    }
                    }
                    else
                    {
                        echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
                    }
                }
                else
                {
                    echo json_encode(array("status" => '0',"message" => 'The user id, notes, heading fields are required !'));
                }
            }
            else
            {
                echo json_encode(array("status" => '0',"message" => 'Bad request !'));
            }
        }
        else
        {
            echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
        }  
    }

    public function DeleteNotes() 
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$notes_post = file_get_contents("php://input");
			$decode_post = json_decode($notes_post);
			$notes_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->notes_id) && !empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$this->db->select('*');
						$this->db->where('n_id',$decode_post->notes_id);
						$this->db->where('n_flag','0');
						$select = $this->db->get('tbl_notes');
						$num = $select->num_rows();

						if($num>0)
						{
							$data=array();
							$data['n_flag'] = 1;

							$update = $this->HM->update_notes($decode_post->user_id,$decode_post->notes_id,$data);

							if($update)
							{  
								echo json_encode(array("status" => '1',"message" => 'Notes deleted successfully'));
							} 
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id and notes id fields are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    }

    public function AddDocuments() 
	{
        $authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$user_id = strip_tags($this->input->post('user_id'));
	 		$heading = strip_tags($this->input->post('heading'));
	 		$image = strip_tags($this->input->post('image'));
	 		$upload='';

			if(!empty($user_id) && !empty($heading))
			{
        		$this->db->select('*');
				$this->db->where('u_id',$user_id);
				$select = $this->db->get('tbl_users');
				$num = $select->num_rows();
				$run = $select->row();

				if($num>0)
				{
					$config['upload_path']          = './public/doc_files';
					$config['allowed_types']        = 'gif|jpg|png|jpeg|pdf|docx|xls';
					$config['max_size']             = "";
					$config['max_width']            = "";
					$config['max_height']           = "";

					$this->load->library('upload',$config); 

					if($this->upload->do_upload('image'))
					{
						$image = $this->upload->data();
						$upload = $image['file_name'];
					}
					else
					{
						$error = array('error' => $this->upload->display_errors());
						echo json_encode(array("status" => '0',"message" => 'Image should be gif, jpg, png, jpeg. pdf, docx format !',"error"=>$error));
						exit();
					}
								
					$data=array();
					$data['d_heading'] = $heading;
					$data['d_files'] = $upload;
					$data['d_status'] = 0;
					$data['d_user_id'] = $run->u_id;

					$insert_id = $this->HM->add_document($data);

					if($insert_id)
					{  
						echo json_encode(array("status" => '1',"message" => 'Your document uploaded successfully !',"document_id"=>$insert_id));
					} 
					else
					{
						echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'The User id and heading field is required !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    }

    public function FetchDocuments() 
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$document_post = file_get_contents("php://input");
			$decode_post = json_decode($document_post);
			$document_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$this->db->select('*');
						$this->db->where('d_user_id',$run->u_id);
						$this->db->where('d_status','0');
						$select = $this->db->get('tbl_documents');
						$document = $select->result_array();
						$num = $select->num_rows();

						if($num>0)
						{
							foreach($document as $values)
							{
								$document=array();
								$document['id'] = $values['d_id'];
								$document['heading'] = $values['d_heading'];
								$document['image'] = base_url('public/doc_files/').$values['d_files'];
								array_push($document_list,$document);
							}

							if($document_list)
							{  
								echo json_encode(array("status" => '1',"message" => 'Document List',"document_list"=>$document_list));
							} 
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    } 

    public function FetchDocumentInfo()
    {
     	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$document_post = file_get_contents("php://input");
			$decode_post = json_decode($document_post);
			$document_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->document_id) && !empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$this->db->select('*');
						$this->db->where('d_id',$decode_post->document_id);
						$this->db->where('d_status','0');
						$select = $this->db->get('tbl_documents');
						$document_data = $select->row();
						$num = $select->num_rows();

						if($num>0)
						{
							$document=array();
							$document['id'] = $document_data->d_id;
							$document['heading'] = $document_data->d_heading;
							$document['image'] = base_url('public/doc_files/').$document_data->d_files;
							array_push($document_list,$document);

							if($document_list)
							{  
								echo json_encode(array("status" => '1',"message" => 'Document Detail',"document_detail"=>$document_list));
							} 
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id and document id fields are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    } 
 	
 	public function DeleteDocument() 
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$document_post = file_get_contents("php://input");
			$decode_post = json_decode($document_post);
			$document_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->document_id) && !empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$this->db->select('*');
						$this->db->where('d_id',$decode_post->document_id);
						$this->db->where('d_status','0');
						$select = $this->db->get('tbl_documents');
						$document_data = $select->row();
						$num = $select->num_rows();

						if($num>0)
						{
							$data=array();
							$data['d_status'] = 1;

							$update = $this->HM->update_document($decode_post->document_id,$data);

							if($update)
							{  
								echo json_encode(array("status" => '1',"message" => 'Document deleted successfully'));
							} 
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id and document id fields are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    }

    public function FetchMyProduct() 
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$product_post = file_get_contents("php://input");
			$decode_post = json_decode($product_post);
			$product_list = array();

			if(count((array)$decode_post)==8)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->category_id) && !empty($decode_post->rep_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
					
					if($user_num!=0)
					{
						$rep_num = $this->HM->check_rep_id($decode_post->rep_id);

						if($rep_num!=0)
						{
							$cat_num = $this->HM->check_category_id($decode_post->category_id);

							if($cat_num)
							{
								if($decode_post->price_sort=='low')
								{
									$this->db->select('*');
									$this->db->order_by('p_price','asc');
									$this->db->where('p_status','0');
									$this->db->where('p_category',$decode_post->category_id);
									$this->db->where('p_user_id',$decode_post->rep_id);
									$select = $this->db->get('tbl_product');
									$data = $select->result_array();
									$num = $select->num_rows();
								}
								elseif($decode_post->price_sort=='high')
								{
									$this->db->select('*');
									$this->db->order_by('p_price','desc');
									$this->db->where('p_status','0');
									$this->db->where('p_category',$decode_post->category_id);
									$this->db->where('p_user_id',$decode_post->rep_id);
									$select = $this->db->get('tbl_product');
									$data = $select->result_array();
									$num = $select->num_rows();
								}
								elseif($decode_post->price_low!='' && $decode_post->price_high!='')
								{
									$this->db->select('*');
									$this->db->where('p_status','0');
									$this->db->where('p_price >=', $decode_post->price_low);
									$this->db->where('p_price <=', $decode_post->price_high);
									$this->db->where('p_category',$decode_post->category_id);
									$this->db->where('p_user_id',$decode_post->rep_id);
									$select = $this->db->get('tbl_product');
									$data = $select->result_array();
									$num = $select->num_rows();
								}
								elseif($decode_post->keyword)
								{
									$this->db->select('*');
									$this->db->where('p_status','0');
									$this->db->where('p_category',$decode_post->category_id);
									$this->db->where('p_user_id',$decode_post->rep_id);
									$this->db->like('p_name', $decode_post->keyword);
									$select = $this->db->get('tbl_product');
									$data = $select->result_array();
									$num = $select->num_rows();
								}
								elseif($decode_post->rating)
								{
									$this->db->select('*');
									$this->db->where('p_status','0');
									$this->db->where('p_review', $decode_post->rating);
									$this->db->where('p_category',$decode_post->category_id);
									$this->db->where('p_user_id',$decode_post->rep_id);
									$select = $this->db->get('tbl_product');
									$data = $select->result_array();
									$num = $select->num_rows();
								}
								else
								{
									$this->db->select('*');
									$this->db->where('p_status','0');
									$this->db->where('p_category',$decode_post->category_id);
									$this->db->where('p_user_id',$decode_post->rep_id);
									$select = $this->db->get('tbl_product');
									$data = $select->result_array();
									$num = $select->num_rows();
								}
								
								if($num>0)
								{
									foreach($data as $values)
									{
										$product=array();
										$product['id'] = $values['p_id'];
										$product['name'] = $values['p_name'];
										$product['price'] = $values['p_price'];
										$product['stock'] = $values['p_stock'];
										$product['packsize'] = $values['p_packsize'];
										$product['description'] = $values['p_description'];
										$product['image'] = base_url('public/product_image/').$values['p_image'];
										$product['review'] = $values['p_review'];

										$profile = $this->HM->edit_profile($values['p_user_id']);
										$user_num = $this->HM->check_rep_id($values['p_user_id']);

										if($user_num!=0)
										{
											$product['reps_id'] = $profile->u_id;
											$product['reps_name'] = $profile->u_first_name;
											$product['reps_company_name'] = $profile->u_company;
											$product['reps_company_logo'] = base_url('public/profile_images/').$profile->u_company_logo;
										}
										else
										{
											$product['reps_id'] = '';
											$product['reps_name'] = '';
											$product['reps_company_name'] = '';
											$product['reps_company_logo'] = '';
										}

										$this->db->where('c_user_id',$decode_post->user_id);
										$this->db->where('c_product_id',$values['p_id']);
										$cart_select = $this->db->get('tbl_cart');
										$cart_num = $cart_select->num_rows();
										$cart_data = $cart_select->row();

										if($cart_num!=0)
										{
											$product['cart_qty'] = $cart_data->c_qty;
											$product['cart_id'] = $cart_data->c_id;
										}
										else
										{
											$product['cart_qty'] = '';
											$product['cart_id'] = '';
										}

										$this->db->where('w_u_id',$decode_post->user_id);
										$this->db->where('w_p_id',$values['p_id']);
										$wishlist_select = $this->db->get('tbl_wishlist');
										$wishlist_num = $wishlist_select->num_rows();
										$wishlist_data = $wishlist_select->row();

										if($wishlist_num!=0)
										{
											$product['wishlist_qty'] = '1';
											$product['wishlist_id'] = $wishlist_data->w_id;
										}
										else
										{
											$product['wishlist_qty'] = '';
											$product['wishlist_id'] = '';
										}

										array_push($product_list,$product);
										
									}

									if($product_list)
									{  
										echo json_encode(array("status" => '1',"message" => 'Product List',"product_list"=>$product_list));
									} 
									else
									{
										echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
									}
								}
								else
								{
									echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
								}
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'The category id is not match with our database !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'The rep id is not match with our database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id, rep id and category field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    } 

    public function FetchProductInfo()
    {
     	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$product_post = file_get_contents("php://input");
			$decode_post = json_decode($product_post);
			$product_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->product_id) && !empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$this->db->select('*');
						$this->db->where('p_id',$decode_post->product_id);
						$this->db->where('p_status','0');
						$select = $this->db->get('tbl_product');
						$product_data = $select->row();
						$num = $select->num_rows();

						if($num!=0)
						{
							$product=array();
							$product['id'] = $product_data->p_id;
							$product['name'] = $product_data->p_name;
							$product['price'] = $product_data->p_price;
							$product['stock'] = $product_data->p_stock;
							$product['packsize'] = $product_data->p_packsize;
							$product['description'] = $product_data->p_description;

							$product_image = str_replace("http://www.alexptech.com/replist/uploads/product_image/", "", $product_data->p_image);
							$product['image'] = base_url('public/product_image/').$product_image;

							$profile = $this->HM->edit_profile($product_data->p_user_id);

							$product['reps_id'] = $profile->u_id;
							$product['reps_name'] = $profile->u_first_name;
							$product['reps_company_name'] = $profile->u_company;

							$company_logo = str_replace("http://www.alexptech.com/replist/uploads/profile_images/", "", $profile->u_company_logo);
							$product['reps_company_logo'] = base_url('public/profile_images/').$company_logo;

							$this->db->where('c_product_id',$product_data->p_id);
							$cart_select = $this->db->get('tbl_cart');
							$cart_num = $cart_select->num_rows();
							$cart_data = $cart_select->row();

							if($cart_num!=0)
							{
								$product['cart_qty'] = $cart_data->c_qty;
								$product['cart_id'] = $cart_data->c_id;
							}
							else
							{
								$product['cart_qty'] = '';
								$product['cart_id'] = '';
							}

							$this->db->where('w_p_id',$product_data->p_id);
							$wishlist_select = $this->db->get('tbl_wishlist');
							$wishlist_num = $wishlist_select->num_rows();
							$wishlist_data = $wishlist_select->row();

							if($wishlist_num!=0)
							{
								$product['wishlist_qty'] = '1';
								$product['wishlist_id'] = $wishlist_data->w_id;
							}
							else
							{
								$product['wishlist_qty'] = '';
								$product['wishlist_id'] = '';
							}

							array_push($product_list,$product);

							if($product_list)
							{  
								echo json_encode(array("status" => '1',"message" => 'Product Detail',"product_detail"=>$product_list));
							} 
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'The product id is not match with our database ! !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id and product id fields are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    } 

	public function AddToCart() 
	{
     	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$cart_post = file_get_contents("php://input");
			$decode_post = json_decode($cart_post);

			if(count((array)$decode_post)==5)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->rep_id) && !empty($decode_post->product_id) && !empty($decode_post->price) && !empty($decode_post->quantity))
				{ 
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$rep_num = $this->HM->check_rep_id($decode_post->rep_id);

						if($rep_num!=0)
						{
							$product_num = $this->HM->check_product_id($decode_post->product_id);

							if($product_num!=0)
							{
								$cart_num = $this->HM->check_cart_num($decode_post->user_id); 

								if($cart_num!=0)
								{
									$cart_exist = $this->HM->check_cart_exist($decode_post->rep_id,$decode_post->user_id);

									if($cart_exist>0)
									{
										$data = array();
										$data['c_product_id'] = $decode_post->product_id;
										$data['c_qty'] = $decode_post->quantity;
										$data['c_rep_id'] = $decode_post->rep_id;
										$data['c_user_id'] = $decode_post->user_id;
										$data['c_price'] = $decode_post->price;
										$data['c_created_at'] = date('Y-m-d h:i:s');

										$cart = $this->HM->add_to_cart($data);

										if($cart)
										{
											echo json_encode(array("status" => '1',"message"=>'Item added into cart successfully !'));
										}
										else
										{
											echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
										}
									}
									else
									{
										echo json_encode(array("status" => '0',"message" => 'Your cart contains item, Please remove items from old rep !'));
									}
								}
								else
								{
									$data = array();
									$data['c_product_id'] = $decode_post->product_id;
									$data['c_qty'] = $decode_post->quantity;
									$data['c_rep_id'] = $decode_post->rep_id;
									$data['c_user_id'] = $decode_post->user_id;
									$data['c_price'] = $decode_post->price;
									$data['c_created_at'] = date('Y-m-d h:i:s');

									$cart = $this->HM->add_to_cart($data);

									if($cart)
									{
										echo json_encode(array("status" => '1',"message"=>'Item added into cart successfully !'));
									}
									else
									{
										echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
									}
								}
							}
							else
							{
								echo json_encode(array("status" => '0',"message"=>'The product id is not match with our database '));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'The rep id is not match with our database '));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					} 
				}     
  				else
				{
					echo json_encode(array("status" => '0',"message"=>'The user id, rep id, product id, price and quantity are required fields '));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    }

    public function replace_cart_item()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$add_cart_post = file_get_contents("php://input");
			$decode_post = json_decode($add_cart_post);

			if(count((array)$decode_post)==5)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->rep_id) && !empty($decode_post->product_id) && !empty($decode_post->price) && !empty($decode_post->quantity))
				{ 		 
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{			
						$product_num = $this->HM->check_product_id($decode_post->product_id);
						$cart_exist = $this->HM->check_cart_value_exist($decode_post->product_id,$decode_post->user_id);

						if($product_num!=0)
						{
							$cart_num = $this->HM->check_cart_num($decode_post->user_id); 

							if($cart_num!=0)
							{		
								if($cart_exist==0)
								{
									$this->db->order_by('c_id','desc');
						            $this->db->where('c_user_id',$decode_post->user_id);
						            $select = $this->db->get('tbl_cart');
						            $cart_data = $select->result_array();

						            foreach($cart_data as $values)
						            {
										$this->db->where('c_user_id',$decode_post->user_id);
										$this->db->delete('tbl_cart');
									}

									$data['c_product_id'] = $decode_post->product_id;
									$data['c_qty'] = $decode_post->quantity;
									$data['c_rep_id'] = $decode_post->rep_id;
									$data['c_user_id'] = $decode_post->user_id;
									$data['c_price'] = $decode_post->price;
									$data['c_created_at'] = date('Y-m-d h:i:s');

									$cart = $this->HM->add_to_cart($data);

									if($cart)
									{
										echo json_encode(array("status" => '1',"message"=>'Item added into cart successfully !'));
									}
									else
									{
										echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
									}
								}
								else
								{
									echo json_encode(array("status" => '0',"message"=>'Item already exist in your cart !'));
								}
							}
							else
							{
								if($cart_exist==0)
								{
									$this->db->order_by('c_id','desc');
						            $this->db->where('c_user_id',$decode_post->user_id);
						            $select = $this->db->get('tbl_cart');
						            $cart_data = $select->result_array();

						            foreach($cart_data as $values)
						            {
										$this->db->where('c_user_id',$decode_post->user_id);
										$this->db->delete('tbl_cart');
									}

									$data['c_product_id'] = $decode_post->product_id;
									$data['c_qty'] = $decode_post->quantity;
									$data['c_rep_id'] = $decode_post->rep_id;
									$data['c_user_id'] = $decode_post->user_id;
									$data['c_price'] = $decode_post->price;
									$data['c_created_at'] = date('Y-m-d h:i:s');

									$cart = $this->HM->add_to_cart($data);

									if($cart)
									{
										echo json_encode(array("status" => '1',"message"=>'Item added into cart successfully !'));
									}
									else
									{
										echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
									}
								}
								else
								{
									echo json_encode(array("status" => '0',"message"=>'Item already added into cart !'));
								}
							}		
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'The product id is not match with our database '));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}		
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id, product id, rep id, quantity and price fields are required!'));
				}
					
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

    public function cart_count() 
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$cart_count_post = file_get_contents("php://input");
			$decode_post = json_decode($cart_count_post);

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num>0)
					{
						$num = $this->HM->cart_count($run->u_id);

						if($num)
						{
							echo json_encode(array("status" => '1',"message" => 'Cart count !',"cart_count"=>$num));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'Cart count !',"cart_count"=>$num));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id is field required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

    public function update_cart()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$update_cart_post = file_get_contents("php://input");
			$decode_post = json_decode($update_cart_post);

			if(count((array)$decode_post)==3)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->cart_id) && !empty($decode_post->qty))
				{	
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num>0)
					{
						$data=array();
						$data['c_qty'] = $decode_post->qty;

						$update = $this->HM->update_cart($run->u_id,$decode_post->cart_id,$data);

						if($update)
						{
							echo json_encode(array("status" => '1',"message" => 'Cart updated successfully !'));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id, cart id and quantity fields are required!'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	} 
    
    public function DeleteFromCart() 
    {
        $authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$delete_cart_post = file_get_contents("php://input");
			$decode_post = json_decode($delete_cart_post);

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->cart_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$this->db->select('*');
						$this->db->where('c_id',$decode_post->cart_id);
						$select = $this->db->get('tbl_cart');
						$num = $select->num_rows();

						if($num!=0)
						{
							$delete = $this->HM->delete_cart($decode_post->cart_id);

							if($delete)
							{
								echo json_encode(array("status" => '1',"message" => 'Item deleted from cart successfully !'));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'The cart id is not match with our database '));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message"=>'The user id and cart id are required fields '));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    } 
    
    public function FetchCart()  
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$cart_post = file_get_contents("php://input");
			$decode_post = json_decode($cart_post);
			$cart_list = array(); $total_price = 0;

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{ 
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$this->db->where('c_user_id',$decode_post->user_id);
						$select = $this->db->get('tbl_cart');
						$num = $select->num_rows();
						$cart = $select->result_array();

						// Total Reward Points
						$points = $this->HM->total_reward_points($decode_post->user_id);

						if(!empty($points->rp_total_points))
						{
							$point = $points->rp_total_points;
						}
						else
						{
							$point = 0;
						}

						foreach($cart as $values)
						{
							$profile = $this->HM->edit_profile($values['c_rep_id']);
							$product_detail = $this->HM->edit_product($values['c_product_id']);

							$final_price = $values['c_price']*$values['c_qty'];
					        $total_price+=$final_price;

					        $is_reward_points_applied = $values['c_is_reward_applied'];
							$is_coupon_applied = $values['c_is_coupon_applied'];

							if($values['c_discount_value']=='')
							{
								$total_discount = '0';
							}
							else
							{
							    $total_discount = $values['c_discount_value'];
							}

							if($values['c_reward_points']=='')
							{
								$total_reward_points = '0';
							}
							else
							{
							    $total_reward_points = $values['c_reward_points'];
							}

							$grand_total = $total_price-$total_discount;

							$cart_item['cart_id'] = $values['c_id'];
							$cart_item['qty'] = $values['c_qty'];
							$cart_item['sub_total'] = (string)$final_price;
							$cart_item['product_id'] = $product_detail->p_id;
							$cart_item['product_name'] = $product_detail->p_name;
							$cart_item['product_price'] = $product_detail->p_price;
							$cart_item['product_stock'] = $product_detail->p_stock;
							$cart_item['product_packsize'] = $product_detail->p_packsize;
							$cart_item['product_description'] = $product_detail->p_description;
							$cart_item['product_image'] = base_url('public/product_image/').$product_detail->p_image;
							$cart_item['reps_id'] = $profile->u_id;
							$cart_item['reps_name'] = $profile->u_first_name;
							$cart_item['reps_company_name'] = $profile->u_company;
							$cart_item['reps_company_logo'] = base_url('public/profile_images/').$profile->u_company_logo;

							array_push($cart_list,$cart_item);
						}

						if($cart_list)
						{
							echo json_encode(array("status" => '1',"message"=>'Cart List !',"cart_list"=>$cart_list,"subtotal"=>(string)$total_price,"applied_total_discount"=>(string)$total_discount,"applied_reward_points"=>(string)$total_reward_points,"is_coupon_applied"=>$is_coupon_applied,"is_reward_points_applied"=>$is_reward_points_applied,"grand_total"=>(string)$grand_total,"total_reward_points"=>$point));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found on our database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}   
				}
  				else
				{
					echo json_encode(array("status" => '0',"message"=>'The user id is required field'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    } 

    public function add_to_wishlist()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$add_wishlist_post = file_get_contents("php://input");
			$decode_post = json_decode($add_wishlist_post);

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->product_id) && !empty($decode_post->user_id)) 
				{
					$this->db->select("*");
					$this->db->where('p_id',$decode_post->product_id);
					$select = $this->db->get('tbl_product');
					$product_run = $select->row();
					$product_num = $select->num_rows();

					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$user_num = $select->num_rows();

					$this->db->select("*");
					$this->db->where('w_u_id',$run->u_id);
					$this->db->where('w_p_id',$product_run->p_id);
					$select = $this->db->get('tbl_wishlist');
					$wishlist_num = $select->num_rows();
					
	 				if($product_num>0)
	 				{
	 					if($user_num>0)
	 					{
	 						if($wishlist_num==0)
	 						{
				 				$data = array();
				 				$data['w_p_id'] = $decode_post->product_id;
				 				$data['w_u_id'] = $run->u_id;
				 				$data['w_created_at'] = date('Y-m-d h:i:s');
				 			
				 				$insert = $this->HM->add_to_wishlist($data);

				 				if($insert)
								{
								 	echo json_encode(array("status" => 'success',"message" => 'Item Added Successfully !'));
								}
								else
								{
							 		echo json_encode(array("status" => 'failed',"message" => 'Please try again !'));
							 	}
							}
							else
							{
							 	echo json_encode(array("status" => 'failed',"message" => 'Item already exist in your wishlist !'));
							}
	 					}
	 					else
			 			{
			 				echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with our database !'));
			 			}
	 				}
	 				else
			 		{
			 			echo json_encode(array("status" => 'failed',"message" => 'The product id is not match with our database !'));
			 		}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id and product id fields are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function wishlist_count()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$wishlist_count_post = file_get_contents("php://input");
			$decode_post = json_decode($wishlist_count_post);

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$user_num = $select->num_rows();

					if($user_num)
					{
						$this->db->select('*');
						$this->db->where('w_u_id',$run->u_id);
						$select = $this->db->get('tbl_wishlist');
						$wishlist_num = $select->num_rows();

						if($wishlist_num)
						{
							echo json_encode(array("status" => '1',"message" => 'Wishlist count !',"wishlist_count"=>$wishlist_num));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'Wishlist count !',"wishlist_count"=>$wishlist_num));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id is field required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}
 
	public function wishlist_list()
	{ 
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$wishlist_list_post = file_get_contents("php://input");
			$decode_post = json_decode($wishlist_list_post);
			$wishlist_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num>0)
					{
						$num = $this->HM->wishlist_count($run->u_id);

						if($num>0)
						{
							$wishlist = $this->HM->manage_wishlist($run->u_id);

							foreach($wishlist as $values)
							{
								$product = $this->HM->edit_product($values['w_p_id']);
								$rep_num = $this->HM->check_rep_id($product->p_user_id);

								if($rep_num!=0)
								{
									$profile = $this->HM->edit_profile($product->p_user_id);

									$wishlist_item['reps_id'] = $profile->u_id;
									$wishlist_item['reps_name'] = $profile->u_first_name;
									$wishlist_item['reps_company_name'] = $profile->u_company;
									$wishlist_item['reps_company_logo'] = base_url('public/profile_images/').$profile->u_company_logo;
								}
								else
								{
									$wishlist_item['reps_id'] = '';
									$wishlist_item['reps_name'] = '';
									$wishlist_item['reps_company_name'] = '';
									$wishlist_item['reps_company_logo'] = '';
								}

								$wishlist_item['wishlist_id'] = $values['w_id'];
								$wishlist_item['product_id'] = $product->p_id;
								$wishlist_item['product_title'] = $product->p_name;
								$wishlist_item['product_price'] = $product->p_price;
								$wishlist_item['product_stock'] = $product->p_stock;
								$wishlist_item['product_packsize'] = $product->p_packsize;
								$wishlist_item['product_description'] = $product->p_description;

								if(!empty($product->p_image))
								{
									$wishlist_item['product_image'] = base_url().'public/product_image/'.$product->p_image;
								}
								else
								{
									$wishlist_item['product_image'] = "";
								}

								$wishlist_item['product_description'] = $product->p_description;
								array_push($wishlist_list,$wishlist_item);	
							}
							
							if($wishlist_list) 
							{ 
								echo json_encode(array("status" => '1',"message" => 'Wishlist list !',"wishlist"=>$wishlist_list));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no item in your cart !'));
						}
					}	
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function delete_wishlist()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$delete_wishlist_post = file_get_contents("php://input");
			$decode_post = json_decode($delete_wishlist_post);

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->wishlist_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num>0)
					{
						$this->db->select('*');
						$this->db->where('w_id',$decode_post->wishlist_id);
						$select = $this->db->get('tbl_wishlist');
						$num = $select->num_rows();

						if($num!=0)
						{
							$delete = $this->HM->delete_wishlist($run->u_id,$decode_post->wishlist_id);

							if($delete)
							{
								echo json_encode(array("status" => '1',"message" => 'wishlist item deleted successfully !'));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'The wishlist id is not match with our database '));
						}
					}
					else
					{
					echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id and wishlist id fields are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function place_order()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$place_order_post = file_get_contents("php://input");
			$decode_post = json_decode($place_order_post);

			if(count((array)$decode_post)==5)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->payment_status) && !empty($decode_post->payment_mode) && !empty($decode_post->total_amount))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$this->db->select('*');
						$this->db->where('c_user_id',$decode_post->user_id);
						$select = $this->db->get('tbl_cart');
						$cart_num = $select->num_rows();

						if($cart_num>0)
						{
							$order_no = $this->HM->generate_order_no();
							$cart = $this->HM->manage_cart($decode_post->user_id);

							foreach($cart as $values)
							{
								$data=array();
								$data['o_order_id'] = $order_no;
								$data['o_user_id'] = $values['c_user_id'];
								$data['o_rep_id'] = $values['c_rep_id'];
								$data['o_product_id'] = $values['c_product_id'];
								$data['o_qty'] = $values['c_qty'];
								$data['o_price'] = $values['c_price'];
								$data['o_total_amount'] = $decode_post->total_amount;
								$data['o_payment_done'] = $decode_post->payment_status;
								$data['o_transaction_id'] = $decode_post->transaction_id;
								$data['o_payment_mode'] = $decode_post->payment_mode;
								$data['o_order_date'] = date('Y-m-d');
								$data['o_created_at'] = date('Y-m-d h:i:s');

								$insert_id = $this->HM->place_order($data);

								// Buyer Notification
								$data = array();
								$data['n_user_id'] = $values['c_user_id'];
								$data['n_title'] = 'Order Placed';
								$data['n_description'] = 'Your place an order !';
								$data['n_created_at'] = date('Y-m-d h:i:s');

								$this->HM->add_notification($data);

								// Reps Notification
								$data = array();
								$data['n_user_id'] = $values['c_rep_id'];
								$data['n_title'] = 'Order Placed';
								$data['n_description'] = 'You will receive an order !';
								$data['n_created_at'] = date('Y-m-d h:i:s');

								$this->db->where('c_user_id',$decode_post->user_id);
								$this->db->delete('tbl_cart');
							}

							if($insert_id)
							{
								echo json_encode(array("status" => 'success',"message" => 'Your order placed successfully !'));
							}
							else
							{
								echo json_encode(array("status" => 'failed',"message" => 'There is something that went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => 'failed',"message" => 'There is no data in your cart !'));
						}
					}
					else
					{
						echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'User id, Total Amount, Transaction Id, Payment Mode and Payment Status field are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function cancel_order()
	{
		$authentication = $this->verify_token();
    	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$cancel_order_post = file_get_contents("php://input");
			$decode_post = json_decode($cancel_order_post);
			$cancel_order_history = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->order_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$this->db->select("*");
						$this->db->where('o_user_id',$decode_post->user_id);
						$this->db->where('o_id',$decode_post->order_id);
						$select = $this->db->get('tbl_orders');
						$order_num = $select->num_rows();
						$order_run = $select->row();

						if($order_num>0)
						{
							$data = array();
							$data['o_flag'] = '2';

							$update = $this->HM->update_order_status($decode_post->order_id,$data);

							if(!empty($update))
							{
								echo json_encode(array("status" => '1',"message" => 'Your order successfully canceled !'));
							}
							else
							{
								echo json_encode(array("status" => '1',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found !'));
						}
					}
					else
					{
						echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'User id and Order id field are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function order_history()
    {
    	$authentication = $this->verify_token();
    	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$order_history_post = file_get_contents("php://input");
			$decode_post = json_decode($order_history_post);
			$order_history = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$this->db->select("*");
						$this->db->where('o_user_id',$decode_post->user_id);
						$select = $this->db->get('tbl_orders');
						$order_num = $select->num_rows();

						if($order_num>0)
						{
							$orders = $this->HM->order_history($decode_post->user_id);

							foreach($orders as $values)
							{
								$product = $this->HM->edit_product($values['o_product_id']);

								if($values['o_flag'] == '1')
								{
								    $order_status = 'Order Pending';
								}
								elseif($values['o_flag'] == '2')
								{
								    $order_status = 'Order Cancelled'; 
							 	}
								elseif($values['o_flag'] == '3') 
								{
								    $order_status = 'Order Delivered';
								}
								else
								{
									$order_status = '';
								}

					          	$order['o_id'] = $values['o_id'];
								$order['order_id'] = $values['o_order_id'];
								$order['product_id'] = $product->p_id;
								$order['product_name'] = $product->p_name;

								if(!empty($product->p_image))
								{
									$order['product_image'] = base_url().'public/product_image/'.$product->p_image;
								}
								else
								{
									$order['product_image'] = "";
								}

								$order['product_description'] = $product->p_description;
								$order['qty'] = $values['o_qty'];
								$order['payable_amount'] = $values['o_total_amount'];
								$order['order_status'] = $order_status;
								array_push($order_history,$order);
							}

							echo json_encode(array("status" => '1',"message" => 'Order History !',"order"=>$order_history));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is not item in your cart !'));
						}
					}
					else
					{
						echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'User id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function banner()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$banner_post = file_get_contents("php://input");
			$decode_post = json_decode($banner_post);
			$banner_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->order_by('b_order','asc');
					$select = $this->db->get('tbl_banner');
					$data = $select->result_array();
					$num = $select->num_rows();
						
					if($num>0)
					{
						foreach($data as $values)
						{
							$banner['id'] = $values['b_id'];

							if(!empty($values['b_image']))
							{
								$banner['image'] = base_url('public/banner/').$values['b_image'];
							}
							else
							{
								$banner['image'] = '';
							}

							array_push($banner_list,$banner);
						}

						if($banner_list)
						{
							echo json_encode(array("status" => '1',"message" => 'Banner List !',"banner" => $banner_list));
						}
						else 
						{
							echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'There is no data found !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function category()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$category_post = file_get_contents("php://input");
			$decode_post = json_decode($category_post);
			$category_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->order_by('c_id','desc');
					$this->db->where('c_status','1');
					$select = $this->db->get('tbl_category');
					$data = $select->result_array();
					$num = $select->num_rows();
						
					if($num>0)
					{
						foreach($data as $values)
						{
							$category['id'] = $values['c_id'];
							$category['name'] = $values['c_name'];

							if(!empty($values['c_image']))
							{
								$category['image'] = base_url('public/category/').$values['c_image'];
							}
							else
							{
								$category['image'] = '';
							}

							array_push($category_list,$category);
						}

						if($category_list)
						{
							echo json_encode(array("status" => '1',"message" => 'category List !',"category" => $category_list));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'There is no data found !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id is required field !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function faq()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$faq_post = file_get_contents("php://input");
			$decode_post = json_decode($faq_post);
			$faq_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->order_by('f_id','desc');
					$select = $this->db->get('tbl_faq');
					$data = $select->result_array();
					$num = $select->num_rows();
									
					if($num>0)
					{
						foreach($data as $values)
						{	
							$faq['id'] = $values['f_id'];
							$faq['question'] = $values['f_question'];
							$faq['answer'] = $values['f_answer'];
							array_push($faq_list,$faq);
						}

						if($faq_list)
						{
							echo json_encode(array("status" => '1',"message" => 'Faq List !',"Faq" => $faq_list));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'There is no data found !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The User id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function privacy_policy()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$privacy_post = file_get_contents("php://input");
			$decode_post = json_decode($privacy_post);

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$this->db->select('*');
						$select = $this->db->get('tbl_privacy_policy');
						$data = $select->row();
						$num = $select->num_rows();
						
						if($num>0)
						{
							if($data)
							{
								echo json_encode(array("status" => '1',"message" => 'Privacy & Policy !',"p&p" => strip_tags($data->pp_message)));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function query_send()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$query_send_post = file_get_contents("php://input");
			$decode_post = json_decode($query_send_post);

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->description) && !empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$data = array();
						$data['q_description'] = $decode_post->description;
						$data['q_user_id'] = $decode_post->user_id;
						$data['q_created_at'] = date('Y-m-d h:i:s');

						$insert = $this->HM->add_query($data);

						if($insert)
						{
							echo json_encode(array("status" => '1',"message" => 'Your query send successfully !'));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The description and user id are required fields !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function add_review()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$query_send_post = file_get_contents("php://input");
			$decode_post = json_decode($query_send_post);

			if(count((array)$decode_post)==4)
			{
				if(!empty($decode_post->product_id) && !empty($decode_post->user_id) && !empty($decode_post->rating) && !empty($decode_post->message))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$product_num = $this->HM->check_product_id($decode_post->product_id);

						if($product_num!=0)
						{
							$data = array();
							$data['r_review'] = $decode_post->rating;
							$data['r_message'] = $decode_post->message;
							$data['r_product_id'] = $decode_post->product_id;
							$data['r_user_id'] = $decode_post->user_id;

							$insert = $this->HM->add_review($data);

							// Update review & rating into product table
							$data=array();
							$data['p_review'] = $decode_post->rating;

							$this->HM->update_product($decode_post->product_id,$data);

							if($insert)
							{ 
							    echo json_encode(array("status" => 'success',"message" => 'Review added successfully !'));
							}	
							else
							{
								echo json_encode(array("status" => 'failed',"message" => 'There is something went wrong'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'The product id is not match with our database '));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The product id, user id, rating and message are required fields !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function review_list()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$review_list_post = file_get_contents("php://input");
			$decode_post = json_decode($review_list_post);
			$review_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->product_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$review_num = $this->HM->review_count($decode_post->product_id);

						if($review_num>0)
						{
							$product_num = $this->HM->check_product_id($decode_post->product_id);

							if($product_num!=0)
							{
								$review = $this->HM->manage_review($decode_post->product_id);

								foreach($review as $values)
								{
									$product = $this->HM->edit_product($values['r_product_id']);
									$profile = $this->HM->edit_profile($decode_post->user_id);

									$reviews['review_id'] = $values['r_id'];
									$reviews['review_rating'] = $values['r_review'];
									$reviews['review_message'] = $values['r_message'];
									$reviews['product_id'] = $product->p_id;
									$reviews['product_title'] = $product->p_name;
									$reviews['product_price'] = $product->p_price;
									$reviews['product_stock'] = $product->p_stock;
									$reviews['product_packsize'] = $product->p_packsize;
									$reviews['product_description'] = $product->p_description;

									if(!empty($product->p_image))
									{
										$reviews['product_image'] = base_url().'public/product_image/'.$product->p_image;
									}
									else
									{
										$reviews['product_image'] = "";
									}

									$reviews['product_description'] = $product->p_description;
									$review['user_name'] = $profile->u_first_name;

									if(!empty($profile->u_image))
									{
										$reviews['user_image'] = base_url().'public/profile_pic/'.$profile->u_image;
									}
									else
									{
										$reviews['user_image'] = "";
									}
									array_push($review_list,$reviews);	
								}
								
								if($review_list) 
								{ 
									echo json_encode(array("status" => '1',"message" => 'Reviews list !',"review"=>$review_list));
								}
								else
								{
									echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
								}
							}
							else
							{
								echo json_encode(array("status" => '0',"message"=>'The product id is not match with our database '));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}	
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function change_password()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$change_password_post = file_get_contents("php://input");
			$decode_post = json_decode($change_password_post);

			if(count((array)$decode_post)==4)
			{
				if(!empty($decode_post->old_password) && !empty($decode_post->new_password) && !empty($decode_post->confirm_password) && !empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
					$run = $this->HM->edit_profile($decode_post->user_id);

					if($user_num!=0)
					{
						if(md5($decode_post->old_password)==$run->u_password)
						{
							if($decode_post->new_password==$decode_post->confirm_password)
							{
								$data=array();
								$data['u_password'] = md5($decode_post->new_password);
								$data['u_decrypt_password'] = $decode_post->new_password;

								$update = $this->HM->change_password($run->u_id,$data);

								if($update)
								{
									echo json_encode(array("status" => '1',"message" => 'Your password changed successfully !'));
								}
								else
								{
									echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
								}
							}
							else
							{
								echo json_encode(array("status" => '0',"message"=>'Both password does not match !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'The old password is not match with our database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The old password, new password, current password and user id are required fileds !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	} 

	public function store_list()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$store_post = file_get_contents("php://input");
			$decode_post = json_decode($store_post);
			$reps_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->category_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
					
					if($user_num!=0)
					{
						$category_num = $this->HM->check_category_id($decode_post->category_id);

						if($user_num!=0)
						{
							$this->db->where('p_category',$decode_post->category_id);
							$select = $this->db->get('tbl_product');
							$data_product = $select->result_array();
							$num_product = $select->num_rows();

							if($num_product!=0)
							{
								foreach($data_product as $values)
								{
									$profile = $this->HM->edit_profile($values['p_user_id']);

									$rep_detail['reps_id'] = $profile->u_id;
									$rep_detail['category_id'] = $decode_post->category_id;
									$rep_detail['reps_name'] = $profile->u_first_name;
									$rep_detail['reps_company_name'] = $profile->u_company;

									$company_logo = str_replace("http://www.alexptech.com/replist/uploads/profile_images/", "", $profile->u_company_logo);
									$rep_detail['reps_company_logo'] = base_url('public/profile_images/').$company_logo;

									array_push($reps_list,$rep_detail);
								}

								if($reps_list) 
								{ 
									echo json_encode(array("status" => '1',"message" => 'Reps List !',"store_list"=>$reps_list));
								}
								else
								{
									echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
								}

							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
							}
						}	
						else
						{
							echo json_encode(array("status" => '0',"message"=>'The category id is not match with our database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id and category id are required fileds !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function forgot_password()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$forgot_password_post = file_get_contents("php://input");
			$decode_post = json_decode($forgot_password_post);
			$otp = random_int(100000,999999);

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->email))
				{
					$this->db->select('*');
					$this->db->where('u_email',$decode_post->email);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$this->db->select('*');
						$this->db->order_by('o_id','desc');
						$this->db->where('o_user_id',$run->u_id);
						$select = $this->db->get('tbl_otp');
						$otp_num = $select->num_rows();

						if($otp_num!=0)
						{
							$verify_otp = random_int(100000,999999);

							// Send otp to user mobile number
							$data=array();
							$data['o_user_id'] = $run->u_id; 
							$data['o_email'] = $decode_post->email;
							$data['o_otp'] = '123456';
							$data['o_source'] = 'Mobile';
							$data['o_type'] = 'Forgot';

							$update = $this->HM->update_email_otp($run->u_id,$data);	
							$last_otp = $this->HM->last_send_otp($run->u_id,$update);

					        if($update)
					        {
					        	echo json_encode(array("status" => '1',"message" => 'An email send on your registered mobile number !',"otp"=>'123456'));
					        } 
					        else
					        {
					        	echo json_encode(array("status" => '0',"message" => 'There is something went wrong'));
					        }
					    }
					    else
					    {
					    	echo json_encode(array("status" => '0',"message" => 'Please verify your email or mobile number.'));
					    }
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The email id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The email id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function verify_forgot_password_otp()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$otp_post = file_get_contents("php://input");
			$decode_post = json_decode($otp_post);
 
			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->email) && !empty($decode_post->otp))
				{
					$this->db->select('*');
					$this->db->order_by('u_id','desc');
					$this->db->where('u_email',$decode_post->email);
					$this->db->join('tbl_otp','tbl_otp.o_email = tbl_users.u_email');
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num!=0)
					{
						if($decode_post->otp==$run->o_otp)
						{
							$data['o_status'] = 1;
							$update = $this->HM->verify_otp($run->u_id,$data);

							if(!empty($update))
							{
								echo json_encode(array("status" => '1',"message"=>'You verified your OTP successfully !'));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'Please verify your email.'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'You entered wrong OTP.'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The email id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The email, otp and language fields are required!'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function resend_forgot_password_otp()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$otp_post = file_get_contents("php://input");
			$decode_post = json_decode($otp_post);
			$verify_otp = random_int(100000,999999);
			$id = '';

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->email) && !empty($decode_post->message_key))
				{
					$this->db->select('*');
					$this->db->where('u_email',$decode_post->email);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num>0)
					{
						// Send otp to user email
						$data['o_otp'] = '123456';

						$update = $this->HM->update_otp($decode_post->email,$data);	

						if($update)
						{
							echo json_encode(array("status" => '1',"message" => 'OTP has been send to your registered email.',"otp"=>'123456'));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The email is not match with our database.!'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The email, language and message key are required fields!'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function set_new_password()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$change_password_post = file_get_contents("php://input");
			$decode_post = json_decode($change_password_post);

			if(count((array)$decode_post)==3)
			{
				if(!empty($decode_post->new_password) && !empty($decode_post->confirm_password) && !empty($decode_post->email))
				{
					$this->db->select('*');
					$this->db->where('u_email',$decode_post->email);
					$select = $this->db->get('tbl_users');
					$num = $select->num_rows();
					$run = $select->row();

					if($num>0)
					{
						if($decode_post->new_password==$decode_post->confirm_password)
						{
							$data=array();
							$data['u_password'] = md5($decode_post->new_password);
							$data['u_decrypt_password'] = $decode_post->new_password;

							$update = $this->HM->change_password($run->u_id,$data);

							if($update)
							{
								echo json_encode(array("status" => '1',"message" => 'Your password changed successfully !'));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message"=>'Both password does not match !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The email is not match with our database !'));
					}
					
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The new password, email and language are required fileds !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
	}

	public function buy_product()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$place_order_post = file_get_contents("php://input");
			$decode_post = json_decode($place_order_post);

			if(count((array)$decode_post)==8)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->payment_status) && !empty($decode_post->payment_mode) && !empty($decode_post->total_amount) && !empty($decode_post->rep_id) && !empty($decode_post->product_id) && !empty($decode_post->qty))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{
						$rep_num = $this->HM->check_rep_id($decode_post->rep_id);

						if($rep_num!=0)
						{
							$product_num = $this->HM->count_product($decode_post->product_id);

							if($product_num!=0)
							{
								$order_no = $this->HM->generate_order_no();
								$product = $this->HM->edit_product($decode_post->product_id);

								$data=array();
								$data['o_order_id'] = $order_no;
								$data['o_user_id'] = $decode_post->user_id;
								$data['o_rep_id'] = $decode_post->rep_id;
								$data['o_product_id'] = $decode_post->product_id;
								$data['o_qty'] = $decode_post->qty;
								$data['o_price'] = $product->p_price;
								$data['o_total_amount'] = $decode_post->total_amount;
								$data['o_payment_done'] = $decode_post->payment_status;
								$data['o_transaction_id'] = $decode_post->transaction_id;
								$data['o_payment_mode'] = $decode_post->payment_mode;
								$data['o_order_date'] = date('Y-m-d');
								$data['o_created_at'] = date('Y-m-d h:i:s');

								$insert_id = $this->HM->place_order($data);

								// Buyer Notification
								$data = array();
								$data['n_user_id'] = $decode_post->user_id;
								$data['n_title'] = 'Order Placed';
								$data['n_description'] = 'Your Order placed successfully !';
								$data['n_created_at'] = date('Y-m-d h:i:s');

								$this->HM->add_notification($data);

								// Reps Notification
								$data = array();
								$data['n_user_id'] = $decode_post->rep_id;
								$data['n_title'] = 'Order Placed';
								$data['n_description'] = 'You will receive an order !';
								$data['n_created_at'] = date('Y-m-d h:i:s');

								$this->HM->add_notification($data);

								if($insert_id)
								{
									echo json_encode(array("status" => 'success',"message" => 'Your order placed successfully !'));
								}
								else
								{
									echo json_encode(array("status" => 'failed',"message" => 'There is something that went wrong !'));
								}
							}
							else
							{
								echo json_encode(array("status" => 'failed',"message" => 'The product id is not match with database !'));
							}
						}
						else
						{
							echo json_encode(array("status" => 'failed',"message" => 'The rep id is not match with database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'User id, Total Amount, Transaction Id, Payment Mode and Payment Status field are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function catalog() 
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$product_post = file_get_contents("php://input");
			$decode_post = json_decode($product_post);
			$product_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->buyer_id) && !empty($decode_post->rep_id))
				{
					$buyer_num = $this->HM->check_buyer_id($decode_post->buyer_id);

					if($buyer_num!=0)
					{
						$user_num = $this->HM->check_rep_id($decode_post->rep_id);

						if($user_num!=0)
						{
							$this->db->select('*');
							$this->db->where('p_user_id',$decode_post->rep_id);
							$this->db->where('p_status','0');
							$select = $this->db->get('tbl_product');
							$product = $select->result_array();
							$num = $select->num_rows();

							if($num>0)
							{
								foreach($product as $values)
								{
									$category = $this->HM->get_category_detail($values['p_category']);

									$product=array();
									$product['id'] = $values['p_id'];

									if(!empty($category))
									{
										$product['category'] = $category->c_name;
									}
									else
									{
										$product['category'] = '';
									}
									
									$product['name'] = $values['p_name'];
									$product['price'] = $values['p_price'];
									$product['stock'] = $values['p_stock'];
									$product['packsize'] = $values['p_packsize'];
									$product['description'] = $values['p_description'];
									$product['image'] = base_url('public/product_image/').$values['p_image'];

									$this->db->where('c_product_id',$values['p_id']);
									$cart_select = $this->db->get('tbl_cart');
									$cart_num = $cart_select->num_rows();
									$cart_data = $cart_select->row();

									if($cart_num!=0)
									{
										$product['cart_qty'] = $cart_data->c_qty;
										$product['cart_id'] = $cart_data->c_id;
									}
									else
									{
										$product['cart_qty'] = '';
										$product['cart_id'] = '';
									}

									$this->db->where('w_p_id',$values['p_id']);
									$wishlist_select = $this->db->get('tbl_wishlist');
									$wishlist_num = $wishlist_select->num_rows();
									$wishlist_data = $wishlist_select->row();

									if($wishlist_num!=0)
									{
										$product['wishlist_qty'] = '1';
										$product['wishlist_id'] = $wishlist_data->w_id;
									}
									else
									{
										$product['wishlist_qty'] = '';
										$product['wishlist_id'] = '';
									}

									array_push($product_list,$product);
								}

								if($product_list)
								{  
									echo json_encode(array("status" => '1',"message" => 'Product List',"product_list"=>$product_list));
								} 
								else
								{
									echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
								}
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'The rep id is not match with our database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The buyer id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The buyer id and rep id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    }

    public function reports()
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$report_post = file_get_contents("php://input");
			$decode_post = json_decode($report_post);
			$order_history = array(); $total_month_sales=0; $total_year_sales=0; $total_sales=0;

			if(count((array)$decode_post)==3)
			{
				if(!empty($decode_post->buyer_id))
				{ 
					$buyer_num = $this->HM->check_buyer_id($decode_post->buyer_id);

					if($buyer_num!=0)
					{
						if(!empty($decode_post->month))
						{
							$to_month =  date("Y-m-d", strtotime($decode_post->month.'+1 month'));
							$from_month =  date("Y-m-d", strtotime($decode_post->month.'-1 month'));

							$this->db->select('*');
							$this->db->order_by('o_id','desc');
							$this->db->where('o_user_id',$decode_post->buyer_id);
							$this->db->where('o_flag','3');
							$this->db->where("o_created_at BETWEEN '{$from_month}' AND '{$to_month}'");
							$select = $this->db->get('tbl_orders');
							$num = $select->num_rows();
							$orders = $select->result_array();
						}
						elseif(!empty($decode_post->year))
						{
							$to_year =  date("Y-m-d", strtotime($decode_post->year.'+1 year'));
							$from_year =  date("Y-m-d", strtotime($decode_post->year.'-1'));

							$this->db->select('*');
							$this->db->order_by('o_id','desc');
							$this->db->where('o_user_id',$decode_post->buyer_id);
							$this->db->where('o_flag','3');
							$this->db->where("o_created_at BETWEEN '{$from_year}' AND '{$to_year}'");
							$select = $this->db->get('tbl_orders');
							$num = $select->num_rows();
							$orders = $select->result_array();
						}

						elseif(empty($decode_post->year) && empty($decode_post->month))
						{
							$this->db->select('*');
							$this->db->order_by('o_id','desc');
							$this->db->where('o_user_id',$decode_post->buyer_id);
							$this->db->where('o_flag','3');
							$select = $this->db->get('tbl_orders');
							$num = $select->num_rows();
							$orders = $select->result_array();
						}
							if($num>0)
							{
								foreach($orders as $values)
								{
									$product = $this->HM->edit_product($values['o_product_id']);

									if($values['o_flag'] == '1')
									{
									    $order_status = 'Order Pending';
									}
									elseif($values['o_flag'] == '2')
									{
									    $order_status = 'Order Cancelled'; 
								 	}
									elseif($values['o_flag'] == '3') 
									{
									    $order_status = 'Order Completed/Delivered';
									}
									else
									{
										$order_status = '';
									}

						          	$order['o_id'] = $values['o_id'];
									$order['order_id'] = $values['o_order_id'];
									$order['product_id'] = $product->p_id;
									$order['product_name'] = $product->p_name;

									if(!empty($product->p_image))
									{
										$order['product_image'] = base_url().'public/product_image/'.$product->p_image;
									}
									else
									{
										$order['product_image'] = "";
									}

									$order['product_description'] = $product->p_description;
									$order['qty'] = $values['o_qty'];
									$order['payable_amount'] = $values['o_total_amount'];
									$order['order_status'] = $order_status;
									$order['payment_date'] = '';
									array_push($order_history,$order);
								}

								// This month sales
								$current_date = date('Y-m-d');
								$to_m =  date("Y-m-d", strtotime($current_date.'+1 month'));
								$from_m =  date("Y-m-d", strtotime($current_date.'-1'));

								$this->db->select('*');
								$this->db->order_by('o_id','desc');
								$this->db->where('o_flag','3');
								$this->db->where("o_created_at BETWEEN '{$from_m}' AND '{$to_m}'");
								$select = $this->db->get('tbl_orders');
								$num = $select->num_rows();
								$orders = $select->result_array();

								foreach($orders as $values)
								{
									$sales=$values['o_total_amount'];
									$total_month_sales+=$sales;
								}

								// This year sales
								$current_date = date('Y-m-d');
								$to_y =  date("Y-m-d", strtotime($current_date.'+1 year'));
								$from_y =  date("Y-m-d", strtotime($current_date.'-1'));

								$this->db->select('*');
								$this->db->order_by('o_id','desc');
								$this->db->where('o_flag','3');
								$this->db->where("o_created_at BETWEEN '{$from_y}' AND '{$to_y}'");
								$select = $this->db->get('tbl_orders');
								$num = $select->num_rows();
								$orders = $select->result_array();

								foreach($orders as $values)
								{
									$sales=$values['o_total_amount'];
									$total_year_sales+=$sales;
								}

								// Total sales
								$this->db->select('*');
								$this->db->order_by('o_id','desc');
								$this->db->where('o_flag','3');
								$select = $this->db->get('tbl_orders');
								$num = $select->num_rows();
								$orders = $select->result_array();

								foreach($orders as $values)
								{
									$sales=$values['o_total_amount'];
									$total_sales+=$sales;
								}

								echo json_encode(array("status" => '1',"message" => 'Order History !',"order"=>$order_history,"this_month"=>$total_month_sales,"this_year"=>$total_year_sales,"total_sales"=>$total_sales));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
							}
						
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The buyer id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The buyer id, month and year fields are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    }

    public function reward_list()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$reward_count_post = file_get_contents("php://input");
			$decode_post = json_decode($reward_count_post);
			$reward_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num>0)
					{
						$this->db->order_by('rp_id','desc');
						$this->db->where('rp_user_id',$run->u_id);
						$select = $this->db->get('tbl_reward_points');
						$reward_num = $select->num_rows();
							
						if($reward_num!=0)
						{
							$data = $this->HM->manage_reward($run->u_id);

							foreach($data as $values)
							{
								$reward['id'] = $values['rp_id']; 
								$reward['points'] = $values['rp_points'];
								$reward['total_points'] = $values['rp_total_points'];
								$reward['transaction_history'] = $values['rp_transaction_history'];
								$reward['created_at'] = date('m-d-Y',strtotime($values['rp_created_at']));
								array_push($reward_list,$reward);
							}
							
							if(!empty($reward_list))
							{
								echo json_encode(array("status" => '1',"message" => 'Reward List !',"Reward_List"=>$reward_list));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
		
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function total_reward_points()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$reward_count_post = file_get_contents("php://input");
			$decode_post = json_decode($reward_count_post);
			$detail = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num>0)
					{
						$this->db->order_by('rp_id','desc');
						$this->db->where('rp_user_id',$run->u_id);
						$select = $this->db->get('tbl_reward_points');
						$reward_num = $select->num_rows();
							
						if($reward_num!=0)
						{
							$data = $this->HM->total_reward_points($run->u_id);
							
							if(!empty($data->rp_total_points))
							{
								$point_detail = array();
								$point_detail['points'] = $data->rp_total_points;
								$point_detail['referal_code'] = $run->u_user_code;
								$point_detail['referal_points'] = '50';
								array_push($detail,$point_detail);

								echo json_encode(array("status" => '1',"message" => 'Total reward points !',"point_detail"=>$detail));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}
						}
						else
						{
							$point_detail = array();
							$point_detail['points'] = '0';
							$point_detail['referal_code'] = $run->u_user_code;
							$point_detail['referal_points'] = '50';
							array_push($detail,$point_detail);

							echo json_encode(array("status" => '1',"message" => 'Total reward points !',"point_detail"=>$detail));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function notification_list()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$notification_post = file_get_contents("php://input");
			$decode_post = json_decode($notification_post);
			$notification_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$buyer_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($buyer_num!=0)
					{
						$notification_num = $this->HM->check_notifications($decode_post->user_id);

						if($notification_num!=0)
						{
							$data = $this->HM->manage_notification($decode_post->user_id);

							foreach($data as $values)
							{	
								$notification['id'] = $values['n_id'];
								$notification['title'] = $values['n_title'];
								$notification['notification'] = $values['n_description'];
								$notification['notification_date'] = $values['n_created_at'];
								array_push($notification_list,$notification);
							}

							if($notification_list)
							{
								echo json_encode(array("status" => '1',"message" => 'Notification List !',"notification" => $notification_list));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database !'));
					}	
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function SendMail() 
	{
        $authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$send_mail_post = file_get_contents("php://input");
			$decode_post = json_decode($send_mail_post);

			if(count((array)$decode_post)==4)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->email) && !empty($decode_post->subject) && !empty($decode_post->message))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);

					if($user_num!=0)
					{ 
            			$to_email = explode(",",$decode_post->email);
            
			            foreach($to_email as $values) 
			            {
			                $to = $values;
			                $subject = $decode_post->subject;
			                $txt = stripslashes($decode_post->message);
			                $headers = $decode_post->email;

			                $send = mail($to,$subject,$txt,$headers);
			            }

			            if($send)
						{
							echo json_encode(array("status" => '1',"message" => 'Your mail send successfully !'));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database '));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The email, subject, message and user id are required fields !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}  
    }

    public function get_rep_details()
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$user_detail_post = file_get_contents("php://input");
			$decode_post = json_decode($user_detail_post);
			$detail_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->rep_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
					
					if($user_num!=0)
					{
						$rep_num = $this->HM->check_rep_id($decode_post->rep_id);

						if($rep_num!=0)
						{
							$profile = $this->HM->edit_profile($decode_post->rep_id);
							$user_num = $this->HM->check_rep_id($decode_post->rep_id);

							if($user_num!=0)
							{
								$detail['reps_id'] = $profile->u_id;
							    $detail['user_code'] = $profile->u_user_code;
								$detail['first_name'] = $profile->u_first_name;
								$detail['last_name'] = $profile->u_last_name;
								$detail['email'] = $profile->u_email;
								$detail['mobile'] = $profile->u_mobile;
								$detail['country'] = $profile->u_country;
								$detail['state'] = $profile->u_state;
								$detail['city'] = $profile->u_city;
								$detail['street_address'] = $profile->u_street_address;
								$detail['postalcode'] = $profile->u_postalcode;
								$detail['sales_position'] = $profile->u_sales_position;
								$detail['area_cover'] = $profile->u_area_cover;
								$detail['department'] = $profile->u_department;
								$detail['customer_application'] = $profile->u_customer_application;
								$detail['reps_company_name'] = $profile->u_company;
								$detail['company_contact'] = $profile->u_company_contact;
								$detail['joining_date'] = $profile->u_joining_date;
								$detail['paypal_id'] = $profile->u_paypal_id;

								if(!empty($profile->u_image))
								{
									$detail['image'] = base_url('public/profile_images/').$profile->u_image;
								}
								else
								{
									$detail['image'] = '';
								}

								if(!empty($profile->u_company_logo))
								{
									$detail['company_logo'] = base_url('public/profile_images/').$profile->u_company_logo;
								}
								else
								{
									$detail['company_logo'] = '';
								}
							}
							else
							{
								$detail['reps_id'] = '';
							    $detail['user_code'] = '';
								$detail['first_name'] = '';
								$detail['last_name'] = '';
								$detail['email'] = '';
								$detail['mobile'] = '';
								$detail['country'] = '';
								$detail['state'] = '';
								$detail['city'] = '';
								$detail['street_address'] = '';
								$detail['postalcode'] = '';
								$detail['sales_position'] = '';
								$detail['area_cover'] = '';
								$detail['department'] = '';
								$detail['customer_application'] = '';
								$detail['reps_company_name'] = '';
								$detail['reps_company_logo'] = '';
								$detail['company_contact'] = '';
								$detail['joining_date'] = '';
								$detail['paypal_id'] = '';
								$detail['image'] = '';
								$detail['company_logo'] = '';
							}

							array_push($detail_list,$detail);

							if($detail_list)
							{  
								echo json_encode(array("status" => '1',"message" => 'Rep detail',"detail_list"=>$detail_list));
							} 
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}		
						}

						else
						{
							echo json_encode(array("status" => '0',"message" => 'The rep id is not match with our database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id, rep id and category field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
    } 

	public function coupon_list()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$coupon_post = file_get_contents("php://input");
			$decode_post = json_decode($coupon_post);
			$coupon_list = array(); $current_date = date('m-d-Y');

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
					
					if($user_num!=0)
					{
						$coupon_num = $this->HM->coupon_num();

						if($coupon_num>0)
						{
							$sql = $this->HM->manage_coupon();

							foreach($sql as $values)
							{	 
								// Check coupon expiry
								$current_date = date('Y-m-d');
                    			$expiry_date = strtotime($values['co_expiry_date']);
                    			$datediff = strtotime($current_date)-$expiry_date;
                    			$result = floor($datediff/(60*60*24));

				                if($result!=0)
				                {
									$coupon['id'] = $values['co_id'];
									$coupon['name'] = $values['co_name'];
									$coupon['coupon_type'] = $values['co_coupon_type'];
									$coupon['description'] = $values['co_description'];
									$coupon['start_date'] = date('m-d-Y',strtotime($values['co_start_date'])); 
									$coupon['expiry_date'] = date('m-d-Y',strtotime($values['co_expiry_date']));
									$coupon['coupon_code'] = $values['co_coupon_code']; 
									$coupon['coupon_value'] = $values['co_coupon_value'];
									$coupon['minimum_value'] = $values['co_min_value'];
									array_push($coupon_list,$coupon);
								}	
							}

							if($coupon_list)
							{
								echo json_encode(array("status" => '1',"message" => 'Coupon List !',"Coupon" => $coupon_list));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function apply_coupon()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$coupon_post = file_get_contents("php://input");
			$decode_post = json_decode($coupon_post);
			$grandtotal=0;

			if(count((array)$decode_post)==2) 
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->coupon_code))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
					$check_coupon = $this->HM->check_coupon_code($decode_post->coupon_code);

					if($user_num>0)
					{
						if($check_coupon!=0)
						{
							$coupon_detail = $this->HM->coupon_detail($decode_post->coupon_code);

							// Get User cart total amount
							$cart = $this->HM->manage_cart($decode_post->user_id);

							foreach($cart as $values)
					        {
					            $subtotal = $values['c_price']*$values['c_qty'];
					            $total=$subtotal;
					            $grandtotal+=$total;
					        }

							if($grandtotal>=$coupon_detail->co_min_value)
							{
								if($coupon_detail->co_coupon_type=='Amount')
								{
									$final_price = $grandtotal-$coupon_detail->co_min_value;
									$data = array();
									$data['c_coupon_code'] = $decode_post->coupon_code;
									$data['c_discount_value'] = $coupon_detail->co_coupon_value;
									$data['c_is_coupon_applied'] = '1';
									$data['c_final_price'] = $final_price;
									$data['c_coupon_discount'] = $coupon_detail->co_coupon_value;

									$update = $this->HM->update_cart_details($run->u_id,$data);

									if($update)
									{
										echo json_encode(array("status" => 'success',"message" => 'Your coupon code applied successfully !'));
									}
									else
									{
										echo json_encode(array("status" => 'failed',"message" => 'There is something that went wrong !'));
									}
								}
								elseif($coupon_detail->co_coupon_type=='Percentage')
								{
									$discount_value = $grandtotal*$coupon_detail->co_coupon_value/100;
									$final_price = $grandtotal-(($grandtotal*$coupon_detail->co_coupon_value)/100);

									$data = array();
									$data['c_coupon_code'] = $decode_post->coupon_code;
									$data['c_discount_value'] = $discount_value;
									$data['c_final_price'] = $final_price;
									$data['c_is_coupon_applied'] = '1';
									$data['c_coupon_discount'] = $discount_value;

									$update = $this->HM->update_cart_details($run->u_id,$data);
		
									if($update)
									{
										echo json_encode(array("status" => 'success',"message" => 'Your coupon code applied successfully !'));
									}
									else
									{
										echo json_encode(array("status" => 'failed',"message" => 'There is something that went wrong !'));
									}
								}
								else
								{
									echo json_encode(array("status" => 'failed',"message" => 'Coupon type is not match !'));
								}
							}
							else
							{
								echo json_encode(array("status" => 'failed',"message" => 'The cart total amount is not matched to apply coupon code !'));
							}
						} 
						else
						{
							echo json_encode(array("status" => 'failed',"message" => 'The Coupon code is not match with database !'));
						}
					}
					else
					{ 
						echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The User id and Coupon Code fields are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function apply_reward_points() 
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$coupon_post = file_get_contents("php://input");
			$decode_post = json_decode($coupon_post);
			$grandtotal=0;

			if(count((array)$decode_post)==2) 
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->reward_points))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
					$total_reward_points = $this->HM->total_reward_points($decode_post->user_id);

					if($user_num>0)
					{
						if($total_reward_points->rp_total_points!=0)
						{
							// Get User cart total amount
							$cart = $this->HM->manage_cart($decode_post->user_id);

							foreach($cart as $values)
							{
							    $subtotal = $values['c_price']*$values['c_qty'];
							    $total=$subtotal;
							    $grandtotal+=$total;
							}

							$final_price = $grandtotal-$total_reward_points->rp_total_points;

							$data = array();
							$data['c_reward_points'] = $decode_post->reward_points;
							$data['c_discount_value'] = $total_reward_points->rp_total_points;
							$data['c_final_price'] = $final_price;
							$data['c_is_reward_applied'] = '1';
							$data['c_reward_points'] = $total_reward_points->rp_total_points;

							$update = $this->HM->update_cart_details($decode_post->user_id,$data);

							if($update)
							{
								echo json_encode(array("status" => 'success',"message" => 'Your reward points applied successfully !'));
							}
							else
							{
								echo json_encode(array("status" => 'failed',"message" => 'There is something that went wrong !'));
							}
						}
						else
						{ 
							echo json_encode(array("status" => 'failed',"message" => 'You have no reward points !'));
						}
					}
					else
					{ 
						echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The User id and Reward Points fields are required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function get_location()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$location_post = file_get_contents("php://input");
			$decode_post = json_decode($location_post);
			$location = array();

			if(count((array)$decode_post)==1) 
			{
				if(!empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
	
					if($user_num!=0)
					{
						$profile = $this->HM->edit_profile($decode_post->user_id);

						$detail['country'] = $profile->u_country;
						$detail['state'] = $profile->u_state;
						$detail['city'] = $profile->u_city;
						$detail['street_address'] = $profile->u_street_address;
						$detail['postalcode'] = $profile->u_postalcode;
						array_push($location,$detail);

						if($location)
						{
							echo json_encode(array("status" => '1',"message" => 'Location details !',"location_details"=>$location));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
						}
					}
					else
					{ 
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The User id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function reps_connected()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$reps_connected_post = file_get_contents("php://input");
			$decode_post = json_decode($reps_connected_post);
			$email_data = array();

			if(count((array)$decode_post)==1) 
			{
				if(!empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
	
					if($user_num!=0)
					{
						$profile = $this->HM->edit_profile($decode_post->user_id);

						$this->db->order_by('sm_id','desc');
						$this->db->where_in('sm_receiver_email',$profile->u_email);
						$select = $this->db->get('tbl_send_mail');
						$data = $select->result_array();
						$num = $select->num_rows();

						if($num>0)
						{
							foreach($data as $values)
							{
								// Get user profile data
								$profile = $this->HM->edit_profile($values['sm_user_id']);

								$email['user_code'] = $profile->u_user_code;
								$email['first_name'] = $profile->u_first_name;
								$email['last_name'] = $profile->u_last_name;
								$email['mobile'] = $profile->u_mobile;
								$email['sales_position'] = $profile->u_sales_position;
								$email['area_cover'] = $profile->u_area_cover;
								$email['gender'] = $profile->u_gender;
								$email['company'] = $profile->u_company;
								$email['department'] = $profile->u_department;
								$email['street_address'] = $profile->u_street_address;
								$email['email'] = $profile->u_email;
								$email['country'] = $profile->u_country;
								$email['state'] = $profile->u_state;
								$email['city'] = $profile->u_city;
								$email['postalcode'] = $profile->u_postalcode;
								$email['customer_application'] = $profile->u_customer_application;
								$email['company_contact'] = $profile->u_company_contact;
								$email['paypal_id'] = $profile->u_paypal_id;
			
								if(!empty($profile->u_image))
								{
									$email['image'] = base_url('public/profile_images/').$profile->u_image;
								}
								else
								{
									$email['image'] = '';
								}

								$email['subject'] = $values['sm_subject'];
								$email['message'] = $values['sm_message'];
								array_push($email_data,$email);
							} 

							if($email_data)
							{
								echo json_encode(array("status" => '1',"message" =>'Reps connected !',"data"=>$email_data));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found on our database !'));
						}
					}
					else
					{ 
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The User id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}

	public function promotion()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$promotion_post = file_get_contents("php://input");
			$decode_post = json_decode($promotion_post);
			$promotion_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_buyer_id($decode_post->user_id);
					
					if($user_num!=0)
					{
						$promotion_num = $this->HM->promotion_num();

						if($promotion_num>0)
						{
							$sql = $this->HM->manage_promotion();

							foreach($sql as $values)
							{	 
								// Check coupon expiry
								$current_date = date('Y-m-d');
                    			$expiry_date = strtotime($values['p_to_date']);
                    			$datediff = strtotime($current_date)-$expiry_date;
                    			$result = floor($datediff/(60*60*24));

				                if($result>=0) 
				                {
				                    $promotion['promotion_status'] = 'Expired';
				                } 
				                elseif($result!=0)
				                {
				                    $promotion['promotion_status'] = 'Not Expired';
				                }

								$promotion['id'] = $values['p_id'];
								$promotion['title'] = $values['p_title'];
								$promotion['description'] = $values['p_description'];
								$promotion['from_date'] = date('m-d-Y',strtotime($values['p_from_date'])); 
								$promotion['to_date'] = date('m-d-Y',strtotime($values['p_to_date']));

								if(!empty($values['p_image']))
								{
									$promotion['image'] = base_url('public/promotion/').$values['p_image'];
								}
								else
								{
									$promotion['image'] = '';
								}

								array_push($promotion_list,$promotion);	
							}

							if($promotion_list)
							{
								echo json_encode(array("status" => '1',"message" => 'Promotion List !',"Promotion" => $promotion_list));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something that went wrong !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found in database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method should be POST and content type should be JSON !'));
		}
	}
}
?>