<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Orders</h4>
              <hr>
              <div class="row">
              <div class="col-md-12">
                      <div class="table-responsive">
                        <table id="order-listing" class="table">
                          <thead>
                            <tr class="bg-dark text-white">
                                <th>Order #</th>
                                <th>Customer Name</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                                <th>Order date</th>
                                <th>Payment Status</th>
                                <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                                <?php
                                    foreach($orders as $values)
                                    {
                                        $this->db->where('u_id',$values['o_user_id']);
                                        $select = $this->db->get('tbl_users');
                                        $data = $select->row();

                                        $this->db->where('p_id',$values['o_product_id']);
                                        $select = $this->db->get('tbl_product');
                                        $data1 = $select->row();

                                        if($values['o_flag'] == $id)
                                        {
                                ?>
                                        <tr>
                                        <td><?php echo $values['o_order_id']; ?></td>
                                        <td><?php echo $data->u_first_name; ?></td>
                                        <td><?php echo $data1->p_name; ?></td>
                                        <td><?php echo $values['o_qty']; ?></td>
                                        <td><?php echo '$'.$values['o_total_amount']; ?></td>
                                        <td><?php
                                        $convert = date("m-d-y", strtotime($values['o_order_date']));
                                         echo $convert; 
                                         ?></td>
                                            <td>
                                                <?php
                                                if($values['o_flag'] == '1')
                                                {
                                                ?>
                                                    <label class="badge badge-danger">Pending</label>
                                                <?php
                                                }
                                                elseif($values['o_flag'] == '2')
                                                {
                                                ?>
                                                    <label class="badge badge-success">Delivered</label>
                                                <?php
                                                }
                                                elseif($values['o_flag'] == '3')
                                                {
                                                ?>
                                                    <label class="badge badge-warning">Canceled</label>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-right">
                                              <a href="<?php echo base_url('view-order/').$values['o_id'];?>">
                                                <button class="btn btn-light">
                                                    <i class="mdi mdi-eye text-primary"></i> View
                                                </button>
                                              </a>
                                            </td>
                                        </tr>
                                <?php
                                        }
                                    }
                                ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
              </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>