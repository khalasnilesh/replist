<?php $this->load->view('admin/header.php');?>
<?php $this->load->view('admin/sidebar'); ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
        <div class="card">
        <div class="card-body">
        <h4>Order Details</h4>
        <hr>
        <?php 
            $this->db->where('p_id',$view->o_product_id);
            $select = $this->db->get('tbl_product');
            $product = $select->row();

            //Total Amount
            $total = $view->o_qty*$view->o_price;  
        ?>
                                <div class="row align-item-center">
                                    <div class="col-md-4">
                                        <div class="product-image">
                                            <?php
                                            if(!empty($product->p_image))
                                            {
                                            ?>
                                                <img src="<?php echo base_url('public/product_image/').$product->p_image; ?>" class="img-fluid" alt="productimage">
                                            <?php
                                            }
                                            else
                                            {
                                            ?>
                                                <img src="<?php echo base_url('public/admin/images/carousel/banner_1.jpg')?>" class="img-fluid" alt="productimage">
                                            <?php 
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="product-history">
                                            <div class="product-history-details d-flex align-item-center py-2">
                                                <div style="flex-basis: 20%;">
                                                    <strong>Product Name : </strong>
                                                </div>
                                                <div>
                                                    <p class="m-0"><?php echo $product->p_name; ?></p>
                                                </div>
                                            </div>
                                            <div class="product-history-details d-flex align-item-center py-2">
                                                <div style="flex-basis: 20%;">
                                                    <strong>Order ID : </strong>
                                                </div>
                                                <div>
                                                    <p class="m-0"><?php echo $view->o_order_id;  ?></p>
                                                </div>
                                            </div>
                                            <div class="product-history-details d-flex align-item-center py-2">
                                                <div style="flex-basis: 20%;">
                                                    <strong>Quantity : </strong>
                                                </div>
                                                <div>
                                                    <p class="m-0"><?php echo $view->o_qty;  ?></p>
                                                </div>
                                            </div>
                                            <div class="product-history-details d-flex align-item-center py-2">
                                                <div style="flex-basis: 20%;">
                                                    <strong>Total Amount : </strong>
                                                </div>
                                                <div>
                                                    <p class="m-0"><?php echo '$'.$total; ?></p>
                                                </div>
                                            </div>
                                            <div class="product-history-details d-flex align-item-center py-2">
                                                <div style="flex-basis: 20%;">
                                                    <strong>Order Date : </strong>
                                                </div>
                                                <div>
                                                    <p class="m-0"><?php echo $view->o_order_date;  ?></p>
                                                </div>
                                            </div>
                                            <div class="product-history-details d-flex align-item-center py-2">
                                                <div style="flex-basis: 20%;">
                                                    <strong>Status : </strong>
                                                </div>
                                                <div>
                                                <?php
                                                if($view->o_flag == '1')
                                                {
                                                ?>
                                                    <label class="badge badge-danger">Pending</label>
                                                <?php
                                                }
                                                elseif($view->o_flag == '2')
                                                {
                                                ?>
                                                    <label class="badge badge-success">Delivered</label>
                                                <?php
                                                }
                                                elseif($view->o_flag == '3')
                                                {
                                                ?>
                                                    <label class="badge badge-warning">Canceled</label>
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
<?php $this->load->view('admin/footer'); ?>