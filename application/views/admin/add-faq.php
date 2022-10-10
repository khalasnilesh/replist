<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Add FAQs</h4>
              <hr>
              <div class="row">
                <div class="col-md-8 m-auto shadow ">
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
                <form class="p-5" method="POST" action="<?php echo base_url('add-category'); ?>" enctype="multipart/form-data" >
                <div class="form-group">
                  <input type="text" name="question" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Question">
                  <div class="text text-danger"><?php echo form_error('name'); ?></div>
                </div>
                <div class="form-group">
                    <textarea name="answer" id="" cols="30" rows="10"  class="form-control form-control-lg"  placeholder="Answer"></textarea>
                  <div class="text text-danger"><?php echo form_error('answer'); ?></div>
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