<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class ApiModel extends CI_model { 
    
    public function login($email, $password, $type)
    {
        $this->db->where('email', $email);
        $this->db->where('password', $password);
        $this->db->where('type', $type);
        $this->db->where('flag !=','2');
        $query = $this->db->get('user_details');
		$result = $query->result_array();
        return $result;
    }
  
  
    public function insert_common($table,$data){
		$this->db->insert($table,$data);
		return $this->db->insert_id();

	}
	
	public function update_common($table,$data,$where,$id){
		$this->db->where($where,$id);
		$this->db->update($table,$data);
	}


	public function update_common_where($table,$data,$where,$id){
		$this->db->where($where,$id);
		$this->db->where('flag','0');
		$this->db->update($table,$data);
	}

	public function update_ads($id){
		$this->db->set('hit','hit+1',FALSE);
		$this->db->where('id', $id);
		$this->db->update('advertisement');
	}
	
	
	public function update_stock($id,$stock){
		$this->db->set('stock','stock-'.$stock,FALSE);
		$this->db->where('id', $id);
		$this->db->update('product');
	}
	
	
	public function update($table,$data){
		$this->db->update($table,$data);
	}
	
	public function delete_common($table,$where,$id){
		$this->db->where($where,$id);
		$this->db->where('flag','0');
		$this->db->delete($table);
	}
	
	public function delete_common2($table,$where,$id){
    	$this->db->where($where,$id);
    	$this->db->where('flag','1');
    	$this->db->delete($table);
	}

    public function list_common($table){
		$this->db->select('*');
 		$this->db->from($table);
 		$this->db->order_by("id","desc");		
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	public function list_common_where($table,$where,$id){
		$this->db->select('*');
		$this->db->where($where,$id);
 		$this->db->from($table);	
 		$this->db->where('flag','0');
 		$this->db->order_by("id","desc");
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	public function list_common_where3($table,$where,$id){
		$this->db->select('*');
		$this->db->where($where,$id);
 		$this->db->from($table);	
 		$this->db->where('flag','1');
 		$this->db->order_by("id","desc");
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	
	public function list_common_product($table,$where,$id){
		$this->db->select('id, image, name');
		$this->db->where($where,$id);
 		$this->db->from($table);	
 		$this->db->where('flag','0');
 		$this->db->order_by("id","desc");
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}


	public function list_common_where2($table,$where,$id){
		$this->db->select('*');
		$this->db->where($where,$id);
 		$this->db->from($table);	
 		$this->db->order_by("timestamp","desc");
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}

    public function get_rep_id($id){
		$this->db->select('rep_id');
 		$this->db->from('order_details');	
 		$this->db->where('order_id',$id);
 		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	public function list_buyer($id){
		$this->db->select('id, email, first_name, last_name, contact_number, company, profile_pic, address, type,device_token, latitute, longitute');
		$this->db->where('id',$id);
 		$this->db->from('user_details');	
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}


	public function list_connected($id){
		$this->db->select('*');
		$this->db->where('flag','1');
		$this->db->where("user_id='$id'");
		$this->db->or_where('friend_id',$id);
 		$this->db->from('invitation');	
 		
 		$this->db->order_by("id","desc");
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}


	public function list_reps($type){
		$this->db->select('id, email, first_name, last_name, contact_number, company, profile_pic, address');
		$this->db->where('type',$type);
		$this->db->where('id !=',$id);
 		$this->db->from('user_details');	
 		$this->db->where('flag','0');
 		$this->db->order_by("id","desc");
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}


	public function list_not_friends($id,$friend_id){
 		$query = $this->db->get("invitation WHERE (user_id = '$friend_id' AND friend_id = '$id') OR (user_id = '$id' AND friend_id = '$friend_id') ORDER BY id DESC");
		$result = $query->result_array();
		return $result;
	}
	



	public function search($id,$user_id){
		$this->db->select('id, email, first_name, last_name, contact_number, company, profile_pic, address, type');
		$this->db->like('first_name',$id);
		$this->db->or_like('email',$id);
		$this->db->or_like('last_name',$id);
		$this->db->or_like('company',$id);
		$this->db->where('id !=',$user_id);
 		$this->db->from('user_details');	
 		$this->db->where('flag!=','2');
 		$this->db->order_by("id","desc");
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}


	public function order_details($where, $id){
		$this->db->select('total_price, date, order_id, rep_id, user_id, flag');
 		$this->db->group_by('order_id');
 		$this->db->from('order_details');	
 		$this->db->where($where,$id);
 		$this->db->where('flag !=','0');
 		$this->db->order_by("timestamp","desc");
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	public function order_details2($where, $id){
		$this->db->select('sum(price) as total_price');
 		$this->db->from('order_details');	
 		$this->db->where($where,$id);
 		$this->db->where('flag !=','0');
 		$this->db->order_by("timestamp","desc");
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}


	public function order_details_info($id){
		$this->db->select('order_details.timestamp, order_details.flag, order_details.price, order_details.date, order_details.order_id, order_details.user_id, order_details.rep_id, order_details.quantity, order_details.total_price, product.id, product.name, product.image');
 		$this->db->from('order_details');
		$this->db->join('product', 'product.id = order_details.product_id','left');
		$this->db->where('order_id',$id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	
	public function individual_order_details_info($id,$buyer_id){
		$this->db->select('order_details.user_id, order_details.rep_id ,order_details.total_price, order_details.date, order_details.order_id, order_details.quantity, product.id, product.name, product.image, order_details.flag');
 		$this->db->from('order_details');
		$this->db->join('product', 'product.id = order_details.product_id','left');
		$this->db->where('order_details.user_id',$buyer_id);
 		$this->db->where('order_details.rep_id',$id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	
	public function individual_order_details_info2($id,$rep_id){
		$this->db->select('order_details.user_id, order_details.rep_id ,order_details.total_price, order_details.date, order_details.order_id, order_details.quantity, product.id, product.name, product.image, order_details.flag');
 		$this->db->group_by('order_details.order_id');
 		$this->db->from('order_details');
		$this->db->join('product', 'product.id = order_details.product_id','left');
		$this->db->where('order_details.user_id',$id);
 		$this->db->where('order_details.rep_id',$rep_id);
 		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}


	public function order_by_year($where, $id){
		$this->db->select('SUM(price) as total_price, RIGHT(date, 4) as date');
 		$this->db->from('order_details');	
 		$this->db->where($where,$id);
 		$this->db->where('flag','1');
 		$this->db->order_by("timestamp","desc");
 		$this->db->group_by('LEFT(timestamp, 4)');
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}



	public function order_by_month($where, $id, $year){
		$this->db->select('SUM(price) as total_price, RIGHT(date, 9) as date, LEFT(timestamp,7) as monthreport');
 		$this->db->from('order_details');	
 		$this->db->where($where,$id);
 		$this->db->where('LEFT(timestamp, 4) =', $year,TRUE);
 		$this->db->where('flag','1');
 		$this->db->order_by("timestamp","desc");
 		$this->db->group_by('LEFT(timestamp, 7)');
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}



	public function order_by_days($where, $id, $month){
		$this->db->select('SUM(price) as total_price, date');
 		$this->db->from('order_details');	
 		$this->db->where($where,$id);
 		$this->db->where('LEFT(timestamp, 7) =',$month,true);
 		$this->db->where('flag','1');
 		$this->db->order_by("timestamp","desc");
 		$this->db->group_by('LEFT(timestamp, 10)');
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}




}
?>	