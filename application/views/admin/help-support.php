<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Help & Support</h4>
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
                            <!-- <th>Subject</th> -->
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $count =1;

                          foreach($query as $values)
                          {
                            $this->db->where('u_id',$values['q_user_id']);
                            $select = $this->db->get('tbl_users');
                            $user = $select->row();
                        ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo $user->u_id;?></td>
                            <td><?php echo $user->u_first_name.$user->u_last_name;?></td>
                            <td><?php echo $user->u_type;?></td>
                            <!-- <td><?php echo $values['q_subject'];?></td> -->
                            <td><?php echo $values['q_description'];?></td>
                            <td>
                              <a href="<?php echo base_url('reply/').$values['q_id']; ?>">
                              <button class="btn btn-outline-primary">Reply</button></a>
                            </td>
                        </tr>
                        <?php }?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>