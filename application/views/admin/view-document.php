<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
<div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12">
            <h4 class="">Document Details</h4>
            <hr>
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-5" style="border-right-style:groove;border-right-color: light-grey;border-right-width: 2px;">
                      <div class="border-bottom text-center pb-4">
                      <?php
                      $this->db->where('u_id',$view->d_user_id);
                      $select = $this->db->get('tbl_users');
                      $user = $select->row();

                        if(!empty($view->d_files))
                        {
                        ?>
                          <img src="<?php echo base_url('public/doc_files/').$view->d_files; ?>" alt="document" class="img-lg rounded-circle mb-3"/>
                        <?php
                        }
                        else
                        {
                        ?>
                          <img src="<?php echo base_url('public/admin/images/faces/face12.jpg'); ?>" alt="profile" class="img-lg rounded-circle mb-3"/>
                        <?php
                        }
                        ?>
                        <div class="">
                          <div class="align-items-center">
                          <h4><?php echo $view->d_heading;?></h4>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-7">
                    <div class="py-4 px-4">
                        <p class="clearfix">
                          <span class="float-left">
                          User Name
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $user->u_first_name.' '.$user->u_last_name;?>
                          </span>
                        </p>
                        <p class="clearfix">
                          <span class="float-left">
                          User Type
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $user->u_type;?>
                          </span>
                        </p>
                        <p class="clearfix">
                          <span class="float-left">
                          Contact Number
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $user->u_mobile;?>
                          </span>
                        </p>
                        <p class="clearfix">
                          <span class="float-left">
                          Email
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $user->u_email;?>
                          </span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
<?php $this->load->view('admin/footer'); ?>