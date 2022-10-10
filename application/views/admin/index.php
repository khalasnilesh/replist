<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Replist Admin</title>
  <!-- base:css -->
  <link rel="stylesheet" href="<?php echo base_url('public/admin/vendors/mdi/css/materialdesignicons.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('public/admin/vendors/flag-icon-css/css/flag-icon.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('public/admin/vendors/css/vendor.bundle.base.css'); ?>">
  <!-- endinject -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo base_url('public/admin/css/vertical-layout-light/style.css'); ?>">
  <!-- endinject -->
  <link rel="shortcut icon"  href="<?php echo base_url('public/admin/logo-dark.png'); ?>" />

</head>

<body class="sidebar-dark">
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <?php
              if(!empty($this->session->flashdata('danger')))
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
              ?>
               <img src="public/admin/logo-sm.png" class="mx-auto d-block" alt="logo">
               <h3 class="text-center">Replist Admin</h3>
              </div>
              <!-- <h4 class="text-center">Hello! let's get started</h4>
              <h6 class="font-weight-light text-center">Sign in to continue.</h6> -->
              <form class="pt-1" method="POST" action="<?php echo base_url('login'); ?>" >
                <div class="form-group">
                  <label for="exampleInputEmail1"><b>Username</b> </label>
                  <input type="email" name="email" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Username">
                  <div class="text text-danger"><?php echo form_error('email'); ?></div>
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1" > <b>Password</b> </label>
                  <input type="password" name="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password">
                  <div class="text text-danger"><?php echo form_error('password'); ?></div>
                </div>
                <div class="mt-3">
                  <input type="submit" name="login" value="SIGN IN" class="btn btn-block btn-primary btn-lg font-weight-medium text-light ">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="<?php echo base_url('public/admin/vendors/js/vendor.bundle.base.js'); ?>"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="<?php echo base_url('public/admin/js/off-canvas.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/js/hoverable-collapse.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/js/template.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/js/settings.js'); ?>"></script>
  <script src="<?php echo base_url('public/admin/js/todolist.js'); ?>"></script>
  <!-- endinject -->
</body>
</html>
