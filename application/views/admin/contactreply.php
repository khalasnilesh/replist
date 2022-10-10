<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Give Solution</h4>
              <hr>
              <div class="row ">
                  <div class="col-md-8 m-auto">
                  <form class="forms-sample">
                    <div class="form-group">
                      <label for="exampleInputUsername1">Reply Query</label> 
                      <textarea class="form-control" style="border: 3px solid lightgrey;" id="exampleInputUsername1" cols="10" rows="13"></textarea>
                    </div>
                    <a href="">
                    <button type="submit" class="btn btn-primary float-right">Submit</button></a>
                  </form>
                  </div>
              </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>