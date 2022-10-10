<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Add Banner</h4>
              <hr>
              <div class="row">
                <div class="col-md-6 m-auto ">
                <form class="p-5" method="POST" action="<?php echo base_url('add-banner'); ?>" enctype="multipart/form-data" >
                <div class="form-group">
                  <select name="user" id="" class="form-control form-control-lg">
                      <option value="">Select</option>
                      <option value="Reps">Reps</option>
                      <option value="Buyer">Buyer</option>
                  </select>
                  <div class="text text-danger"><?php echo form_error('user'); ?></div>
                </div>
                <div class="form-group">
                  <input type="file" name="image" class="form-control form-control-lg"  placeholder="Category Name">
                  <div class="text text-danger"><?php echo form_error('image'); ?></div>
                </div>
                <div class="form-group">
                  <input type="text" name="title" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Title">
                  <div class="text text-danger"><?php echo form_error('title'); ?></div>
                </div>
                <div class="mt-3">
                  <input type="submit" name="submit" value="Submit" class="btn btn-block btn-primary btn-lg font-weight-medium text-light ">
                </div>
              </form>
                </div>
                </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>