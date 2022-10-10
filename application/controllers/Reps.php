<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
use Twilio\Rest\Client;
    
class Reps extends CI_Controller 
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
		$a['title'] = 'Good Evening';
        $a['msg'] = 'Welcome to MYBIO23 .Your Registration has been successful !';

        $load['data']['title'] = 'Good Evening';
        $load['data']['is_background'] = false;
        $load['data']['message'] = 'Welcome to MYBIO23 .Your Registration has been successful !';
        $load['data']['image'] = '';
        $load['data']['payload'] = $a;
        $load['data']['timestamp'] = date('Y-m-d h:i:s');

        $token[] = 'f7zmoGk8R4yVyMWnIgr7nw:APA91bFLod3kNAcwg-slZBrQn0sU__mwGsdn8iJ8-azIopFNlErL4-dKthyAp-6__MOAdswsR26isr_q_abSOwIPuSqIo7aF6L-JOsCRJjNyPGoqyKFCOjjePh93UJ-DcoO6ESDmdgd5';

        $this->common->android_push($token,$load,'AAAAkyClyO4:APA91bEgg_4H05w2_C56aGzb83iyQDqNTXhligpYj4rrBUHXzCubmtuKtJtKqGtxvqHejg2JLz7oMlSVQlv57ZVYZAHWNZLkx5lJ11Fj0k5grkv__th9AyoEsOYUyrnqONUSEyeLvhNM	');

        echo json_encode($load);
	}

	public function homepage()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$homepage_post = file_get_contents("php://input");
			$decode_post = json_decode($homepage_post);

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_type','Reps');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$num = $select->num_rows();

					if($num!=0)
					{
						$this->db->select("*");
						$this->db->where('o_rep_id',$decode_post->user_id);
						$select = $this->db->get('tbl_orders');
						$order_num = $select->num_rows();

						if($order_num!=0)
						{
							$total_orders = $this->HM->total_orders($decode_post->user_id);
							$delivered_orders = $this->HM->delivered_orders($decode_post->user_id);
							$pending_orders = $this->HM->pending_orders($decode_post->user_id);

							if(!empty($total_orders))
							{
								echo json_encode(array("status" => '1',"message"=>'Order history detail !',"total_orders"=>(string)$total_orders,"delivered_orders"=>(string)$delivered_orders,"pending_orders"=>(string)$pending_orders));
							}
							else
							{
								echo json_encode(array("status" => '1',"message"=>'No order history found !',"total_orders"=>"0","delivered_orders"=>"0","pending_orders"=>"0"));
							}
						}
						else
						{
							echo json_encode(array("status" => '1',"message"=>'No order history found !',"total_orders"=>"0","delivered_orders"=>"0","pending_orders"=>"0"));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message"=>'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id is required filed !'));
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
 
	public function SignUp()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$register_post = file_get_contents("php://input");
			$decode_post = json_decode($register_post);

			if(count((array)$decode_post)==8)
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
							$data = array();
							$data['u_first_name'] = $decode_post->first_name;
							$data['u_last_name'] = $decode_post->last_name;
							$data['u_email'] = $decode_post->email;
							$data['u_mobile'] = $decode_post->mobile;
							$data['u_password'] = md5($decode_post->password);
							$data['u_decrypt_password'] = $decode_post->password;
							$data['u_device_id'] = $decode_post->device_id;
							$data['u_firebase_token'] = $decode_post->firebase_token;
							$data['u_type'] = 'Reps';
							$data['u_message_key'] = $decode_post->message_key;
							$data['u_verified'] = '0'; 

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

	public function Registerusers()
	{
		$this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$user_post = file_get_contents("php://input");
			$decode_post = json_decode($user_post);
			$user_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id))
				{
					if(!empty($decode_post->keyword))
					{
						$this->db->select('*');
						$this->db->order_by('u_id','desc');
						$this->db->like('u_first_name',$decode_post->keyword);
						$select = $this->db->get('tbl_users');
						$num = $select->num_rows();
						$res = $select->result_array();
					}
					else
					{
						$this->db->select('*');
						$this->db->order_by('u_id','desc');
						$select = $this->db->get('tbl_users');
						$num = $select->num_rows();
						$res = $select->result_array();
					}
					
					if($num>0)
					{
						foreach($res as $data)
						{
							$user_detail = array();
							$user_detail['user_id'] = $data['u_id'];  
							$user_detail['name'] = $data['u_first_name'];
							$user_detail['user_id'] = $data['u_id'];
							$user_detail['email'] = $data['u_email'];
							$user_detail['mobile'] = $data['u_mobile'];
							$user_detail['country'] = $data['u_country'];
							$user_detail['type'] = $data['u_type'];
							$user_detail['state'] = $data['u_state'];
							$user_detail['city'] = $data['u_city'];
							$user_detail['street_address'] = $data['u_street_address'];
							$user_detail['postalcode'] = $data['u_postalcode'];
							$user_detail['sales_position'] = $data['u_sales_position'];
							$user_detail['area_cover'] = $data['u_area_cover'];
							$user_detail['department'] = $data['u_department'];
							$user_detail['customer_application'] = $data['u_customer_application'];
							$user_detail['company'] = $data['u_company'];
							$user_detail['company_contact'] = $data['u_company_contact'];
							$user_detail['joining_date'] = $data['u_joining_date'];
							$user_detail['paypal_id'] = $data['u_paypal_id'];
							$user_detail['fb'] = $data['u_fb'];
							$user_detail['insta'] = $data['u_insta'];
							$user_detail['linkedin'] = $data['u_linkedin'];
							$user_detail['website'] = $data['u_website'];

							if(!empty($data['u_gender']))
							{
								$user_detail['gender'] = $data['u_gender'];
							}
							else
							{
								$user_detail['gender'] = '';
							}
									
							if(!empty($data['u_image']))
							{
								$user_detail['image'] = base_url('public/profile_images/').$data['u_image'];
							}
							else
							{
								$user_detail['image'] = '';
							}

							array_push($user_list,$user_detail);
						}
						
						if(!empty($user_list))
						{
							echo json_encode(array("status" => '1',"message" => 'Registered users', "User Details" => $user_list));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'There is no data found on our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id field is required!'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'Bad request !'));
			}
		}
		else
		{
			echo json_encode(array("status" => '0',"message" => 'API method Should be POST and Content type JSON !'));
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
					$this->db->where('u_type','Reps');
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
					$this->db->where('u_type','Reps');
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
					$user_num = $this->HM->check_rep_id($decode_post->user_id);
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
								echo json_encode(array("status" => '1',"message"=>'Welcome to Replist .Your Registration has been successful !',"token"=>$jwt_token,"user_id"=>$run->u_id));
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
						$data['o_otp'] = '123456';
						$update = $this->HM->update_otp($decode_post->mobile,$data);	

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

	public function ProfileInfo() 
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
					$this->db->where('u_type','Reps');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$data = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
							$user_detail=array();
						    $user_detail['user_id'] = $data->u_id;  
							$user_detail['first_name'] = $data->u_first_name;
							$user_detail['last_name'] = $data->u_last_name;
							$user_detail['email'] = $data->u_email;
							$user_detail['mobile'] = $data->u_mobile;
							$user_detail['country'] = $data->u_country;
							$user_detail['state'] = $data->u_state;
							$user_detail['city'] = $data->u_city;
							$user_detail['street_address'] = $data->u_street_address;
							$user_detail['postalcode'] = $data->u_postalcode;
							$user_detail['sales_position'] = $data->u_sales_position;
							$user_detail['area_cover'] = $data->u_area_cover;
							$user_detail['department'] = $data->u_department;
							$user_detail['customer_application'] = $data->u_customer_application;
							$user_detail['company'] = $data->u_company;
							$user_detail['business_type'] = $data->u_business_type;
							$user_detail['company_contact'] = $data->u_company_contact;
							$user_detail['joining_date'] = $data->u_joining_date;

							if(!empty($data->u_paypal_id))
							{
								$user_detail['paypal_id'] = $data->u_paypal_id;
							}
							else
							{
								$user_detail['paypal_id'] = '';
							}

							if(!empty($data->u_fb) && $data->u_fb!='null')
							{
								$user_detail['fb'] = $data->u_fb;
							}
							else
							{
								$user_detail['fb'] = '';
							}

							if(!empty($data->u_insta) && $data->u_insta!='null')
							{
								$user_detail['insta'] = $data->u_insta;
							}
							else
							{
								$user_detail['insta'] = '';
							}

							if(!empty($data->u_linkedin) && $data->u_linkedin!='null')
							{
								$user_detail['linkedin'] = $data->u_linkedin;
							}
							else
							{
								$user_detail['linkedin'] = '';
							}

							if(!empty($data->u_website) && $data->u_website!='null')
							{
								$user_detail['website'] = $data->u_website;
							}
							else
							{
								$user_detail['website'] = '';
							}

							if(!empty($data->u_gender) && $data->u_gender!='null')
							{
								$user_detail['gender'] = $data->u_gender;
							}
							else
							{
								$user_detail['gender'] = '';
							}
									
							if(!empty($data->u_image))
							{
								$profile_pic = str_replace("http://www.alexptech.com/replist/uploads/profile_images/", "", $data->u_image);
								$user_detail['image'] = base_url('public/profile_images/').$profile_pic;
							}
							else
							{
								$user_detail['image'] = '';
							}

							if(!empty($data->u_company_logo))
							{
								$company_logo = str_replace("http://www.alexptech.com/replist/uploads/company_logo/", "", $data->u_company_logo);
								$user_detail['company_logo'] = base_url('public/profile_images/').$company_logo;
							}
							else
							{
								$user_detail['company_logo'] = '';
							}

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

    public function DeleteProfile() 
    {
    	$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$profile_post = file_get_contents("php://input");
			$decode_post = json_decode($profile_post);
			$current_date = date('Y-m-d');

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_type','Reps');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$data=array();
						$data['u_deactivate_date'] = $current_date;

						$update = $this->HM->update_profile($run->u_id,$data);

						if($update)
						{
							echo json_encode(array("status" => '1',"message" => 'Your account has been deactivated successfull !'));
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
					$this->db->where('u_type','Reps');
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

						if(!empty($decode_post->delivery_number))
						{
							$data['u_delivery_number'] = $decode_post->delivery_number;
						}
						else
						{
							$data['u_delivery_number'] = $run->u_delivery_number;
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
				$this->db->where('u_type','Reps');
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
							$upload = $image['file_name'];
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
						$upload = $run->u_image;
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
						    $image = $this->upload->data();
							$data1 = $image['file_name'];
						}
					}
					else
					{
						$data1 = $run->u_company_logo;
					}

					$data=array();
					$data['u_image'] = $upload;
					$data['u_company_logo'] = $data1;

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

    public function AddProducts() 
    {
        $authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST')
	 	{
	 		$user_id = $this->input->post('user_id');
	 		$name = strip_tags($this->input->post('name'));
	 		$category = strip_tags($this->input->post('category'));
	 		$price = strip_tags($this->input->post('price'));
	 		$stock = strip_tags($this->input->post('stock'));
	 		$packsize = strip_tags($this->input->post('packsize'));
	 		$description = strip_tags($this->input->post('description'));
	 		$barcode = strip_tags($this->input->post('barcode'));
	 		$item_weight = strip_tags($this->input->post('item_weight'));
	 		$item_ounce = strip_tags($this->input->post('item_ounce'));
	 		$image = strip_tags($this->input->post('image'));
	 		$multiple_image = strip_tags($this->input->post('multiple_image[]'));
	 		$upload='';

			if(!empty($user_id) && !empty($name) && !empty($category) && !empty($price) && !empty($stock) && !empty($packsize) && !empty($description) && !empty($barcode) && !empty($item_weight) && !empty($item_ounce))
			{
				$this->db->select('*');
				$this->db->where('u_id',$user_id);
				$select = $this->db->get('tbl_users');
				$run = $select->row();
				$num = $select->num_rows();

				if($num==1)
				{
					$this->db->select('*');
					$this->db->where('c_id',$category);
					$select1 = $this->db->get('tbl_category');
					$num1 = $select1->num_rows();

					if($num1!=0)
					{
						$config['upload_path']          = './public/product_image';
						$config['allowed_types']        = 'gif|jpg|png|jpeg';
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
							echo json_encode(array("status" => '0',"message" => 'Image should be gif, jpg, png, jpeg format !',"error"=>$error));
							exit();
						}

						$data=array();
						$data['p_name'] = $name;
						$data['p_category'] = $category;
						$data['p_image'] = $upload;
						$data['p_price'] = $price;
						$data['p_stock'] = $stock;
						$data['p_packsize'] = $packsize;
						$data['p_description'] = $description;
						$data['p_barcode'] = $barcode;
						$data['p_item_weight'] = $item_weight;
						$data['p_item_ounce'] = $item_ounce;
						$data['p_status'] = 0;
						$data['p_user_id'] = $run->u_id;

						$insert_id = $this->HM->add_product($data);

						// Multiple Images
						$count = count($_FILES['multiple_image']['name']);
		    
				      	for($i=0;$i<$count;$i++)
				      	{
					        if(!empty($_FILES['multiple_image']['name'][$i]))
					        {
						        $_FILES['file']['name'] = $_FILES['multiple_image']['name'][$i];
						        $_FILES['file']['type'] = $_FILES['multiple_image']['type'][$i];
						        $_FILES['file']['tmp_name'] = $_FILES['multiple_image']['tmp_name'][$i];
						        $_FILES['file']['error'] = $_FILES['multiple_image']['error'][$i];
						        $_FILES['file']['size'] = $_FILES['multiple_image']['size'][$i];
						  
						        $config['upload_path'] = './public/product_image/'; 
						        $config['allowed_types'] = 'jpg|jpeg|png|gif';
						        $config['max_size'] = '';
						        $config['file_name'] = $_FILES['multiple_image']['name'][$i];
						   
						        $this->load->library('upload',$config); 
						        if($this->upload->do_upload('file'))
						        {
						            $uploadData = $this->upload->data();
						            $filename['pi_image'] = $uploadData['file_name'];
						            $filename['pi_product_id'] = $insert_id;

						            $this->db->insert('tbl_product_image',$filename);
						        }
					        }
					    }

						if($insert_id)
						{  
							echo json_encode(array("status" => '1',"message" => 'Your product added successfully !',"product_id"=>$insert_id));
						} 
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The category id is not match with our database !'));
					}
				}	
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'The user id, name, price, stock, packsize, description, item weight, item ounce and barcode fields are required !'));
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

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_type','Reps');
					$this->db->where('u_id',$decode_post->user_id);
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$this->db->select('*');
						$this->db->where('p_user_id',$run->u_id);
						$this->db->where('p_status','0');
						$select = $this->db->get('tbl_product');
						$product = $select->result_array();
						$num = $select->num_rows();

						if($num>0)
						{
							foreach($product as $values)
							{
								$product=array();
								$product['id'] = $values['p_id'];
								$product['name'] = $values['p_name'];
								$product['price'] = $values['p_price'];
								$product['stock'] = $values['p_stock'];
								$product['packsize'] = $values['p_packsize'];
								$product['description'] = $values['p_description'];

								if(!empty($values['p_image']))
								{
									$product['image'] = base_url('public/product_image/').$values['p_image'];
								}
								else
								{
									$product['image'] = "";
								}

								$product['barcode'] = $values['p_barcode'];
								$product['item_weight'] = $values['p_item_weight'];
								$product['item_ounce'] = $values['p_item_ounce'];

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
							echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
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
					$this->db->where('u_type','Reps');
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

						if($num>0)
						{
							$product=array();
							$product['id'] = $product_data->p_id;
							$product['name'] = $product_data->p_name;
							$product['price'] = $product_data->p_price;
							$product['stock'] = $product_data->p_stock;
							$product['packsize'] = $product_data->p_packsize;
							$product['description'] = $product_data->p_description;

							if(!empty($product_data->p_image))
							{
								$product['image'] = base_url('public/product_image/').$product_data->p_image;
							}
							else
							{
								$product['image'] = "";
							}

							$product['barcode'] = $product_data->p_barcode;
							$product['item_weight'] = $product_data->p_item_weight;
							$product['item_ounce'] = $product_data->p_item_ounce;

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
							echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
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

    public function product_multiple_image()
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
					$this->db->where('u_type','Reps');
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

						if($num>0)
						{
    						$multiple_image = $this->HM->multiple_image($product_data->p_id);

    						if(!empty($multiple_image))
    						{
								foreach($multiple_image as $b)
								{
									
									$product['multiple_image'] = base_url('public/product_image/').$b['pi_image'];
									array_push($product_list,$product);
								}

								if($product_list)
								{  
									echo json_encode(array("status" => '1',"message" => 'Product Multiple Image',"product_detail"=>$product_list));
								} 
								else
								{
									echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
								}
							}
							else
							{
								echo json_encode(array("status" => '1',"message" => 'Product Multiple Image',"product_detail"=>[]));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'The product id is not match with our database !'));
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

    public function DeleteProduct() 
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
					$this->db->where('u_type','Reps');
					$select = $this->db->get('tbl_users');
					$run = $select->row();
					$num = $select->num_rows();

					if($num==1)
					{
						$this->db->select('*');
						$this->db->where('p_id',$decode_post->product_id);
						$select = $this->db->get('tbl_product');
						$product_data = $select->row();
						$num = $select->num_rows();

						if($num>0)
						{
							$data=array();
							$data['p_status'] = 1;

							$update = $this->HM->update_product($decode_post->product_id,$data);

							if($update)
							{  
								echo json_encode(array("status" => '1',"message" => 'Product deleted successfully'));
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

    public function UpdateProduct() 
    {
        $authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST')
	 	{
	 		$user_id = strip_tags($this->input->post('user_id'));
	 		$product_id = strip_tags($this->input->post('product_id'));
	 		$name = strip_tags($this->input->post('name'));
	 		$price = strip_tags($this->input->post('price'));
	 		$stock = strip_tags($this->input->post('stock'));
	 		$packsize = strip_tags($this->input->post('packsize'));
	 		$description = strip_tags($this->input->post('description'));
	 		$image = strip_tags($this->input->post('image'));
	 		$barcode = strip_tags($this->input->post('barcode'));
	 		$item_weight = strip_tags($this->input->post('item_weight'));
	 		$item_ounce = strip_tags($this->input->post('item_ounce'));
	 		$upload='';
			
			if(!empty($user_id) && !empty($name) && !empty($price) && !empty($stock) && !empty($packsize) && !empty($description) && !empty($product_id) && !empty($barcode) && !empty($item_weight) && !empty($item_ounce))
			{
				$this->db->select('*');
				$this->db->where('u_id',$user_id);
				$select = $this->db->get('tbl_users');
				$run = $select->row();
				$num = $select->num_rows();

				$product_num = $this->HM->count_product($product_id);
				$edit_product = $this->HM->edit_product($product_id);

				if($product_num==1)
				{
					if($num==1)
					{
						if(!empty($_FILES['image']['name']))
						{
							$config['upload_path']          = './public/product_image';
							$config['allowed_types']        = 'gif|jpg|png|jpeg';
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
								echo json_encode(array("status" => '0',"message" => 'Image should be gif, jpg, png, jpeg format !',"error"=>$error));
								exit();
							}
						}
						else
						{
							$upload1 = $edit_product->p_image;
						}
								
						$data=array();

						if(empty($name))
						{
							$data['p_name'] = $edit_product->p_name;
						}
						else
						{
							$data['p_name'] = $name;
						}

						if(empty($upload))
						{
							$data['p_image'] = $upload1;
						}
						else
						{
							$data['p_image'] = $upload;
						}

						if(empty($price))
						{
							$data['p_price'] = $edit_product->p_price;
						}
						else
						{
							$data['p_price'] = $price;
						}

						if(empty($stock))
						{
							$data['p_stock'] = $edit_product->p_stock;
						}
						else
						{
							$data['p_stock'] = $stock;
						}
						
						if(empty($packsize))
						{
							$data['p_packsize'] = $edit_product->p_packsize;
						}
						else
						{
							$data['p_packsize'] = $packsize;
						}
						
						if(empty($description))
						{
							$data['p_description'] = $edit_product->p_description;
						}
						else
						{
							$data['p_description'] = $description;
						}

						if(empty($barcode))
						{
							$data['p_barcode'] = $edit_product->p_barcode;
						}
						else
						{
							$data['p_barcode'] = $barcode;
						}

						if(empty($item_weight))
						{
							$data['p_item_weight'] = $edit_product->p_item_weight;
						}
						else
						{
							$data['p_item_weight'] = $item_weight;
						}

						if(empty($item_ounce))
						{
							$data['p_item_ounce'] = $edit_product->p_item_ounce;
						}
						else
						{
							$data['p_item_ounce'] = $item_ounce;
						}

						$data['p_status'] = 0;
						$data['p_user_id'] = $run->u_id;

						$update = $this->HM->update_product($product_id,$data);

						if($update)
						{  
							echo json_encode(array("status" => '1',"message" => 'Your product updated successfully !'));
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
					echo json_encode(array("status" => '0',"message" => 'The product id is not match with our database !'));
				}
			}
			else
			{
				echo json_encode(array("status" => '0',"message" => 'The user id, product id, name, price, stock, pack size and description fields are required !'));
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
				$this->db->where('u_type','Reps');
				$select = $this->db->get('tbl_users');
				$num = $select->num_rows();
				$run = $select->row();

				if($num>0)
				{
					$config['upload_path']          = './public/doc_files';
					$config['allowed_types']        = 'gif|jpg|png|jpeg';
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
						echo json_encode(array("status" => '0',"message" => 'Image should be gif, jpg, png, jpeg format !',"error"=>$error));
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
					$this->db->where('u_type','Reps');
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
							echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
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
     	$this->verify_token();
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
					$this->db->where('u_type','Reps');
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
							echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
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
					$this->db->where('u_type','Reps');
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
							echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
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

    public function BuyersList()
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$buyer_post = file_get_contents("php://input");
			$decode_post = json_decode($buyer_post);
			$buyers_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id))
				{
					$rep_num = $this->HM->check_rep_id($decode_post->user_id);

					if($rep_num!=0)
					{
						if(!empty($decode_post->keyword))
						{
							$this->db->select('*');
							$this->db->where('u_type','Buyer');
							$this->db->like('u_first_name',$decode_post->keyword);
							$select1 = $this->db->get('tbl_users');
							$data1 = $select1->result_array();
							$num1 = $select1->num_rows();
						}
						else
						{
							$this->db->select('*');
							$this->db->where('u_type','Buyer');
							$select1 = $this->db->get('tbl_users');
							$data1 = $select1->result_array();
							$num1 = $select1->num_rows();
						}

						if($num1!=0)
						{
							foreach($data1 as $values)
							{	
								$this->db->where('i_flag','1');
								$this->db->where('i_sender_id',$decode_post->user_id);
								$this->db->where('i_receiver_id',$values['u_id']);
								$select = $this->db->get('tbl_invitation');
								$sql = $select->result_array();
								$num = $select->num_rows();

								foreach($sql as $key)
								{
									if($values['u_id']!=$decode_post->user_id)
									{
										$buyers['id'] = $values['u_id'];
										$buyers['user_code'] = $values['u_user_code']; 
									  	$buyers['first_name'] = $values['u_first_name']; 
										$buyers['last_name'] = $values['u_last_name']; 
										$buyers['email'] = $values['u_email'];
										$buyers['contact_number'] = $values['u_mobile'];
										$buyers['company'] = $values['u_company'];
										$buyers['state'] = $values['u_state'];
										$buyers['city'] = $values['u_city'];
										$buyers['street_address'] = $values['u_street_address'];
										$buyers['postalcode'] = $values['u_postalcode'];
										$buyers['sales_position'] = $values['u_sales_position'];
										$buyers['area_cover'] = $values['u_area_cover'];
										$buyers['department'] = $values['u_department'];

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
								}
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
				if(!empty($decode_post->user_id))
				{
					$rep_num = $this->HM->check_rep_id($decode_post->user_id);

					if($rep_num!=0)
					{ 
						if(!empty($decode_post->keyword))
						{
							$this->db->select('*');
							$this->db->where('u_type','Reps');
							$this->db->like('u_first_name',$decode_post->keyword);
							$select1 = $this->db->get('tbl_users');
							$data1 = $select1->result_array();
							$num1 = $select1->num_rows();
						}
						else
						{
							$this->db->select('*');
							$this->db->where('u_type','Reps');
							$select1 = $this->db->get('tbl_users');
							$data1 = $select1->result_array();
							$num1 = $select1->num_rows();
						}

						if($num1!=0)
						{
							foreach($data1 as $values)
							{	
								if($values['u_id']!=$decode_post->user_id)
								{
									$this->db->where('i_flag','1');
									$this->db->where('i_sender_id',$decode_post->user_id);
									$this->db->where('i_receiver_id',$values['u_id']);
									$select = $this->db->get('tbl_invitation');
									$sql = $select->result_array();

									foreach($sql as $key)
									{
										$reps['id'] = $values['u_id'];
										$reps['user_code'] = $values['u_user_code']; 
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

										if(!empty($values['u_image']))
										{
											$reps['image'] = base_url('public/profile_images/').$values['u_image'];
										}
										else
										{
											$reps['image'] = '';
										}

										array_push($reps_list,$reps);
									}
								}
							}

							if($reps_list)
							{
								echo json_encode(array("status" => '1',"message" => 'reps List !',"reps_list" => $reps_list));
							}
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is no connection found !'));
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
                    $this->db->where('u_type','Reps');
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
	            if(!empty($decode_post->user_id)) 
	            {
	                $this->db->select('*');
	                $this->db->where('u_id',$decode_post->user_id);
	                $select = $this->db->get('tbl_users');
	                $run = $select->row();
	                $num = $select->num_rows();

	                if($num!=0)
	                {
		           	 	$this->db->select('*');
		           	 	$this->db->where('i_flag','0');
		           	 	$this->db->where('i_sender_id',$decode_post->user_id);
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
		                    	$user_detail['name'] = $sender_data->u_first_name;
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
								$user_detail['status'] = $sender_data->u_type;
		                    	$user_detail['message'] = $values['i_message'];

		                    	if(!empty($sender_data->u_image))
								{
									$user_detail['image'] = base_url('public/profile_images/').$sender_data->u_image;
								}
								else
								{
									$user_detail['image'] = '';
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
		            	    echo json_encode(array("status" => '1',"message" => 'There is no data found on database !',"invitation_list"=>[]));
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
					$this->db->where('u_type','Reps');
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
				                    	$list['name'] = $receiver_details->u_first_name.$receiver_details->u_last_name;
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
				                    	$list['name'] = $sender_details->u_first_name.$sender_details->u_last_name;
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
	                            echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
	                        }
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
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

                    	$update = $this->HM->update_invitation($decode_post->invitation_id,$data);

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
  	    $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
            $decode_post = json_decode(file_get_contents("php://input"));
            $date = date('Y-m-d h:i:s');

            if(count((array)$decode_post)==3)
            {
                if(!empty($decode_post->user_id) && !empty($decode_post->heading) && !empty($decode_post->notes)) 
                {
                    $this->db->select('*');
                    $this->db->where('u_type','Reps');
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
	                        echo json_encode(array("status" => '1',"message" => 'You send friend request successfully !',"notes_id"=>(string)$insert_id));
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
    	$this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
        	$decode_post = json_decode(file_get_contents("php://input"));
        	$notes_list = array();
            
        	if(count((array)$decode_post)==1)
        	{
	            if(!empty($decode_post->user_id)) 
	            {
	                $this->db->select('*');
	                $this->db->where('u_type','Reps');
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
	                        echo json_encode(array("status" => '0',"message" => 'No data Found'));
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
	                $this->db->where('u_type','Reps');
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
                    $this->db->where('u_type','Reps');
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
    	$this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$decode_post = json_decode(file_get_contents("php://input"));
			$notes_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->notes_id) && !empty($decode_post->user_id))
				{
					$this->db->select('*');
					$this->db->where('u_type','Reps');
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
							echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
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

	public function OrderDetails()
	{
		$authentication = $this->verify_token();
    	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$order_history_post = file_get_contents("php://input");
			$decode_post = json_decode($order_history_post);
			$order_history = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_rep_id($decode_post->user_id);

					if($user_num!=0)
					{
						if(!empty($decode_post->status))
						{
							$this->db->select("*");
							$this->db->where('o_rep_id',$decode_post->user_id);
							$this->db->where('o_flag',$decode_post->status);
							$select = $this->db->get('tbl_orders');
							$order_num = $select->num_rows();
							$orders = $select->result_array();
						}
						else
						{
							$this->db->select("*");
							$this->db->where('o_rep_id',$decode_post->user_id);
							$select = $this->db->get('tbl_orders');
							$order_num = $select->num_rows();
							$orders = $select->result_array();
						}

						if($order_num>0)
						{
							foreach($orders as $values)
							{
								$product = $this->HM->edit_product($values['o_product_id']);
								$profile = $this->HM->edit_profile($values['o_user_id']);

								if($values['o_flag'] == '1')
								{
								    $order_status = 'Order Pending';
								}
								elseif($values['o_flag'] == '2')
								{
								    $order_status = 'Order Canceled'; 
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
								$order['order_date'] = $values['o_order_date'];
								$order['expected_date'] = date('Y-m-d', strtotime($values['o_order_date']. ' + 5 days'));
								$order['buyer_name'] = $profile->u_first_name;
								$order['buyer_email'] = $profile->u_email;
								$order['buyer_mobile_number'] = $profile->u_mobile;
								$order['buyer_address'] = $profile->u_street_address.','.$profile->u_state.','.$profile->u_city.','.$profile->u_postalcode.','.$profile->u_country;

								array_push($order_history,$order);
							}

							echo json_encode(array("status" => '1',"message" => 'Order History !',"order"=>$order_history));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'There is no record found !'));
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

	public function OrderDetailsInfo()
	{
		$authentication = $this->verify_token();
    	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$order_history_post = file_get_contents("php://input");
			$decode_post = json_decode($order_history_post);
			$order_history = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->order_id))
				{
					$user_num = $this->HM->check_rep_id($decode_post->user_id);

					if($user_num!=0)
					{
						$this->db->select("*");
						$this->db->where('o_id',$decode_post->order_id);
						$select = $this->db->get('tbl_orders');
						$order_num = $select->num_rows();
						$orders = $select->result_array();

						if($order_num>0)
						{
							foreach($orders as $values)
							{
								$product = $this->HM->edit_product($values['o_product_id']);
								$profile = $this->HM->edit_profile($values['o_user_id']);

								if($values['o_flag'] == '1')
								{
								    $order_status = 'Order Pending';
								}
								elseif($values['o_flag'] == '2')
								{
								    $order_status = 'Order Canceled'; 
							 	}
								elseif($values['o_flag'] == '3') 
								{
								    $order_status = 'Order Completed/Delivered';
								}
								else
								{
									$order_status = '';
								}

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
								$order['order_date'] = $values['o_order_date'];
								$order['expected_date'] = date('Y-m-d', strtotime($values['o_order_date']. ' + 5 days'));
								$order['buyer_name'] = $profile->u_first_name;
								$order['buyer_email'] = $profile->u_email;
								$order['buyer_mobile_number'] = $profile->u_mobile;
								$order['buyer_address'] = $profile->u_street_address.','.$profile->u_state.','.$profile->u_city.','.$profile->u_postalcode.','.$profile->u_country;
								array_push($order_history,$order);
							}

							echo json_encode(array("status" => '1',"message" => 'Order History !',"order"=>$order_history));
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'The order id is not match with database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => 'failed',"message" => 'The user id is not match with database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The User id & Order Id fields are required !'));
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
					$user_num = $this->HM->check_rep_id($decode_post->user_id);

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
					$user_num = $this->HM->check_rep_id($decode_post->user_id);

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
					$rep_num = $this->HM->check_rep_id($decode_post->user_id);

					if($rep_num!=0)
					{ 
						$profile = $this->HM->edit_profile($decode_post->user_id);
            			$to_email = explode(",",$decode_post->email);
            
			            foreach($to_email as $values) 
			            {
			                $to = $values;
			                $subject = $decode_post->subject;
			                $txt = stripslashes($decode_post->message);
			                $headers = "From:".$profile->u_email. "\r\n";

			                mail($to,$subject,$txt,$headers);

			                $data = array();
			                $data['sm_user_id'] = $decode_post->user_id;
			                $data['sm_subject'] = $decode_post->subject;
			                $data['sm_message'] = $decode_post->message;
			                $data['sm_receiver_email'] = $values;
			                $data['sm_sender_email'] = $profile->u_email;
			                $data['sm_created_at'] = date('Y-m-d h:i:s');

			                $insert = $this->HM->send_mail($data);
			            }

			            if($insert)
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
				if(!empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_rep_id($decode_post->user_id);

					if($user_num!=0)
					{
						$this->db->where('c_id',$decode_post->category_id);
						$select = $this->db->get('tbl_category');
						$num_rows = $select->num_rows();

						if($num_rows!=0)
						{
							$this->db->select('*');
							$this->db->where('p_user_id',$decode_post->user_id);
							$this->db->where('p_status','0');
							$this->db->where('p_category',$decode_post->category_id);
							$this->db->join('tbl_category','tbl_category.c_id = tbl_product.p_category');
							$select = $this->db->get('tbl_product');
							$product = $select->result_array();
							$num = $select->num_rows();

							if($num>0)
							{
								foreach($product as $values)
								{
									$product=array();
									$product['id'] = $values['p_id'];
									$product['category'] = $values['c_name'];
									$product['name'] = $values['p_name'];
									$product['price'] = $values['p_price'];
									$product['stock'] = $values['p_stock'];
									$product['packsize'] = $values['p_packsize'];
									$product['description'] = $values['p_description'];
									$product['image'] = base_url('public/product_image/').$values['p_image'];
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
								echo json_encode(array("status" => '0',"message" => 'There is no data found on database !'));
							}
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'The category id is not match with our database !'));
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

    public function edit_company_info()
    {
    	$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$company_info_post = file_get_contents("php://input");
			$decode_post = json_decode($company_info_post);
			$info_list = array();

			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->user_id))
				{
					$user_num = $this->HM->check_rep_id($decode_post->user_id);

					if($user_num!=0)
					{
						$info = $this->HM->edit_company_info($decode_post->user_id);
						$profile = $this->HM->edit_profile($decode_post->user_id);

						$company_info=array();
						
						if(!empty($profile->u_company_logo))
						{
							$company_info['company_logo'] = base_url('public/profile_images/').$profile->u_company_logo;
						}
						else
						{
							$company_info['company_logo'] = '';
						}

						if(!empty($profile->u_website) && $profile->u_website!='null')
						{
							$company_info['website'] = $profile->u_website;
						}
						else
						{
							$company_info['website'] = '';
						}

						if(!empty($info->ci_about))
						{
							$company_info['about'] = $info->ci_about;
						}
						else
						{
							$company_info['about'] = '';
						}

						if(!empty($info->ci_monday))
						{
							$company_info['monday'] = $info->ci_monday;
						}
						else
						{
							$company_info['monday'] = '';
						}

						if(!empty($info->ci_tuesday))
						{
							$company_info['tuesday'] = $info->ci_tuesday;
						}
						else
						{
							$company_info['tuesday'] = '';
						}

						if(!empty($info->ci_wednesday))
						{
							$company_info['wednesday'] = $info->ci_wednesday;
						}
						else
						{
							$company_info['wednesday'] = '';
						}

						if(!empty($info->ci_thursday))
						{
							$company_info['thursday'] = $info->ci_thursday;
						}
						else
						{
							$company_info['thursday'] = '';
						}

						if(!empty($info->ci_friday))
						{
							$company_info['friday'] = $info->ci_friday;
						}
						else
						{
							$company_info['friday'] = '';
						}

						if(!empty($info->ci_saturday))
						{
							$company_info['saturday'] = $info->ci_saturday;
						}
						else
						{
							$company_info['saturday'] = '';
						}

						if(!empty($info->ci_sunday))
						{
							$company_info['sunday'] = $info->ci_sunday;
						}
						else
						{
							$company_info['sunday'] = '';
						}
						
						array_push($info_list,$company_info);

						if($info_list)
						{
							echo json_encode(array("status" => '1',"message" => 'Company Info',"company_info"=>$info_list));
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

    public function update_company_info() 
	{
		$authentication = $this->verify_token();
		if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
		{
			$profile_post = file_get_contents("php://input");
			$decode_post = json_decode($profile_post);

			if(count((array)$decode_post)==9)
			{
				if(!empty($decode_post->user_id)) 
				{
					$user_num = $this->HM->check_rep_id($decode_post->user_id);

					if($user_num!=0)
					{
						$info = $this->HM->edit_company_info($decode_post->user_id);
						$num = $this->HM->check_company_info($decode_post->user_id);

						if($num!='')
						{
							$data = array();
							if(!empty($decode_post->about))
							{
								$data['ci_about'] = $decode_post->about;
							}
							else
							{
								$data['ci_about'] = $info->ci_about;
							}

							if(!empty($decode_post->monday))
							{
								$data['ci_monday'] = $decode_post->monday;
							}
							else
							{
								$data['ci_monday'] = $info->ci_monday;
							}

							if(!empty($decode_post->tuesday))
							{
								$data['ci_tuesday'] = $decode_post->tuesday;
							}
							else
							{
								$data['ci_tuesday'] = $info->ci_tuesday;
							}

							if(!empty($decode_post->wednesday))
							{
								$data['ci_wednesday'] = $decode_post->wednesday;
							}
							else
							{
								$data['ci_wednesday'] = $info->ci_wednesday;
							}

							if(!empty($decode_post->thursday))
							{
								$data['ci_thursday'] = $decode_post->thursday;
							}
							else
							{
								$data['ci_thursday'] = $info->ci_thursday;
							}

							if(!empty($decode_post->friday))
							{
								$data['ci_friday'] = $decode_post->friday;
							}
							else
							{
								$data['ci_friday'] = $info->ci_friday;
							}

							if(!empty($decode_post->saturday))
							{
								$data['ci_saturday'] = $decode_post->saturday;
							}
							else
							{
								$data['ci_saturday'] = $info->ci_saturday;
							}

							if(!empty($decode_post->sunday))
							{
								$data['ci_sunday'] = $decode_post->sunday;
							}
							else
							{
								$data['ci_sunday'] = $info->ci_sunday;
							}

							$update = $this->HM->update_company_info($decode_post->user_id,$data);
						}
						else
						{
							$data=array();
							$data['ci_about'] = $decode_post->about;
							$data['ci_tuesday'] = $decode_post->tuesday;
							$data['ci_wednesday'] = $decode_post->wednesday;
							$data['ci_thursday'] = $decode_post->thursday;
							$data['ci_friday'] = $decode_post->friday;
							$data['ci_saturday'] = $decode_post->saturday;
							$data['ci_sunday'] = $decode_post->sunday;
							$data['ci_user_id'] = $decode_post->user_id;

							$update = $this->HM->add_company_info($data);
						}

						if($update)
						{
							echo json_encode(array("status" => '1',"message" => 'Company info updated successfully !'));
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

    public function reports()
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$report_post = file_get_contents("php://input");
			$decode_post = json_decode($report_post);
			$order_history = array(); $total_sales=0; $total_year_sales=0; $total_sale=0;

			if(count((array)$decode_post)==4)
			{
				if(!empty($decode_post->buyer_id))
				{
					$rep_num = $this->HM->check_rep_id($decode_post->buyer_id);

					if($rep_num!=0)
					{
						if(!empty($decode_post->from_date) && !empty($decode_post->to_date))
						{
							$this->db->select('*');
							$this->db->order_by('o_id','desc');
							$this->db->where('o_rep_id',$decode_post->buyer_id);
							$this->db->where("o_order_date BETWEEN '{$decode_post->from_date}' AND '{$decode_post->to_date}'");
							$select = $this->db->get('tbl_orders');
							$num = $select->num_rows();
							$orders = $select->result_array();
						}
						elseif(!empty($decode_post->year))
						{
							$to_year =  date("Y", strtotime($decode_post->year.'+1 year'));
							$from_year =  date("Y", strtotime($decode_post->year.'-1'));

							$this->db->select('*');
							$this->db->order_by('o_id','desc');
							$this->db->where('o_rep_id',$decode_post->buyer_id);
							$this->db->where("o_order_date BETWEEN '{$from_year}' AND '{$to_year}'");
							$select = $this->db->get('tbl_orders');
							$num = $select->num_rows();
							$orders = $select->result_array();
						}
						else
						{
							$this->db->select('*');
							$this->db->order_by('o_id','desc');
							$this->db->where('o_rep_id',$decode_post->buyer_id);
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
								array_push($order_history,$order);
							}

							// This month sales
							$current_date = date('m');
							$to_m =  date("m", strtotime($current_date.'+1 month'));
							$from_m =  date("m", strtotime($current_date.'-1'));

							$this->db->select('*'); 
							$this->db->order_by('o_id','desc');
							$this->db->where('o_rep_id',$decode_post->buyer_id);
							$this->db->where("o_created_at BETWEEN '{$from_m}' AND '{$to_m}'");
							$select = $this->db->get('tbl_orders');
							$num = $select->num_rows();
							$orders = $select->result_array();

							foreach($orders as $values)
							{
								$sales=$values['o_total_amount'];
								$total_sales+=$sales;
							}

							// This year sales
							$current_date = date('Y');
							$to_y =  date("Y-01-1", strtotime($current_date.'+1 year'));
							$from_y =  date("Y-01-1", strtotime($current_date.'-1'));

							$this->db->select('*');
							$this->db->order_by('o_id','desc');
							$this->db->where('o_rep_id',$decode_post->buyer_id);
							$this->db->where("o_created_at BETWEEN '{$from_y}' AND '{$to_y}'");
							$select = $this->db->get('tbl_orders');
							$num = $select->num_rows();
							$orders = $select->result_array();

							foreach($orders as $values)
							{
								$sales=$values['o_total_amount'];
								$total_year_sales+=$sales;
							}

							// Total Sales
							$this->db->select('*');
							$this->db->order_by('o_id','desc');
							$this->db->where('o_rep_id',$decode_post->buyer_id);
							$select = $this->db->get('tbl_orders');
							$num = $select->num_rows();
							$orders = $select->result_array();

							foreach($orders as $values)
							{
								$sales=$values['o_total_amount'];
								$total_sale+=$sales;
							}

							echo json_encode(array("status" => '1',"message" => 'Order History !',"order"=>$order_history,"this_month"=>$total_sales,"this_year"=>$total_year_sales,"total_sale"=>$total_sale));
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
					echo json_encode(array("status" => '0',"message" => 'The rep id, month and year fields are required !'));
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

    public function get_delivery_number()
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$delivery_number_post = file_get_contents("php://input");
			$decode_post = json_decode($delivery_number_post);
			$number = array();
		
			if(count((array)$decode_post)==1)
			{
				if(!empty($decode_post->rep_id))
				{
					$rep_num = $this->HM->check_rep_id($decode_post->rep_id);

					if($rep_num!=0)
					{
						$profile = $this->HM->edit_profile($decode_post->rep_id);

						$delivery_number=array();
						$delivery_number['delivery_number'] = $profile->u_delivery_number;
						array_push($number,$delivery_number);

						if(!empty($number))
						{
							echo json_encode(array("status" => '1',"message" => 'Delivery Agent Number !',"number"=>$number));
						}
						else
						{
							echo json_encode(array("status" => '1',"message" => 'Delivery Agent Number !',"number"=>''));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The rep id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The rep id field is required !'));
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

    public function share_order_details()
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$delivery_number_post = file_get_contents("php://input");
			$decode_post = json_decode($delivery_number_post);
		
			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->order_id) && !empty($decode_post->rep_id))
				{
					$rep_num = $this->HM->check_rep_id($decode_post->rep_id);

					if($rep_num!=0)
					{
						$order_num = $this->HM->check_order_id($decode_post->order_id);

						if($order_num!=0)
						{	
							$data=array();
							$data['so_order_id'] = $decode_post->order_id;
							$data['so_rep_id'] = $decode_post->rep_id;
							$data['so_otp'] = random_int(1000,9999);

							$insert = $this->HM->add_share_order_details($data);

							if($insert)
							{
								echo json_encode(array("status" => '1',"message" => 'Order details information shared successfully !'));
							} 
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}	
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'The order id is not match with our database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The rep id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The rep id field is required !'));
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
					$rep_num = $this->HM->check_rep_id($decode_post->user_id);

					if($rep_num!=0)
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

	public function get_buyer_details()
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$user_detail_post = file_get_contents("php://input");
			$decode_post = json_decode($user_detail_post);
			$detail_list = array();

			if(count((array)$decode_post)==2)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->buyer_id))
				{
					$rep_num = $this->HM->check_rep_id($decode_post->user_id);
					
					if($rep_num!=0)
					{
						$buyer_num = $this->HM->check_buyer_id($decode_post->buyer_id);

						if($buyer_num!=0)
						{
							$profile = $this->HM->edit_profile($decode_post->buyer_id);
							$user_num = $this->HM->check_buyer_id($decode_post->buyer_id);

							if($user_num!=0)
							{
								// Check invitation 
								$this->db->where('i_flag','1');
								$this->db->where('i_sender_id',$decode_post->user_id);
								$this->db->where('i_receiver_id',$profile->u_id);
								$select = $this->db->get('tbl_invitation');
								$connection_num = $select->num_rows();

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

								if($connection_num==0)
								{
									$detail['connection_status'] = '0';
								}
								else
								{
									$detail['connection_status'] = '1';
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
								echo json_encode(array("status" => '1',"message" => 'Buyer detail',"detail_list"=>$detail_list));
							} 
							else
							{
								echo json_encode(array("status" => '0',"message" => 'There is something went wrong !'));
							}		
						}
						else
						{
							echo json_encode(array("status" => '0',"message" => 'The buyer id is not match with our database !'));
						}
					}
					else
					{
						echo json_encode(array("status" => '0',"message" => 'The user id is not match with our database !'));
					}
				}
				else
				{
					echo json_encode(array("status" => '0',"message" => 'The user id and buyer id fields are required !'));
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

    public function help_center()
    {
    	$authentication = $this->verify_token();
       	if($this->input->server('REQUEST_METHOD') == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json')
	 	{
			$help_center_post = file_get_contents("php://input");
			$decode_post = json_decode($help_center_post);
		
			if(count((array)$decode_post)==3)
			{
				if(!empty($decode_post->user_id) && !empty($decode_post->subject) && !empty($decode_post->message))
				{
					$rep_num = $this->HM->check_rep_id($decode_post->user_id);

					if($rep_num!=0)
					{
						$data = array();
						$data['hc_user_id'] = $decode_post->user_id;
						$data['hc_subject'] = $decode_post->subject;
						$data['hc_message'] = $decode_post->message;
						$data['hc_created_at'] = date('Y-m-d h:i:s');

						$insert = $this->HM->help_center($data);

						if($insert)
						{
							echo json_encode(array("status" => '1',"message" => 'Your enquiry submitted successfully !'));
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
					echo json_encode(array("status" => '0',"message" => 'The user id, subject and message fields are required !'));
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