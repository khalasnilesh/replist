<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
<div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12">
            <h4 class="">Reps Profile</h4>
            <hr>
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-5" style="border-right-style:groove;border-right-color: light-grey;border-right-width: 2px;">
                      <div class="border-bottom text-center pb-4">
                        <?php
                        if(!empty($view->u_image))
                        {
                        ?>
                          <img src="<?php echo base_url('public/profile_images/').$view->u_image; ?>" alt="profile" class="img-lg rounded-circle mb-3"/>
                        <?php
                        }
                        else
                        {
                        ?>
                          <img src="<?php echo base_url('public/admin/images/faces/face12.jpg'); ?>" alt="profile" class="img-lg rounded-circle mb-3"/>
                        <?php
                        }
                        ?>
                        <div>
                          <div class="align-items-center">
                            <h6 class="text-center text-muted">User Id : <b class="text-dark"><?php echo $view->u_id;?></b></h6>
                            <h4><?php echo $view->u_first_name.' '.$view->u_last_name;?></h4>
                          </div>
                        </div>
                      </div>
                      <div class="py-2 px-4">
                        <!-- <p class="clearfix">
                          <span class="float-left">
                          Gender
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_gender;?>
                          </span>
                        </p> -->
                        <p class="clearfix">
                          <span class="float-left">
                          Contact Number
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_mobile;?>
                          </span>
                        </p>
                        <p class="clearfix">
                          <span class="float-left">
                          Email
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_email;?>
                          </span>
                        </p>
                      </div>
                    </div>
                    <div class="col-lg-7">
                    <div class="py-4 px-4">
                        <p class="clearfix">
                          <span class="float-left">
                          company name
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_company;?>
                          </span>
                        </p>
                        <p class="clearfix">
                          <span class="float-left">
                          Business Contact Number
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_mobile;?>
                          </span>
                        </p>
                        <p class="clearfix">
                          <span class="float-left">
                          Business Type
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_business_type;?>
                          </span>
                        </p>
                        <p class="clearfix">
                          <span class="float-left">
                          Department
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_department;?>
                          </span>
                        </p>
                        <!-- <p class="clearfix">
                          <span class="float-left">
                          Customer Application
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_customer_application;?>
                          </span>
                        </p> -->
                        <p class="clearfix">
                          <span class="float-left">
                          Sales Position
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_sales_position;?>
                          </span>
                        </p>
                        <p class="clearfix">
                          <span class="float-left">
                          Area Cover
                          </span>
                          <span class="float-right text-muted">
                          <?php echo $view->u_area_cover;?>
                          </span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 mt-3">
              <div class="card">
                <div class="card-body">
                  <h4 >Additional Information</h4>
                  <hr>
                  <?php 
                     //document
                     $this->db->where('d_user_id',$view->u_id);
                     $select = $this->db->get('tbl_documents');
                     $dnum = $select->num_rows();
                     $data = $select->result_array();

                     //friend
                      $this->db->where('i_receiver_id',$view->u_id);
                      $this->db->where('i_flag','1');
                      $select = $this->db->get('tbl_invitation');
                      $num = $select->num_rows();
                      $data1 = $select->result_array();

                     //order
                      $this->db->where('o_user_id',$view->u_id);
                      $select = $this->db->get('tbl_orders');
                      $order = $select->row();
                      $orderlist = $select->result_array();
                      $onum = $select->num_rows();

                      $this->db->where('p_user_id',$view->u_id);
                      $this->db->order_by('p_user_id','desc');
                      $select = $this->db->get('tbl_product');
                      $catalog = $select->result_array();
                      $procat = $select->num_rows();
                  ?>
                  <div class="row">
                    <div class="col-md-12 mx-auto">
                      <ul class="nav nav-pills nav-pills-custom" id="pills-tab-custom" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="pills-home-tab-custom" data-toggle="pill" href="#pills-health" role="tab" aria-controls="pills-home" aria-selected="true">
                            Sales Report
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="pills-profile-tab-custom-order" data-toggle="pill" href="#pills-career" role="tab" aria-controls="pills-profile" aria-selected="false">
                            Order History
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="pills-profile-tab-custom1" data-toggle="pill" href="#pills-career1" role="tab" aria-controls="pills-profile" aria-selected="false">
                            Company Information
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="pills-contact-tab-custom" data-toggle="pill" href="#pills-music" role="tab" aria-controls="pills-contact" aria-selected="false">
                            Products
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="pills-vibes-tab-custom" data-toggle="pill" href="#pills-vibes" role="tab" aria-controls="pills-contact" aria-selected="false">
                            Documents
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="pills-energy-tab-custom" data-toggle="pill" href="#pills-energy" role="tab" aria-controls="pills-contact" aria-selected="false">
                            Friends
                          </a>
                        </li>
                      </ul>
                      <div class="tab-content tab-content-custom-pill" id="pills-tabContent-custom">
                        <div class="tab-pane fade show active" id="pills-health" role="tabpanel" aria-labelledby="pills-home-tab-custom">
                        <div class="media-body ">
                              <?php
                              if($onum>0)
                              {
                              foreach($orderlist as $olist)
                              {
                                  $this->db->where('p_id',$olist['o_product_id']);
                                  $select = $this->db->get('tbl_product');
                                  $product = $select->row();
                                 if(empty($olist['o_user_id']))
                                 {
                              ?>
                                <div class="col-md-6 float-left">
                                  <label for="">No Order </label>
                                  <h6><span class="text-secondary">Order Id: </span> </h6>
                                  <h4><span class="text-primary">Product Name: </span> </h4>
                                  <h5><span class="text-primary">Quantity: </span></h5>
                                  <h5><span class="text-primary">Total Amount: </span> </h5>
                                </div>
                                <div class="col-md-6 float-right">
                                  <h5><span class="text-primary">Ordered On: </span> </h5>
                                  <h5><span class="text-primary">Status: </span> </h5>
                                </div>
                              <?php
                                 }
                                 else
                                 {
                              ?>
                               <div class="row shadow-sm p-3 m-3 rounded">
                                <div class="col-md-6 border-right">
                                  <h6><span class="text-secondary">Order Id: </span><?php echo $olist['o_order_id']; ?></h6>
                                  <h4 class="text-primary"><span class="text-dark">Product Name: </span><?php echo $product->p_name;?></h4>
                                  <h5 class="mt-3 text-primary"><span class="text-dark">Quantity: </span><?php echo $olist['o_qty']; ?></h5>
                                  <?php
                                   $total_amount = $olist['o_qty']*$olist['o_price'];
                                  ?>
                                  <h5 class="mt-3 text-primary"><span class="text-dark">Total Amount: </span> <?php echo '$'.$total_amount; ?></h5>
                                </div>

                                <div class="col-md-6 ">
                                  <h5 class="text-primary"><span class="text-dark">Ordered On: </span><?php echo $olist['o_order_date']; ?> </h5>
                                  <?php
                                  if($olist['o_flag'] == '1')
                                  {
                                  ?>
                                    <h5 class="text-primary"><span class="text-dark">Status: </span><label class="badge badge-danger">Pending</label> </h5>
                                  <?php
                                  }
                                  elseif($olist['o_flag']  == '2')
                                  {
                                  ?>
                                    <h5 class="text-primary"><span class="text-dark">Status: </span><label class="badge badge-success">Delivered</label> </h5>
                                  <?php
                                  }
                                  elseif($olist['o_flag']  == '3')
                                  {
                                  ?>
                                    <h5><span class="text-dark">Status: </span><label class="badge badge-warning">Canceled</label> </h5>
                                  <?php
                                  }
                                  ?>
                                </div>
                               </div>
                              
                              <?php
                                 }
                              }
                            }
                            else
                            {
                            ?>
                              <h5><span class="text-primary">No Sales Report</span></h5>
                            <?php
                            }
                              ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-career" role="tabpanel" aria-labelledby="pills-profile-tab-custom-order">
                          <div class="media">
                            <div class="media-body ">
                              <?php
                              if($onum>0)
                              {
                              foreach($orderlist as $olist)
                              {
                                  $this->db->where('p_id',$olist['o_product_id']);
                                  $select = $this->db->get('tbl_product');
                                  $product = $select->row();
                                 if(empty($olist['o_user_id']))
                                 {
                              ?>
                                <div class="col-md-6 float-left">
                                  <label for="">No Order </label>
                                  <h6><span class="text-secondary">Order Id: </span> </h6>
                                  <h4><span class="text-primary">Product Name: </span> </h4>
                                  <h5><span class="text-primary">Quantity: </span></h5>
                                  <h5><span class="text-primary">Total Amount: </span> </h5>
                                </div>
                                <div class="col-md-6 float-right">
                                  <h5><span class="text-primary">Ordered On: </span> </h5>
                                  <h5><span class="text-primary">Status: </span> </h5>
                                </div>
                              <?php
                                 }
                                 else
                                 {
                              ?>
                               <div class="row shadow-sm p-3 m-3 rounded">
                                <div class="col-md-6 border-right">
                                  <h6><span class="text-secondary">Order Id: </span><?php echo $olist['o_order_id']; ?></h6>
                                  <h4 class="text-primary"><span class="text-dark">Product Name: </span><?php echo $product->p_name;?></h4>
                                  <h5 class="mt-3 text-primary"><span class="text-dark">Quantity: </span><?php echo $olist['o_qty']; ?></h5>
                                  <?php
                                   $total_amount = $olist['o_qty']*$olist['o_price'];
                                  ?>
                                  <h5 class="mt-3 text-primary"><span class="text-dark">Total Amount: </span> <?php echo '$'.$total_amount; ?></h5>
                                </div>

                                <div class="col-md-6 ">
                                  <h5 class="text-primary"><span class="text-dark">Ordered On: </span><?php echo $olist['o_order_date']; ?> </h5>
                                  <?php
                                  if($olist['o_flag'] == '1')
                                  {
                                  ?>
                                    <h5 class="text-primary"><span class="text-dark">Status: </span><label class="badge badge-danger">Pending</label> </h5>
                                  <?php
                                  }
                                  elseif($olist['o_flag']  == '2')
                                  {
                                  ?>
                                    <h5 class="text-primary"><span class="text-dark">Status: </span><label class="badge badge-success">Delivered</label> </h5>
                                  <?php
                                  }
                                  elseif($olist['o_flag']  == '3')
                                  {
                                  ?>
                                    <h5><span class="text-dark">Status: </span><label class="badge badge-warning">Canceled</label> </h5>
                                  <?php
                                  }
                                  ?>
                                </div>
                               </div>
                              
                              <?php
                                 }
                              }
                            }
                            else
                            {
                            ?>
                              <h5><span class="text-primary">No Order History</span></h5>
                            <?php
                            }
                              ?>
                            </div>
                          </div>
                        </div>
                        <div class="tab-pane fade" id="pills-career1" role="tabpanel" aria-labelledby="pills-profile-tab-custom1">
                          <div class="media">
                          <?php
                              if(!empty($view->u_company_logo))
                              {
                            ?>
                                <img class="rounded "  width="170" src="<?php echo base_url('public/company_logo/').$view->u_company_logo;?>" alt="sample image">
                            <?php
                              }
                              else
                              {
                            ?>
                                <img class="rounded m-5" width="170"  src="<?php echo base_url('public/admin/images/samples/300X300/10.jpg');?>" alt="Company image">
                            <?php
                              }
                            ?>
                            <div class="media-body ml-3">
                              <h5><span class="text-primary">Name: </span> <?php echo $view->u_company;?></h5>
                              <h5><span class="text-primary">Contact Number: </span> <?php echo $view->u_company_contact;?></h5>
                              <h5><span class="text-primary">Business Type: </span> <?php echo $view->u_business_type;?></h5>
                            </div>
                          </div>
                        </div>
                        <div class="tab-pane fade" id="pills-music" role="tabpanel" aria-labelledby="pills-contact-tab-custom">
                          <div class="media ">
                          <div class="media-body mb-3">
                              <?php
                              if($procat>0) 
                              {
                              foreach($catalog as $cat)
                              {
                              ?>
                              <div class="row shadow-sm p-3 mb-3 bg-white rounded">
                              <div class="col-md-3">
                              <?php
                                if(!empty($cat->p_image))
                                {
                                  // echo $cat->p_image;
                                  // die();
                            ?>
                                  <img class="rounded" width="170" src="<?php echo base_url('public/product_image/').$cat->p_image; ?>" alt="Friend image">
                            <?php
                                }
                                else
                                {
                            ?>
                                  <img class="rounded " width="170" src="<?php echo base_url('public/admin/images/samples/300x300/11.jpg'); ?>" alt="Friend image">
                            <?php
                                }
                            ?>
                              </div>
                              <div class="col-md-9">
                                <h5><span class="text-primary">Name : </span><?php echo $cat['p_name']; ?></h5>
                                <p><span class="text-primary">Description : </span><?php echo $cat['p_description']; ?></p>
                                <p><span class="text-primary">Price : </span><?php echo '$'.$cat['p_price']; ?></p>
                                </div>
                              </div>
                              <?php
                              }
                            }
                            else
                            {
                            ?>
                                <h5><span class="text-primary">No Products</span></h5>
                            <?php
                            }
                              ?>
                          </div>
                          </div>
                        </div>
                        <div class="tab-pane fade" id="pills-vibes" role="tabpanel" aria-labelledby="pills-vibes-tab-custom">
                        <div class="media">
                          <div class="media-body mb-3">
                          <?php
                            if($dnum!=0)
                            {
                              foreach($data as $doce)
                              {
                            ?>
                                <div class="row shadow-sm p-3 mb-3 bg-white rounded">
                                  <div class="col-md-3">
                            <?php
                                if(!empty($doce['d_files']))
                                {
                            ?>
                                  <img class="rounded" width="170" src="<?php echo base_url('public/doc_files/').$doce['d_files']; ?>" alt="Document image">
                            <?php
                                }
                                else
                                {
                            ?>
                                  <img class="rounded " width="170" src="<?php echo base_url('public/admin/images/samples/300x300/11.jpg'); ?>" alt="Document image">
                            <?php
                                }
                            ?>
                              </div>
                                <div class="col-md-9">
                                  <h5><span class="text-primary">Name: </span> <?php echo $doce['d_heading'];?></h5>
                                </div>
                                </div>
                            <?php
                              }
                            }
                            else
                            {
                            ?>
                              <div class="media-body">
                              <h5><span class="text-primary">No Document</span></h5>
                              </div>
                            <?php
                            }
                            ?>
                          </div>
                        </div>
                        </div>
                        <div class="tab-pane fade" id="pills-energy" role="tabpanel" aria-labelledby="pills-energy-tab-custom">
                          <div class="media">
                          <div class="media-body mb-3">
                            <?php
                            if($num>0)
                            {
                              foreach($data1 as $fri)
                              {
                                $this->db->where('u_id',$fri['i_sender_id']);
                                $select = $this->db->get('tbl_users');
                                $friend = $select->row();
                            ?>
                                <div class="row shadow-sm p-3 mb-3 bg-white rounded">
                                  <div class="col-md-3">
                            <?php
                                if(!empty($friend->u_image))
                                {
                            ?>
                                  <img class="rounded" width="170" src="<?php echo base_url('public/profile_images/').$friend->u_image; ?>" alt="Friend image">
                            <?php
                                }
                                else
                                {
                            ?>
                                  <img class="rounded " width="170" src="<?php echo base_url('public/admin/images/samples/300x300/11.jpg'); ?>" alt="Friend image">
                            <?php
                                }
                            ?>
                              </div>
                                <div class="col-md-9">
                                  <h5><span class="text-primary">Name: </span> <?php echo $friend->u_first_name.$friend->u_last_name;?></h5>
                                  <h5><span class="text-primary">Mobile : </span> <?php echo $friend->u_mobile;?></h5>
                                  <h5><span class="text-primary">Email : </span> <?php echo $friend->u_email;?></h5>
                                  <h5><span class="text-primary">Company : </span> <?php echo $friend->u_company;?></h5>
                                </div>
                                </div>
                            <?php
                              }
                            }
                            else
                            {
                            ?>
                              <div class="media-body">
                              <h5><span class="text-primary">No Friends</span></h5>
                              </div>
                            <?php
                            }
                            ?>
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
        <!-- content-wrapper ends -->
   
<?php $this->load->view('admin/footer'); ?>