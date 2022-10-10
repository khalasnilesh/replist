<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Products</h4>
              <hr>
              <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                          <thead>
                            <tr class="bg-dark text-white">
                                <th>Sr.No.</th>
                                <th>Product Id</th>
                                <th>Product Name</th>
                                <th>Description</th>
                                <th>Price </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $count = 1;
                            foreach($product as $values)
                            {
                            ?>
                                <tr>
                                  <td><?php echo $count++; ?></td>
                                  <td><?php echo 'PR0'.$values['p_id'];?></td>
                                  <td><?php echo $values['p_name']; ?></td>
                                  <td><?php echo $values['p_description']; ?></td>
                                  <td><?php echo '$'.$values['p_price']; ?></td>
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