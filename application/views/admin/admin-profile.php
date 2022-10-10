<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Admin Profile</h4>
              <hr>
              <div class="row">
              <div class="col-12">
                            <div class="card card-default ">
                                <div class="card-body">
                                  <div class="row">
                                      <div class="col-md-12">
                                      <div class="card-header card-header-border-bottom d-flex justify-content-between float-right">
                                    <div class="add-btn">
                                      <a href="<?php echo base_url('edit-admin-profile/').$profile->id; ?>"><i class="mdi mdi-pencil mr-2 "></i>Edit Profile</a>
                                    </div>
                                </div>
                                      </div>
                                    <div class="col-md-4">
                                      <div class="admin-profile-box">
                                        <div class="admin-profile text-center" >
                                            <?php
                                            if(empty($profile->image))
                                            {
                                            ?>
                                                <img style="border-radius:200px;" src="<?php echo base_url('public/admin/images/faces/face4.jpg')?>" width="150" alt="admin_image">
                                            <?php
                                            }
                                            else
                                            {
                                            ?>
                                                <img style="border-radius:200px;" src="<?php echo base_url('public/admin/images/admin/').$profile->image;?>" width="150" alt="admin_image">
                                            <?php    
                                            }
                                            ?>
                                          <h4 class="text-dark mt-3"><?php echo $profile->name; ?></h4>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-8">
                                      <div class="admin-profile-details">
                                        <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                                          <div>
                                            <strong class="text-dark">Gender</strong>
                                          </div>
                                          <div>
                                            <p><?php echo $profile->gender; ?></p>
                                          </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between border-bottom py-3">
                                          <div>
                                            <strong class="text-dark">Mobile Number</strong>
                                          </div>
                                          <div>
                                            <p><?php echo $profile->mobile; ?></p>
                                          </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between border-bottom py-3">
                                          <div>
                                            <strong class="text-dark">Email </strong>
                                          </div>
                                          <div>
                                            <p><?php echo $profile->email; ?></p>
                                          </div>
                                        </div>
                                        <!-- <div class="d-flex align-items-center justify-content-between border-bottom py-3">
                                          <div>
                                            <strong class="text-dark">Address</strong>
                                          </div>
                                          <div>
                                            <p></p>
                                          </div>
                                        </div> -->
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>