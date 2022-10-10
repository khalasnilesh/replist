<?php
class Home_Model extends CI_Model 
{
	public function add_user($data)
	{
		$this->db->insert('tbl_users',$data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	public function edit_profile($id)
	{
		$this->db->select('*');
		$this->db->where('u_id',$id);
		$select = $this->db->get('tbl_users');
		$data = $select->row();
		return $data;
	}

	public function update_profile($id,$data)
	{
		$this->db->where('u_id',$id);
		$update = $this->db->update('tbl_users',$data);
		return $update;
	}

	public function change_password($id,$data)
	{
		$this->db->where('u_id',$id);
		$update = $this->db->update('tbl_users',$data);
		return $update;
	}

	public function send_otp($data)
	{
		$this->db->insert('tbl_otp',$data);
		$otp_id = $this->db->insert_id();
		return $otp_id;
	}

	public function last_send_otp($user_id,$insert_id)
	{
		if(!empty($insert_id))
		{
			$this->db->select('*');
			$this->db->where('o_user_id',$user_id);
			$this->db->where('o_id',$insert_id);
			$select = $this->db->get('tbl_otp');
			$last_otp = $select->row();
			return $last_otp;
		}
		else
		{
			$this->db->select('*');
			$this->db->where('o_user_id',$user_id);
			$select = $this->db->get('tbl_otp');
			$last_otp = $select->row();
			return $last_otp;
		}
	}

	public function update_otp($mobile,$data)
	{
		$this->db->where('o_mobile_number',$mobile);
		$update = $this->db->update('tbl_otp',$data);
		return $update;
	}

	public function update_email_otp($user_id,$data)
	{
		$this->db->where('o_user_id',$user_id);
		$update = $this->db->update('tbl_otp',$data);
		return $update;
	}

	public function verify_otp($id,$data)
	{
		$this->db->where('o_user_id',$id);
		$update = $this->db->update('tbl_otp',$data);
		return $update;
	}

	public function add_product($data)
	{
		$this->db->insert('tbl_product',$data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	public function count_product($id)
	{
		$this->db->select('*');
		$this->db->where('p_id',$id);
		$select = $this->db->get('tbl_product');
		$product_num = $select->num_rows();
		return $product_num;
	}

	public function edit_product($id)
	{
		$this->db->select('*');
		$this->db->where('p_id',$id);
		$select = $this->db->get('tbl_product');
		$data = $select->row();
		return $data;
	}

	public function update_product($id,$data)
	{
		$this->db->where('p_id',$id);
		$update = $this->db->update('tbl_product',$data);
		return $update;
	}

	public function add_document($data)
	{
		$this->db->insert('tbl_documents',$data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	public function count_document()
	{
		$this->db->select('*');
		$this->db->where('d_id',$id);
		$this->db->where('d_status','0');
		$select = $this->db->get('tbl_documents');
		$document_num = $select->num_rows();
		return $document_num;
	}

	public function edit_document($id)
	{
		$this->db->select('*');
		$this->db->where('d_id',$id);
		$select = $this->db->get('tbl_documents');
		$data = $select->row();
		return $data;
	}

	public function update_document($id,$data)
	{
		$this->db->where('d_id',$id);
		$update = $this->db->update('tbl_documents',$data);
		return $update;
	}

	public function add_notes($data)
	{
		$this->db->insert('tbl_notes',$data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	public function manage_notes($id)
	{
		$this->db->select('*');
		$this->db->where('n_flag','0');
		$this->db->where('n_user_id',$id);
		$select = $this->db->get('tbl_notes');
		$data = $select->result_array();
		return $data;
	}

	public function edit_notes($notes_id)
	{
		$this->db->select('*');
		$this->db->where('n_id',$notes_id);
		$this->db->where('n_flag','0');
		$select = $this->db->get('tbl_notes');
		$data = $select->row();
		return $data;
	}

	public function update_notes($user_id,$id,$data)
	{
		$this->db->where('n_user_id',$user_id);
		$this->db->where('n_id',$id);
		$update = $this->db->update('tbl_notes',$data);
		return $update;
	}

	public function delete_notes($id)
	{
		$this->db->where('n_id',$id);
		$this->db->where('n_flag','1');
		$this->db->delete('tbl_notes');
	}

	public function add_invitation($data)
	{
		$this->db->insert('tbl_invitation',$data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	public function update_invitation($invitation_id,$data)
	{
		$this->db->where('i_id',$invitation_id);
        $update = $this->db->update('tbl_invitation',$data);
        return $update;
	}

	public function check_rep_id($id)
	{
		$this->db->select('*');
		$this->db->where('u_id',$id);
		$this->db->where('u_type','Reps');
		$select = $this->db->get('tbl_users');
		$rep_num = $select->num_rows();
		return $rep_num;
	}

	public function check_buyer_id($id)
	{
		$this->db->select('*');
		$this->db->where('u_id',$id);
		$this->db->where('u_type','Buyer');
		$select = $this->db->get('tbl_users');
		$buyer_num = $select->num_rows();
		return $buyer_num;
	}

	public function check_product_id($id)
	{
		$this->db->select('*');
		$this->db->where('p_id',$id);
		$select = $this->db->get('tbl_product');
		$product_num = $select->num_rows();
		return $product_num;
	}

	public function check_cart_exist($rep_id,$user_id)
	{
		$this->db->select('*');
		$this->db->where('c_user_id',$user_id);
		$this->db->where('c_rep_id',$rep_id);
		$select = $this->db->get('tbl_cart');
		$cart_exist = $select->num_rows();
		return $cart_exist;
	}

	public function check_cart_num($user_id)
	{
		$this->db->select('*');
		$this->db->where('c_user_id',$user_id);
		$select = $this->db->get('tbl_cart');
		$cart_num = $select->num_rows();
		return $cart_num;
	}

	public function check_cart_value_exist($product_id,$user_id)
	{
		$this->db->select('*');
		$this->db->where('c_product_id',$product_id);
		$this->db->where('c_user_id',$user_id);
		$select = $this->db->get('tbl_cart');
		$cart_num = $select->num_rows();
		return $cart_num;
	}

	public function add_to_cart($data)
	{
		$cart = $this->db->insert('tbl_cart',$data);
		return $cart;
	}

	public function edit_cart($id)
	{
		$this->db->where('c_id',$id);
		$select = $this->db->get('tbl_cart');
		$data = $select->row();
		return $data;
	}

	public function update_cart($user_id,$cart_id,$data)
	{
		$this->db->where('c_user_id',$user_id);
		$this->db->where('c_id',$cart_id);
		$update = $this->db->update('tbl_cart',$data);
		return $update;
	}

	public function manage_cart($id)
	{
		$this->db->where('c_user_id',$id);
		$select = $this->db->get('tbl_cart');
		$data = $select->result_array();
		return $data;
	}	

	public function cart_count($id)
	{
		$this->db->where('c_user_id',$id);
		$select = $this->db->get('tbl_cart');
		$num = $select->num_rows();
		return $num;

	}

	public function delete_cart($cart_id)
	{
		$this->db->where('c_id',$cart_id);
		$delete = $this->db->delete('tbl_cart');
		return $delete;
	}

	public function add_to_wishlist($data)
	{	
		$this->db->insert('tbl_wishlist',$data);
		$insert = $this->db->insert_id();
		return $insert;
	}

	public function manage_wishlist($id)
	{
		$this->db->where('w_u_id',$id);
		$select = $this->db->get('tbl_wishlist');
		$data = $select->result_array();
		return $data;
	}

	public function wishlist_count($id)
	{
		$this->db->where('w_u_id',$id);
		$select = $this->db->get('tbl_wishlist');
		$num = $select->num_rows();
		return $num;
	}

	public function delete_wishlist($user_id,$wishlist_id)
	{
		$this->db->where('w_id',$wishlist_id);
		$this->db->where('w_u_id',$user_id);
		$delete = $this->db->delete('tbl_wishlist');
		return $delete;
	}

	public function reps_list()
	{
		$this->db->select('*');
		$this->db->where('u_type','Reps');
		$select = $this->db->get('tbl_users');
		$reps = $select->result_array();
		return $reps;
	}

	public function buyer_list()
	{
		$this->db->select('*');
		$this->db->where('u_type','Buyer');
		$select = $this->db->get('tbl_users');
		$buyer = $select->result_array();
		return $buyer;
	}

	public function generate_order_no()
	{
		$this->db->select('*');
		$this->db->order_by('o_id','desc');
		$select = $this->db->get('tbl_orders');
		$order_data = $select->row();
		$num = $select->num_rows();

		if($num>0)
		{
			$order_no = $order_data->o_order_id;
			$order_no++;
			return $order_no;
		}
		else
		{
			$order_no = 'OD1000';
			return $order_no;
		}
	}

	public function place_order($data)
	{
		$this->db->insert('tbl_orders',$data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	public function update_order_status($id,$data)
	{
		$this->db->where('o_id',$id);
		$update = $this->db->update('tbl_orders',$data);
		return $update;
	}

	public function order_history($id)
	{
		$this->db->select('*');
		$this->db->order_by('o_id','desc');
		$this->db->where('o_user_id',$id);
		$select = $this->db->get('tbl_orders');
		$data = $select->result_array();
		return $data;
	}

	public function add_query($data)
	{
		$insert = $this->db->insert('tbl_query',$data);
		return $insert;
	}

	public function add_review($data)
	{
		$insert = $this->db->insert('tbl_reviews',$data);
		return $insert;
	}

	public function review_count($product_id)
	{
		$this->db->select('*');
		$this->db->where('r_product_id',$product_id);
		$select = $this->db->get('tbl_reviews');
		$review_num = $select->num_rows();
		return $review_num;
	}

	public function manage_review($product_id)
	{
		$this->db->select('*');
		$this->db->where('r_product_id',$product_id);
		$select = $this->db->get('tbl_reviews');
		$review_data = $select->result_array();
		return $review_data;
	}

	public function check_category_id($id)
	{
		$this->db->select('*');
		$this->db->where('c_id',$id);
		$select = $this->db->get('tbl_category');
		$category_num = $select->num_rows();
		return $category_num;
	}

	public function check_company_info($id)
	{
		$this->db->where('ci_user_id',$id);
		$select = $this->db->get('tbl_company_info');
		$num = $select->num_rows();
		return $num;
	}

	public function add_company_info($data)
	{
		$update = $this->db->insert('tbl_company_info',$data);
		return $update;
	}

	public function edit_company_info($id)
	{
		$this->db->where('ci_user_id',$id);
		$select = $this->db->get('tbl_company_info');
		$data = $select->row();
		return $data;
	}

	public function update_company_info($id,$data)
	{
		$this->db->where('ci_user_id',$id);
		$update = $this->db->update('tbl_company_info',$data);
		return $update;
	}

	public function check_friend_exist_or_not($user_id,$friend_id)
	{
		$this->db->select('*');
		$this->db->where('i_sender_id',$user_id);
		$this->db->where('i_receiver_id',$friend_id);
		$this->db->or_where('i_sender_id',$user_id);
		$this->db->or_where('i_receiver_id',$friend_id);
		$select = $this->db->get('tbl_invitation');
		$check_friend = $select->num_rows();
		return $check_friend;
	}

	public function check_referal_code($referal_code)
	{
		$this->db->where('u_user_code',$referal_code);
		$select = $this->db->get('tbl_users');
		$referal_code = $select->num_rows();
		return $referal_code;
	}

	public function get_parent_id($referal_code)
	{
		$this->db->where('u_user_code',$referal_code);
		$select = $this->db->get('tbl_users');
		$user_profile = $select->row();
		return $user_profile;
	}

	public function add_reward_points($data)
	{
		$insert = $this->db->insert('tbl_reward_points',$data);
		return $insert;
	} 

	public function manage_reward_points($user_id)
	{
		$this->db->select('*');
		$this->db->order_by('rp_id','desc');
		$this->db->where('rp_user_id',$user_id);
		$select = $this->db->get('tbl_reward_points');
		$data = $select->row();
		return $data;
	}

	public function manage_reward($id)
	{
		$this->db->where('rp_user_id',$id);
		$select = $this->db->get('tbl_reward_points');
		$data = $select->result_array();
		return $data;
	}

	public function count_reward_points($user_id)
	{
		$this->db->select('*');
		$this->db->where('rp_user_id',$user_id);
		$select = $this->db->get('tbl_reward_points');
		$reward_num = $select->num_rows();
		return $reward_num;
	}

	public function total_reward_points($id)
	{
		$this->db->order_by('rp_id','desc');
		$this->db->where('rp_user_id',$id);
		$select = $this->db->get('tbl_reward_points');
		$data = $select->row();
		return $data;
	}

	public function check_order_id($id)
	{
		$this->db->where('o_id',$id);
		$select = $this->db->get('tbl_orders');
		$order_num = $select->num_rows();
		return $order_num;
	}

	public function add_share_order_details($data)
	{
		$insert = $this->db->insert('tbl_share_order_details',$data);
		return $insert;
	}

	public function get_category_detail($id)
	{
		$this->db->where('c_id',$id);
		$select = $this->db->get('tbl_category');
		$data = $select->row();
		return $data;
	}

	public function add_notification($data)
	{
		$insert = $this->db->insert('tbl_notifications',$data);
		return $insert;
	}

	public function check_notifications($user_id)
	{
		$this->db->where('n_user_id',$user_id);
		$select = $this->db->get('tbl_notifications');
		$notification_num = $select->num_rows();
		return $notification_num;
	}

	public function manage_notification($user_id)
	{
		$this->db->where('n_user_id',$user_id);
		$select = $this->db->get('tbl_notifications');
		$data = $select->result_array();
		return $data;
	}

	public function coupon_num()
	{
		$this->db->select('*');
		$this->db->order_by('co_id','desc');
		$select = $this->db->get('tbl_coupon');
		$num = $select->num_rows();
		return $num;	
	}

	public function manage_coupon()
	{
		$this->db->select('*');
		$this->db->order_by('co_id','desc');
		$select = $this->db->get('tbl_coupon');
		$data = $select->result_array();
		return $data;	
	}

	public function check_coupon_code($coupon_code)
	{
		$this->db->select('*');
		$this->db->where('co_coupon_code',$coupon_code);
		$select = $this->db->get('tbl_coupon');
		$num = $select->num_rows();
		return $num;
	}

	public function coupon_detail($coupon_code)
	{
		$this->db->select('*');
		$this->db->where('co_coupon_code',$coupon_code);
		$select = $this->db->get('tbl_coupon');
		$data = $select->row();
		return $data;
	}

	public function update_cart_details($user_id,$data)
	{
		$this->db->where('c_user_id',$user_id);
		$update = $this->db->update('tbl_cart',$data);
		return $update;
	}

	public function help_center($data)
	{
		$insert = $this->db->insert('tbl_help_center',$data);
		return $insert;
	}

	public function send_mail($data)
	{
		$insert = $this->db->insert('tbl_send_mail',$data);
		return $insert;
	}

	public function promotion_num()
	{
		$this->db->select('*');
		$this->db->order_by('p_id','desc');
		$select = $this->db->get('tbl_promotion');
		$num = $select->num_rows();
		return $num;	
	}

	public function manage_promotion()
	{
		$this->db->select('*');
		$this->db->order_by('p_id','desc');
		$select = $this->db->get('tbl_promotion');
		$data = $select->result_array();
		return $data;	
	}

	public function total_orders($id)
	{
		$this->db->select("*");
		$this->db->where('o_rep_id',$id);
		$select = $this->db->get('tbl_orders');
		$order_num = $select->num_rows();
		return $order_num;
	}

	public function delivered_orders($id)
	{
		$this->db->select("*");
		$this->db->where('o_rep_id',$id);
		$this->db->where('o_flag','3');
		$select = $this->db->get('tbl_orders');
		$order_num = $select->num_rows();
		return $order_num;
	}

	public function pending_orders($id)
	{
		$this->db->select("*");
		$this->db->where('o_rep_id',$id);
		$this->db->where('o_flag','1');
		$select = $this->db->get('tbl_orders');
		$order_num = $select->num_rows();
		return $order_num;
	}

	public function multiple_image($id)
	{
		$this->db->where('pi_product_id',$id);
		$select = $this->db->get('tbl_product_image');
		$data = $select->result_array();
		return $data;
	}
}
?>