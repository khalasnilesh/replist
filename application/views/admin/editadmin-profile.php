<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Edit Admin Profile</h4>
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
                            else
                            {
                                echo "";
                            }
                            ?>
                            <div class="card card-default">
                                <div class="card-header card-header-border-bottom ">
                                    <div>
                                      <h2></h2>
                                    </div>
                                </div>
                                <div class="card-body">
                                <form action="<?php echo base_url('update-admin-profile/').$profile->id; ?>" method="POST" enctype="multipart/form-data">
                                <div class="upload-image-box">
                                  <img id="img-upload" width="150" height="150" style="border: 1px solid gray;" />
                                  <br>  
                                  <input type="file" name="image" class="mb-2 mt-2" onchange="document.getElementById('img-upload').src = window.URL.createObjectURL(this.files[0])">
                                  <br>
                                  <strong class="text-dark">Change Profile Image <span class="text-muted">(150px X 150px)</span></strong>
                              </div>
                              <hr>
                              <div class="form-group">
                                  <label for="">Name</label>
                                  <input type="text" value="<?php echo $profile->name; ?>" class="form-control" name="name" id="">
                              </div>
                              <div class="form-group">
                                <label for="">Gender</label>
                                  <select name="gender" id="" class="form-control">
                                  <option value=""><?php echo $profile->gender; ?></option>
                                  <option value="male">Male</option>
                                  <option value="female" >Female</option>
                                  </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Mobile Number</label>
                                    <input type="text" class="form-control" value="<?php echo $profile->mobile; ?>" name="mobile" id="">
                                </div>
                                <div class="form-group">
                                  <label for="">Email   </label>
                                  <input type="text" class="form-control" value="<?php echo $profile->email; ?>" name="email" id="">
                                </div>
                                <!-- <div class="form-group">
                                    <label for="">Address</label>
                                    <textarea name="address"  id="" cols="0" rows="5" class="form-control ">
                                    <?php echo $profile->a_address; ?>
                                    </textarea>
                                </div> -->
                                <div >
                                    <input type="submit" class="btn bg-primary text-white" value="Submit" name="update">
                                </div>
                            </form>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>