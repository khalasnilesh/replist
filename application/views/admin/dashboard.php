
<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar.php');?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="d-flex justify-content-between flex-wrap">
                <div class="d-flex align-items-center dashboard-header flex-wrap mb-3 mb-sm-0">
                  <h5 class="mr-4 mb-0 font-weight-bold">Dashboard</h5>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
              <div class="card icon-card-primary">
                <a class="text-white text-decoration-none" href="<?php echo base_url('reps'); ?>">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="icon mb-0 mb-md-2 mb-xl-0 mr-2">
                          <i class="mdi mdi-star-circle"></i>
                        </div>
                        <p class="font-weight-medium mb-0">Reps</p>
                      </div>
                      <div class="d-flex align-items-center mt-3 flex-wrap">
                        <h3 class="font-weight-normal mb-0 mr-2"><?php echo count($reps); ?></h3>
                      </div>
                    <small class="font-weight-medium d-block mt-2">Total Reps</small>
                  </div>
                </a>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
              <div class="card icon-card-success">
              <a class="text-white text-decoration-none" href="<?php echo base_url('buyer'); ?>">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="icon mb-0 mb-md-2 mb-xl-0 mr-2">
                        <i class="mdi mdi-truck"></i>
                      </div>
                      <p class="font-weight-medium mb-0">Buyers</p>
                    </div>
                    <div class="d-flex align-items-center mt-3 flex-wrap">
                      <h3 class="font-weight-normal mb-0 mr-2"><?php echo count($buyer); ?></h3>
                    </div>
                  <small class="font-weight-medium d-block mt-2">Total Buyers</small>
                </div>
              </a>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
              <div class="card icon-card-info">
              <a class="text-white text-decoration-none" href="<?php echo base_url('report'); ?>">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="icon mb-0 mb-md-2 mb-xl-0 mr-2">
                        <i class="mdi mdi-basket"></i>
                      </div>
                      <p class="font-weight-medium mb-0">Orders</p>
                    </div>
                    <div class="d-flex align-items-center mt-3 flex-wrap">
                      <h3 class="font-weight-normal mb-0 mr-2"><?php echo $orders;?></h3>
                    </div>
                    <small class="font-weight-medium d-block mt-2">Total Orders</small>
                </div>
              </a>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
              <div class="card icon-card-dark">
              <a class="text-white text-decoration-none" href="<?php echo base_url('report'); ?>">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="icon mb-0 mb-md-2 mb-xl-0 mr-2">
                        <i class="mdi mdi-package-down"></i>
                      </div>
                      <p class="font-weight-medium mb-0">Sale</p>
                    </div>
                    <div class="d-flex align-items-center mt-3 flex-wrap">
                      <h3 class="font-weight-normal mb-0 mr-2"><?php echo $orders;?></h3>
                    </div>
                    <small class="font-weight-medium d-block mt-2">Total Sales</small>
                </div>
              </a>
              </div>
            </div>
          </div>
          <h4>Order History</h4>
              <hr>
          <div class="row">
            <div class="col-md-12">
              <div class="table-responsive">
                <table id="order-listing" class="table">
                  <thead>
                    <tr class="bg-dark text-light">
                      <th>Order #</th>
                      <th>Customer Name</th>
                      <th>Product Name</th>
                      <th>Quantity</th>
                      <th>Total Amount</th>
                      <th>Order date</th>
                      <th>Order Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  foreach($order as $values)
                  {
                    $this->db->where('u_id',$values['o_user_id']);
                    $select = $this->db->get('tbl_users');
                    $data = $select->row();

                    $this->db->where('p_id',$values['o_product_id']);
                    $select = $this->db->get('tbl_product');
                    $data1 = $select->row();
                  ?>                
                    <tr>
                      <td><?php echo $values['o_order_id']; ?></td>
                      <td><?php echo $data->u_first_name; ?></td>
                      <td><?php echo $data1->p_name; ?></td>
                      <td><?php echo $values['o_qty']; ?></td>
                      <td><?php echo '$'.$values['o_total_amount']; ?></td>
                      <td><?php echo $values['o_order_date']; ?></td>
                      <td>
                      <?php
                      if($values['o_flag']==1)
                      {
                      ?>
                      <label class="badge badge-danger">Pending</label>
                      <?php  
                      }
                      elseif($values['o_flag']==2)
                      {
                      ?>
                      <label class="badge badge-success">Delivered</label>
                      <?php  
                      }
                      elseif($values['o_flag']==3)
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
                      ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
        <!-- partial -->
      </div>

  <!-- base:js -->
  <script src="<?php echo base_url('public/admin/vendors/js/vendor.bundle.base.js');?>"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="<?php echo base_url('public/admin/js/off-canvas.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/js/hoverable-collapse.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/js/template.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/js/settings.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/js/todolist.js'); ?>"></script>
  <!-- endinject -->
  <!-- plugin js for this page -->
  <script src="<?php echo base_url('public/admin/vendors/chart.js/Chart.min.js'); ?>"></script>
  <!-- End plugin js for this page -->
  <!-- Custom js for this page-->
  <script src="<?php echo base_url('public/admin/js/dashboard.js'); ?>"></script>
  <!-- End custom js for this page-->
  <script src="<?php echo base_url('public/admin/vendors/datatables.net/jquery.dataTables.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/vendors/datatables.net-bs4/dataTables.bootstrap4.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/js/data-table.js'); ?>"></script>

</body>
</html>

