<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Edit Category</h4>
              <hr>
              <div class="row">
                <div class="col-md-6 m-auto ">
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
                <form class="p-5" method="POST" action="<?php echo base_url('edit-category/').$view->c_id; ?>"  enctype="multipart/form-data" >
                <div class="form-group">
                  <input type="text" name="name" value="<?php echo $view->c_name; ?>" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Category Name">
                  <div class="text text-danger"><?php echo form_error('name'); ?></div>
                </div>
                <div class="form-group">
                  <input type="file" name="image" class="form-control form-control-lg"  placeholder="Category Name">
                  <div class="text text-danger"><?php echo form_error('image'); ?></div>
                </div>
                <div class="form-group">
                  <select name="status" class="form-control form-control-lg">
                 
                    <option value="">
                        <?php
                        if($view->c_status == 1)
                        {
                        ?>
                        Active
                        <?php
                        }
                        else
                        {
                        ?>
                        Inactive
                        <?php
                        }
                        ?> 
                    </option>
                    <option value="1">Active</option>
                    <option value="0">InActive</option>
                  </select>
                  <div class="text text-danger"><?php echo form_error('status'); ?></div>
                </div>
                <div class="mt-3">
                  <input type="submit" name="update" value="Update" class="btn btn-block btn-primary btn-lg font-weight-medium text-light ">

                </div>
              </form>
                </div>
                </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>