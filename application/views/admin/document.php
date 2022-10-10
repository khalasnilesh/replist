<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Documents</h4>
              <hr>
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table id="order-listing" class="table">
                      <thead>
                        <tr>
                            <th>Serial #</th>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Type</th>
                            <th>Heading</th>
                            <!-- <th>date</th> -->
                            <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          $count = 1;
                          foreach($doc as $values)
                          {
                            $this->db->where('u_id',$values['d_user_id']);
                            $select = $this->db->get('tbl_users');
                            $data = $select->row();
                            $num = $select->num_rows();
                            if($num!=0)
                            {
                        ?>
                          <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo $data->u_id; ?></td>
                            <td><?php echo $data->u_first_name.$data->u_last_name; ?></td>
                            <td><?php echo $data->u_type;?></td>
                            <td><?php echo $values['d_heading']; ?></td>
                            <td>
                              <a href="<?php echo base_url('view-document/').$values['d_id'];?>">
                              <button class="btn btn-light"><i class="mdi mdi-eye text-primary"></i> View</button></a>
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