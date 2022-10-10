<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Rewards</h4>
              <hr>
              <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                          <thead>
                            <tr class="bg-dark text-white">
                                <th>Sr.No.</th>
                                <th>User Id</th>
                                <th>Reward Point</th>
                                <th>Purchase Date</th>
                                <!-- <th>Actions</th> -->
                            </tr>
                          </thead>
                          <tbody>
                                <tr>
                                  <td>1</td>
                                  <td>RP1</td>
                                  <td>20</td>
                                  <td>05/23/2022</td>
                                </tr>
                                <tr>
                                  <td>2</td>
                                  <td>RP2</td>
                                  <td>30</td>
                                  <td>05/23/2022</td>
                                </tr><tr>
                                  <td>3</td>
                                  <td>RP3</td>
                                  <td>50</td>
                                  <td>05/23/2022</td>
                                </tr><tr>
                                  <td>4</td>
                                  <td>RP4</td>
                                  <td>20</td>
                                  <td>05/23/2022</td>
                                </tr><tr>
                                  <td>5</td>
                                  <td>RP5</td>
                                  <td>80</td>
                                  <td>05/23/2022</td>
                                </tr><tr>
                                  <td>6</td>
                                  <td>RP6</td>
                                  <td>10</td>
                                  <td>05/23/2022</td>
                                </tr><tr>
                                  <td>7</td>
                                  <td>RP7</td>
                                  <td>90</td>
                                  <td>05/23/2022</td>
                                </tr><tr>
                                  <td>8</td>
                                  <td>RP8</td>
                                  <td>80</td>
                                  <td>05/23/2022</td>
                                </tr><tr>
                                  <td>9</td>
                                  <td>RP9</td>
                                  <td>120</td>
                                  <td>05/23/2022</td>
                                </tr><tr>
                                  <td>10</td>
                                  <td>RP10</td>
                                  <td>202</td>
                                  <td>05/23/2022</td>
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