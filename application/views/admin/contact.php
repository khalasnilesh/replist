<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
            <div class="card-body">
              <h4>Contact</h4>
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table id="order-listing" class="table">
                      <thead>
                        <tr>
                            <th>Serial #</th>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                            <td>1</td>
                            <td>RP001</td>
                            <td>Edinburgh</td>
                            <td>New York</td>
                            <td>Hello</td>
                            <td>
                              <a href="<?php echo base_url('contact-reply'); ?>">
                              <button class="btn btn-outline-primary">Reply</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>RP002</td>
                            <td>Doe</td>
                            <td>Brazil</td>
                            <td>Hello</td>
                            <td>
                            <a href="<?php echo base_url('contact-reply'); ?>">
                              <button class="btn btn-outline-primary">Reply</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>RP003</td>
                            <td>Sam</td>
                            <td>Tokyo</td>
                            <td>Hello</td>
                            <td>
                            <a href="<?php echo base_url('contact-reply'); ?>">
                              <button class="btn btn-outline-primary">Reply</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>RP004</td>
                            <td>Sam</td>
                            <td>Tokyo</td>
                            <td>Hello</td>
                            <td>
                            <a href="<?php echo base_url('contact-reply'); ?>">
                              <button class="btn btn-outline-primary">Reply</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>RP005</td>
                            <td>Sam</td>
                            <td>Tokyo</td>
                            <td>Hello</td>
                            <td>
                            <a href="<?php echo base_url('contact-reply'); ?>">
                              <button class="btn btn-outline-primary">Reply</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>RP006</td>
                            <td>Sam</td>
                            <td>Tokyo</td>
                            <td>Hello</td>
                            <td>
                            <a href="<?php echo base_url('contact-reply'); ?>">
                              <button class="btn btn-outline-primary">Reply</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>RP007</td>
                            <td>Cris</td>
                            <td>Tokyo</td>
                            <td>Hello</td>
                            <td>
                            <a href="<?php echo base_url('contact-reply'); ?>">
                              <button class="btn btn-outline-primary">Reply</button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td>RP008</td>
                            <td>Tim</td>
                            <td>Italy</td>
                            <td>Hello</td>
                            <td>
                              <button class="btn btn-outline-primary">Reply</button>
                            </td>
                        </tr>
                        <tr>
                            <td>9</td>
                            <td>RP009</td>
                            <td>John</td>
                            <td>Tokyo</td>
                            <td>Hello</td>
                            <td>
                              <button class="btn btn-outline-primary">Reply</button>
                            </td>
                        </tr>
                        <tr>
                            <td>10</td>
                            <td>RP010</td>
                            <td>Tom</td>
                            <td>Germany</td>
                            <td>Hello</td>
                            <td>
                              <button class="btn btn-outline-primary">Reply</button>
                            </td>
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