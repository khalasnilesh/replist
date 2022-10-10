<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{	
		parent:: __construct();
		$this->load->model('Admin_model','AM');
		$this->load->library('form_validation');
		$this->load->helper('security');
	}

	public function index()
	{
		$this->load->view('admin/index');
	}

	public function login()
	{
		if($this->input->post('login'))
		{	
			$this->form_validation->set_rules('email','Email','required|trim|valid_email');
			$this->form_validation->set_rules('password','Password','required|strip_tags|trim|min_length[6]');

			if($this->form_validation->run())
			{
				$email = $this->security->xss_clean($this->input->post('email'));
				$password = $this->security->xss_clean($this->input->post('password'));
				
				$this->db->select('*');
				$this->db->where('email',$email);
				$this->db->where('password',sha1($password));
				$select = $this->db->get('admin');
				$num = $select->row();
				$res = $select->num_rows();

				if($res==1)
				{
					$this->session->set_userdata('a_id',$num->id);
					redirect(base_url('dashboard'));
				}
				else
				{	
					$this->session->set_flashdata('danger','The email & password you entered is incorrect !');
					redirect(base_url('ReplistAdmin'));
				}
			}	
			else
			{
				$this->session->set_flashdata('danger','The email & password you entered is not valid !');
				redirect(base_url('ReplistAdmin'));
			}
		}
		else
		{
			$this->load->view('admin/index');
		}
	}

	public function logout()
	{
		$this->admin_access();
		$this->session->unset_userdata('a_id');
		redirect(base_url('ReplistAdmin'));
	}

	public function admin_access()
	{
		if(empty($this->session->userdata('a_id')))
		{
			redirect(base_url('ReplistAdmin'));
		}
	}

	public function dashboard()
	{
		$this->admin_access();

		//total Reps
		$data['reps'] = $this->AM->reps_list();

		//total Buyers
		$data['buyer'] = $this->AM->buyer_list();

		//orders list
		$data['order'] = $this->AM->order_list();

		//total orders
		$data['orders'] = $this->db->count_all('tbl_orders');

		$this->load->view('admin/dashboard',$data);
	}

	public function adminprofile()
	{
		$this->admin_access();
		$id = $this->session->userdata('a_id');
		$data['profile'] = $this->AM->admin_profile($id);
		$this->load->view('admin/admin-profile',$data);
	}

	public function editadminprofile()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		$data['profile'] = $this->AM->admin_profile($id);
		$this->load->view('admin/editadmin-profile',$data);
	}

	public function updateadminprofile()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		if($this->input->post('update'))
		{
			$this->form_validation->set_rules('email','Email','required|trim|valid_email');
			$this->form_validation->set_rules('name','Name','required|strip_tags|trim');
			$this->form_validation->set_rules('mobile','Mobile','required|strip_tags|trim|min_length[10]');
			$this->form_validation->set_rules('gender','Gender','required|strip_tags|trim');

			if(empty($_FILES['image']['name']))
			{
			    $this->form_validation->set_rules('image','Image','required|trim');
			}

			if($this->form_validation->run())
			{
				$email = $this->security->xss_clean($this->input->post('email'));
				$name = $this->security->xss_clean($this->input->post('name'));
				$mobile = $this->security->xss_clean($this->input->post('mobile'));
				$gender = $this->security->xss_clean($this->input->post('gender'));
				$upload = '';

				$config['upload_path'] = './public/admin/images/admin';
				$config['allowed_types'] = 'jpg|png|jpeg|gif';
				$config['max_size'] = '';
				$config['max_height'] = '';
				$config['max_width'] = '';
				
				$this->load->library('upload',$config);

				if(!empty($_FILES['image']['name']))
				{
					if(!$this->upload->do_upload('image'))
					{
						$this->session->set_flashdata('danger','Please Upload Required format Image!!');
						$data['profile'] = $this->AM->admin_profile($id);
						$this->load->view('admin/editadmin-profile',$data);
					}   
					else
					{
						$image1 = $this->upload->data();
						$upload = $image1['file_name'];
					}
				}
				else
				{
					$upload = $this->input->post('image');
				}

				$data = array();
				$data['name'] = $name;
				$data['email'] = $email;
				$data['mobile'] = $mobile;
				$data['gender'] = $gender;
				$data['image'] = $upload;

				$this->AM->update_admin($id,$data);
				$this->session->set_flashdata('success','Admin Profile Updated Successfully!!');
				$data['profile'] = $this->AM->admin_profile($id);
				$this->load->view('admin/editadmin-profile',$data);
			}
			else
			{
				$this->session->set_flashdata('danger','Please fill Required Fields !!');
				$data['profile'] = $this->AM->admin_profile($id);
				$this->load->view('admin/editadmin-profile',$data);
			}
		}
		else
		{
			$this->admin_access();
			$data['profile'] = $this->AM->admin_profile($id);
			$this->load->view('admin/editadmin-profile',$data);
		}
	}

	public function password()
	{
		$this->admin_access();
		$id = $this->session->userdata('a_id');
		$data['profile'] = $this->AM->admin_profile($id);
		$this->load->view('admin/password',$data);
	}

	public function changepassword()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		if($this->input->post('update'))
		{
			$this->form_validation->set_rules('old','Old Password','required|strip_tags|trim|min_length[5]');
			$this->form_validation->set_rules('new','New Password','required|strip_tags|trim|min_length[6]');
			$this->form_validation->set_rules('confirm','Confirm Password','required|strip_tags|trim|min_length[6]|matches[new]');

			if($this->form_validation->run()== TRUE)
			{
				$oldpassword = $this->security->xss_clean($this->input->post('old'));
				$newpassword = $this->security->xss_clean($this->input->post('new'));
				$confirmpassword = $this->security->xss_clean($this->input->post('confirm'));

				$data = $this->AM->admin_profile($id);

				if(sha1($oldpassword) == $data->password)
				{
					$res=array();
					$res['password'] = sha1($newpassword);
					// $res['a_decrypt_password'] = $newpassword;

					$this->AM->change_password($id,$res);
					$this->session->set_flashdata('success','Your password changed successfully !');
					redirect(base_url('password'));
				}
				else
				{
					$this->session->set_flashdata('danger','You Entered wrong password !');
					redirect(base_url('password'));
				}
			}
			else
			{
				$this->admin_access();
				$id = $this->session->userdata('a_id');
				$data['profile'] = $this->AM->admin_profile($id);
				$this->load->view('admin/password',$data);
			}
		}
		else
		{
			$this->admin_access();
			redirect(base_url('dashboard'));
		}		
	}

	public function reps()
	{
		$this->admin_access();
		$data['reps'] = $this->AM->reps_list();
		$this->load->view('admin/reps',$data);
	}

	public function viewrep()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		$data['view'] = $this->AM->view_rep($id);
		$this->load->view('admin/view-rep',$data);
	}

	public function buyer()
	{
		$this->admin_access();
		$data['buyer'] = $this->AM->buyer_list();
		$this->load->view('admin/buyer',$data);
	}

	public function viewbuyer()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		$data['view'] = $this->AM->view_buyer($id);
		$this->load->view('admin/view-buyer',$data);
	}

	public function report()
	{
		$this->admin_access();
		$data['order'] = $this->AM->order_list();
		$this->load->view('admin/report',$data);
	}

	public function document()
	{
		$this->admin_access();
		$data['doc'] = $this->AM->doc_list();
		$this->load->view('admin/document',$data);
	}

	public function viewdocument()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		$data['view'] = $this->AM->view_document($id);
		$this->load->view('admin/view-document',$data);
	}

	public function helpsupport()
	{
		$this->admin_access();
		$data['query'] = $this->AM->help_support();
		$this->load->view('admin/help-support',$data);
	}

	public function supportreply()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		if($this->input->post('submit'))
		{
			$this->form_validation->set_rules('answer','Answer','required');

			if($this->form_validation->run())
			{
				$answer = $this->security->xss_clean($this->input->post('answer'));

				$data = array();
				$data['q_answer'] = $answer;

				$update = $this->AM->query_reply($id,$data);

				if($update)
				{
					$this->session->set_flashdata('success','Answer Updated Successfully!! ');
					$this->admin_access();
					$data['reply'] = $this->AM->query($id);
					$this->load->view('admin/support-reply',$data);
				}
			}
			else
			{
				$this->session->set_flashdata('danger','Please fill the Required Field!!');
				$this->admin_access();
				$data['reply'] = $this->AM->query($id);
				$this->load->view('admin/support-reply',$data);
			}
		}
		else
		{
			$this->admin_access();
			$data['reply'] = $this->AM->query($id);
			$this->load->view('admin/support-reply',$data);
		}
	}

	public function contact()
	{
		$this->admin_access();
		$this->load->view('admin/contact');
	}

	public function contactreply()
	{
		$this->admin_access();
		$this->load->view('admin/contactreply');
	}

	public function salesreport()
	{
		$this->admin_access();
		$this->load->view('admin/sales-report');
	}
	
	public function purchasereport()
	{
		$this->admin_access();
		$this->load->view('admin/purchase-report');
	}

	public function orderhistory()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		$data['id'] = $id;
		$data['orders'] = $this->AM->order_list();
		$this->load->view('admin/order-history',$data);	
	}

	public function vieworder()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		$data['view'] = $this->AM->view_order($id);
		$this->load->view('admin/view-orderhistory',$data);
	}

	public function reward()
	{
		$this->admin_access();
		$this->load->view('admin/reward');
	}

	public function category()
	{
		$this->admin_access();
		$data['category'] = $this->AM->category();
		$this->load->view('admin/category',$data);
	}

	public function addcategory()
	{
		$this->admin_access();
		if($this->input->post('submit'))
		{
			$this->form_validation->set_rules('name','Name','required|trim');
			$this->form_validation->set_rules('status','Status','required');
			$upload = "";

			if($this->form_validation->run())
			{
				$name = $this->security->xss_clean($this->input->post('name'));
				$status = $this->security->xss_clean($this->input->post('status'));
				$upload = '';

				$config['upload_path'] = './public/category';
				$config['allowed_types'] = 'jpg|png|jpeg|gif';
				$config['max_size'] = '';
				$config['max_height'] = '';
				$config['max_width'] = '';
				
				$this->load->library('upload',$config);

				if(!empty($_FILES['image']['name']))
				{
					if(!$this->upload->do_upload('image'))
					{
						$this->session->set_flashdata('danger','Please Upload Required format Image!!');
						$this->load->view('admin/add-category');
					}   
					else
					{
						$image1 = $this->upload->data();
						$upload = $image1['file_name'];
					}
				}
				else
				{
					$upload = $this->input->post('image');
				}

				$data = array();
				$data['c_name'] = $name;
				$data['c_status'] = $status;
				$data['c_image'] = $upload;

				$add = $this->AM->add_category($data);

				if($add)
				{
					$this->session->set_flashdata('success','Category Added Successfuly!!');
					$this->load->view('admin/add-category');
				}
			}
			else
			{
				$this->session->set_flashdata('danger','Fill all the Required Fields');
				$this->load->view('admin/add-category');
			}
		}
		else
		{
			$this->load->view('admin/add-category');
		}
	}

	public function editcategory()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		if($this->input->post('update'))
		{
			$this->form_validation->set_rules('name','Name','required|trim');
			$this->form_validation->set_rules('status','Status','required');

			if(empty($_FILES['image']['name']))
			{
			    $this->form_validation->set_rules('image','Image','required|trim');
			}

			if($this->form_validation->run())
			{
				$name = $this->security->xss_clean($this->input->post('name'));
				$status = $this->security->xss_clean($this->input->post('status'));
				$upload = '';

				$config['upload_path'] = './public/category';
				$config['allowed_types'] = 'jpg|png|jpeg|gif';
				$config['max_size'] = '';
				$config['max_height'] = '';
				$config['max_width'] = '';
				
				$this->load->library('upload',$config);

				if(!empty($_FILES['image']['name']))
				{
					if(!$this->upload->do_upload('image'))
					{
						$this->session->set_flashdata('danger','Please Upload Required format Image!!');
						$data['view'] = $this->AM->edit_category($id);
						$this->load->view('admin/edit-category',$data);
					}   
					else
					{
						$image1 = $this->upload->data();
						$upload = $image1['file_name'];
					}
				}
				else
				{
					$upload = $this->input->post('image');
				}

				$data = array();
				$data['c_name'] = $name;
				$data['c_status'] = $status;
				$data['c_image'] = $upload;

				$update = $this->AM->update_category($data,$id);

				if($update)
				{
					$this->session->set_flashdata('success','Category Updated Successfuly!!');
					$data['view'] = $this->AM->edit_category($id);
					$this->load->view('admin/edit-category',$data);
				}
			}
			else
			{
				$this->session->set_flashdata('danger','Fill all the Required Fields');
				$data['view'] = $this->AM->edit_category($id);
				$this->load->view('admin/edit-category',$data);
			}

		}
		else
		{
			$data['view'] = $this->AM->edit_category($id);
			$this->load->view('admin/edit-category',$data);
		}
	}

	public function banner()
	{
		$this->admin_access();
		$data['banner'] = $this->AM->banner();
		$this->load->view('admin/banner',$data);
	}

	public function addbanner()
	{
		$this->admin_access();
		if($this->input->post('submit'))
		{
			$this->form_validation->set_rules('user','User','required|trim');
			$this->form_validation->set_rules('title','Title','required');

			if(empty($_FILES['image']['name']))
			{
			    $this->form_validation->set_rules('image','Image','required|trim');
			}

			if($this->form_validation->run())
			{
				$user = $this->security->xss_clean($this->input->post('user'));
				$title = $this->security->xss_clean($this->input->post('title'));
				$upload = '';

				$config['upload_path'] = './public/banner';
				$config['allowed_types'] = 'jpg|png|jpeg|gif';
				$config['max_size'] = '';
				$config['max_height'] = '';
				$config['max_width'] = '';
				
				$this->load->library('upload',$config);

				if(!empty($_FILES['image']['name']))
				{
					if(!$this->upload->do_upload('image'))
					{
						$this->session->set_flashdata('danger','Please Upload Required format Image!!');
						$this->load->view('admin/add-banner');
					}   
					else
					{
						$image1 = $this->upload->data();
						$upload = $image1['file_name'];
					}
				}
				else
				{
					$upload = $this->input->post('image');
				}

				$data = array();
				$data['b_user'] = $user;
				$data['b_title'] = $title;
				$data['b_image'] = $upload;

				$add = $this->AM->add_banner($data);

				if($add)
				{
					$this->session->set_flashdata('success','Banner Added Successfuly!!');
					$this->load->view('admin/add-banner');
				}
			}
			else
			{
				$this->session->set_flashdata('danger','Fill all the Required Fields');
				$this->load->view('admin/add-banner');
			}
		}
		else
		{
			$this->load->view('admin/add-banner');
		}
	}

	public function editbanner()
	{
		$this->admin_access();
		$id = $this->uri->segment('2');
		if($this->input->post('update'))
		{
			$this->form_validation->set_rules('user','User','required|trim');
			$this->form_validation->set_rules('title','Title','required');
			
			if(empty($_FILES['image']['name']))
			{
			    $this->form_validation->set_rules('image','Image','required|trim');
			}

			if($this->form_validation->run())
			{
				$user = $this->security->xss_clean($this->input->post('user'));
				$title = $this->security->xss_clean($this->input->post('title'));
				$upload = '';

				$config['upload_path'] = './public/banner';
				$config['allowed_types'] = 'jpg|png|jpeg|gif';
				$config['max_size'] = '';
				$config['max_height'] = '';
				$config['max_width'] = '';
				
				$this->load->library('upload',$config);

				if(!empty($_FILES['image']['name']))
				{
					if(!$this->upload->do_upload('image'))
					{
						$this->session->set_flashdata('danger','Please Upload Required format Image!!');
						$data['edit'] = $this->AM->edit_banner($id);
						$this->load->view('admin/edit-banner',$data);
					}   
					else
					{
						$image1 = $this->upload->data();
						$upload = $image1['file_name'];
					}
				}
				else
				{
					$upload = $this->input->post('image');
				}

				$data = array();
				$data['b_user'] = $user;
				$data['b_title'] = $title;
				$data['b_image'] = $upload;

				$update = $this->AM->update_banner($id,$data);

				if($update)
				{
					$this->session->set_flashdata('success','Banner Updated Successfuly!!');
					$data['edit'] = $this->AM->edit_banner($id);
					$this->load->view('admin/edit-banner',$data);
				}
			}
			else
			{
				$this->session->set_flashdata('danger','Fill all the Required Fields');
				$data['edit'] = $this->AM->edit_banner($id);
				$this->load->view('admin/edit-banner',$data);
			}
		}
		else
		{
			$data['edit'] = $this->AM->edit_banner($id);
			$this->load->view('admin/edit-banner',$data);
		}
	}

	public function product()
	{
		$this->admin_access();
		$data['product'] = $this->AM->product();
		$this->load->view('admin/product',$data);
	}

	public function faq()
	{
		$this->admin_access();
		$this->load->view('admin/faq');
	}

	public function addfaq()
	{
		$this->admin_access();
		$this->load->view('admin/add-faq');
	}

	public function privacy()
	{
		$this->admin_access();
		$this->load->view('admin/privacy-security');
	}

	// public function purchasereport()
	// {
	// 	$this->admin_access();
	// 	$id = $this->uri->segment('2');
	// 	if($this->input->post('search'))
	// 	{

	// 		$this->db->select('*');
	// 		$this->db->where('o_user_id',$id);
	// 		$this->db->where('YEAR(o_created_at)', date('Y'));
	// 		$this->db->where('MONTH(o_created_at)', date('m'));
	// 		$select = $this->db->get('tbl_orders');
	// 		$data = $select->result_array();

	// 		$this->load->view('admin/view-buyer',$data);
	// 	}
	// 	else
	// 	{
	// 		$this->load->view('admin/view-buyer/#purd');
	// 	}
	// }
}
