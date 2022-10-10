<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Sales Report</h4>
              <hr>
              <div class="row">
              <div class="col-md-12">
                      <div class="table-responsive">
                        <table id="order-listing" class="table">
                          <thead>
                            <tr class="bg-dark text-white">
                                <th>Sr.#</th>
                                <th>From date - To date</th>
                                <th>No. of Orders</th>
                                <th>Total Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                                <tr>
                                  <td>1</td>
                                  <td>01/01/2022 - 31/04/2022</td>
                                  <td>100</td>
                                  <td>$10000</td>
                              </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
              </div>
            </div>
          </div>
        </div>
<?php $this->load->view('admin/footer'); ?>