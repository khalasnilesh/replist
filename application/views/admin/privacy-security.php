<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4 class="text-left">Privacy and Security</h4>
              <hr>
              <div class="row">
              <div class="col-md-12 col-lg-12">
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
                        <div class="card-header card-header-border-bottom">
                        <h2></h2>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo base_url(''); ?>" method="post">
                        <div class="item form-group">
                            <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Message<span class="required">*</span></label>
                            <textarea name="message" class="form-control">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta ex nisi quaerat quam cupiditate optio quo eveniet numquam distinctio reiciendis sed esse quasi obcaecati, voluptas inventore laborum. Voluptatibus, ipsum dicta culpa illum officiis recusandae iste soluta explicabo cupiditate corrupti quasi error, reiciendis assumenda autem nostrum quo, alias labore tenetur odio.</textarea>
                            <div class="text text-danger">
                            <?php echo form_error('message'); ?>
                            </div>
                        </div>
                        <div>
                        <input type="submit" value="Submit" name="submit"  class="btn btn-primary" class="submit-btn mt-4">
                        </div>
                        </form>
                    </div>
                    </div>
                </div>
                </div>
            </div>
          </div>
        </div>
          <!-- Javascript -->
  <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
  <script>
    CKEDITOR.replace( 'message' );
  </script>
<?php $this->load->view('admin/footer'); ?>