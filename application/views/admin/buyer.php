<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Buyer</h4>
              <hr>
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table id="order-listing" class="table">
                      <thead>
                        <tr  class="bg-dark text-light">
                            <th>Serial #</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <!-- <th>Gender</th> -->
                            <th>Company Name</th>
                            <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $count = 1; 
                          foreach($buyer as $values)
                          {
                        ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo $values['u_id'];?></td>
                            <td><?php echo $values['u_first_name'].' '.$values['u_last_name'];?></td>
                            <td><?php echo $values['u_mobile'];?></td>
                            <td><?php echo $values['u_email'];?></td>
                            <!-- <td><?php echo $values['u_gender'];?></td> -->
                            <td><?php echo $values['u_company'];?></td>
                            <td>
                              <a href="<?php echo base_url('view-buyer/').$values['u_id']; ?>">
                              <button class="btn btn-light"><i class="mdi mdi-eye text-primary"></i> View</button></a>
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
        </div>
<?php $this->load->view('admin/footer'); ?>