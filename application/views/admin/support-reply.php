<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Give Solution</h4>
              <?php
                            if(!empty($this->session->flashdata('success')))
                            {
                            ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('success'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                            <?php
                            }
                            elseif(!empty($this->session->flashdata('danger')))
                            {
                                ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('danger'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <?php
                            }
                            else
                            {
                                echo "";
                            }
                            ?>
              <hr>
              <?php
                $this->db->where('u_id',$reply->q_user_id);
                $select = $this->db->get('tbl_users');
                $user = $select->row();
              ?>
              <div class="row ">
                  <div class="col-md-5 shadow rounded p-5">
                      <div class="product-history">
                        <div class="product-history-details d-flex align-item-center py-2">
                          <div style="flex-basis: 100%;">
                            <strong>User Name : </strong>
                          </div>
                          <div>
                              <p class="m-0"><?php echo $user->u_first_name.$user->u_last_name; ?></p>
                          </div>
                        </div>
                        <div class="product-history-details d-flex align-item-center py-2">
                          <div style="flex-basis: 100%;">
                            <strong>User ID : </strong>
                          </div>
                          <div>
                            <p class="m-0"><?php echo $user->u_id; ?></p>
                          </div>
                        </div>
                        <div class="product-history-details d-flex align-item-center py-2">
                            <div style="flex-basis: 100%;">
                              <strong>Type : </strong>
                            </div>
                            <div>
                              <p class="m-0"><?php echo $user->u_type;  ?></p>
                            </div>
                        </div> 
                        <div class="product-history-details d-flex align-item-center py-2">
                          <div style="flex-basis: 100%;">
                            <strong>Message : </strong>
                          </div>
                          <div>
                            <p class="m-0"><?php echo $reply->q_description; ?></p>
                          </div>
                        </div>
                      </div>
                  </div>
                  <div class="col-md-7 m-auto p-3">
                    <form class="forms-sample" method="POST" action="<?php echo base_url('reply/').$reply->q_id;?>">
                      <div class="form-group">
                        <label for="exampleInputUsername1" ><h4> Write Solution </h4></label> 
                        <textarea class="form-control" style="border: 2px solid lightgrey;" name="answer" id="exampleInputUsername1" cols="10" rows="7"></textarea>
                      </div>
                      <input type="submit" name="submit" class="btn btn-primary float-right" value="Submit">
                      <!-- <button type="submit" name="submit" class="btn btn-primary float-right">Submit</button> -->
                    </form>
                  </div>
              </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>