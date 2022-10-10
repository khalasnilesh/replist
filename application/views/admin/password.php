<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Change Password</h4>
              <hr>
              <div class="row">
              <div class="col-md-12 col-lg-8 offset-lg-2">
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
                          elseif(!empty($this->session->flashdata('warning')))
                          {
                            ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?php echo $this->session->flashdata('warning'); ?>
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
                            <div class="card card-default">
                                <div class="card-body">
                                    <div>
                                      <form action="<?php echo base_url('change-password/').$profile->id; ?>" method="POST">
                                        <div class="form-group">
                                            <label for="">Enter Current Password</label>
                                            <input type="password" name="old" class="form-control" value="<?php echo set_value('old')?>">
                                            <div class="text text-danger"><?php echo form_error('old'); ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Enter New Password</label>
                                            <input type="password" name="new" class="form-control" value="<?php echo set_value('new')?>">
                                            <div class="text text-danger"><?php echo form_error('new'); ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">
                                            Confirm New Password</label>
                                            <input type="password" name="confirm" class="form-control" value="<?php echo set_value('confirm')?>">
                                            <div class="text text-danger"><?php echo form_error('confirm'); ?></div>
                                        </div>
                                    <div >
                                        <input type="submit" class="btn bg-primary text-light" value="Submit" name="update">
                                    </div>
                                      </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>