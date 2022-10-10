<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4 class="text-center">FAQS</h4>
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
                                <div class="card-header card-header-border-bottom d-flex align-items-center justify-content-between">
                                    <div>
                                      <h3></h3>
                                    </div>
                                    <div class="">
                                      <a class="btn btn-success" href="<?php echo base_url('add-faq'); ?>"><i class="mdi mdi-plus mr-2"></i>Add FAQS</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                  <div>
                                    <div id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="headingOne1">                                     
                                          <h4 class="mb-0">
                                            <button class="btn btn-link text-dark" data-toggle="collapse" data-target="#collapseOne1" aria-expanded="true" aria-controls="collapseOne1">
                                             <i class="fa-solid fa-chevron-down mr-5" style="font-size: 25px;"></i>
                                             Question One
                                            </button>
                                          </h4>
                                        </div>
                                        <div id="collapseOne1" class="collapse " aria-labelledby="headingOne1" data-parent="#accordion">
                                          <div class="card-body">
                                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorem, perferendis dolores vel eveniet eaque eius perspiciatis! Doloremque nostrum totam adipisci.
                                          </div>
                                        </div>
                                      </div>
                                      <div class="card">
                                        <div class="card-header" id="headingOne2">                                     
                                          <h4 class="mb-0">
                                            <button class="btn btn-link text-dark" data-toggle="collapse" data-target="#collapseOne2" aria-expanded="true" aria-controls="collapseOne2">
                                             <i class="fa-solid fa-chevron-down mr-5" style="font-size: 25px;"></i>
                                             Question Two
                                            </button>
                                          </h4>
                                        </div>
                                        <div id="collapseOne2" class="collapse " aria-labelledby="headingOne2" data-parent="#accordion">
                                          <div class="card-body">
                                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorem, perferendis dolores vel eveniet eaque eius perspiciatis! Doloremque nostrum totam adipisci.
                                          </div>
                                        </div>
                                      </div>
                                      <div class="card">
                                        <div class="card-header" id="headingOne3">                                     
                                          <h4 class="mb-0">
                                            <button class="btn btn-link text-dark" data-toggle="collapse" data-target="#collapseOne3" aria-expanded="true" aria-controls="collapseOne3">
                                             <i class="fa-solid fa-chevron-down mr-5" style="font-size: 25px;"></i>
                                             Question Three
                                            </button>
                                          </h4>
                                        </div>
                                        <div id="collapseOne3" class="collapse " aria-labelledby="headingOne3" data-parent="#accordion">
                                          <div class="card-body">
                                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorem, perferendis dolores vel eveniet eaque eius perspiciatis! Doloremque nostrum totam adipisci.
                                          </div>
                                        </div>
                                      </div>
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