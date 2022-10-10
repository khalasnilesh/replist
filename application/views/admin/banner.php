<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Banners</h4>
              <hr>
              <div class="row">
                  <div class="col-md-12 mb-4">
                      <a href="<?php echo base_url('add-banner'); ?>" class="float-right btn btn-success"><i class="mdi mdi-plus "></i>Add-Banner</a>
                  </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                          <thead>
                            <tr class="bg-dark text-white">
                                <th>Sr.No.</th>
                                <th>User Name</th>
                                <th>Title</th>
                                <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $count = 1;
                            foreach($banner as $values)
                            {
                            ?>
                                <tr>
                                  <td><?php echo $count++; ?></td>
                                  <td><?php echo $values['b_user'];?></td>
                                  <td><?php echo $values['b_title'];?></td>
                                  <td class="text-right">
                                    <a class="btn btn-light p-2" href="<?php echo base_url('edit-banner/').$values['b_id'];?>">
                                        <i class="mdi mdi-pen text-primary"></i> Edit
                                    </a>
                                  </td>
                                </tr>
                            <?php
                            }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>