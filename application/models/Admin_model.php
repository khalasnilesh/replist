<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Model extends CI_Model {

    public function admin_profile($id)
    {
        $this->db->where('id',$id);
        $select = $this->db->get('admin');
        $data = $select->row();
        return $data;
    }

    public function update_admin($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('admin',$data);
    }

    public function change_password($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('admin',$data);
    }

    public function reps_list()
    {
        $this->db->select('*');
        $this->db->order_by('u_id','asc');
        $this->db->where('u_type','Reps');
        $select = $this->db->get('tbl_users');
        $result = $select->result_array();
        return $result;
    }

    public function view_rep($id)
    {
        $this->db->where('u_id',$id);
        $this->db->where('u_type','Reps');
        $select = $this->db->get('tbl_users');
        $data = $select->row();
        return $data;
    }
    
    public function buyer_list()
    {
        $this->db->select('*');
        $this->db->order_by('u_id','asc');
        $this->db->where('u_type','Buyer');
        $select = $this->db->get('tbl_users');
        $result = $select->result_array();
        return $result;
    }

    public function view_buyer($id)
    {
        $this->db->where('u_id',$id);
        $this->db->where('u_type','Buyer');
        $select = $this->db->get('tbl_users');
        $data = $select->row();
        return $data;
    }

    public function order_list()
    {
        $this->db->select('*');
        $this->db->order_by('o_id','asc');
        $select = $this->db->get('tbl_orders');
        $result = $select->result_array();
        return $result;
    }

    public function order_history()
    {
        $this->db->select('*');
        $select = $this->db->get('tbl_orders');
        $result = $select->result_array();
        return $result;
    }

    public function view_order($id)
    {
        $this->db->where('o_id',$id);
        $select = $this->db->get('tbl_orders');
        $result = $select->row();
        return $result;
    }

    public function doc_list()
    {
        $this->db->select('*');
        $this->db->order_by('d_id','asc');
        $select = $this->db->get('tbl_documents');
        $result = $select->result_array();
        return $result;
    }

    public function view_document($id)
    {
        $this->db->where('d_id',$id);
        $select = $this->db->get('tbl_documents');
        $result = $select->row();
        return $result;
    }

    public function help_support()
    {
        $this->db->select('*');
        $select = $this->db->get('tbl_query');
        $result = $select->result_array();
        return $result;
    }

    public function query_reply($id,$data)
    {
        $this->db->where('q_id',$id);
        $update =  $this->db->update('tbl_query',$data);
        return $update;
    }

    public function query($id)
    {
        $this->db->where('q_id',$id);
        $select = $this->db->get('tbl_query');
        $result = $select->row();
        return $result;
    }

    public function category()
    {
        $this->db->select('*');
        $select = $this->db->get('tbl_category');
        $result = $select->result_array();
        return $result;
    }

    public function edit_category($id)
    {
        $this->db->where('c_id',$id);
        $select = $this->db->get('tbl_category');
        $result = $select->row();
        return $result;
    }

    public function add_category($data)
    {
        $add = $this->db->insert('tbl_category',$data);
        return $add;
    }

    public function update_category($data,$id)
    {
        $this->db->where('c_id',$id);
        $update = $this->db->update('tbl_category',$data);
        return $update;
    }

    public function product()
    {
        $this->db->select('*');
        $select = $this->db->get('tbl_product');
        $result = $select->result_array();
        return $result;
    }

    public function banner()
    {
        $this->db->select('*');
        $select = $this->db->get('tbl_banner');
        $result = $select->result_array();
        return $result;
    }

    public function add_banner($data)
    {
        $add = $this->db->insert('tbl_banner',$data);
        return $add;
    }

    public function edit_banner($id)
    {
        $this->db->where('b_id',$id);
        $select = $this->db->get('tbl_banner');
        $result = $select->row();
        return $result;
    }

    public function update_banner($id,$data)
    {
        $this->db->where('b_id',$id);
        $update = $this->db->update('tbl_banner',$data);
        return $update;
    }
    
}
?>