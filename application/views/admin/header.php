<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Replist | Dashboard</title>
  <!-- base:css -->
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css">
  <!-- <link rel="stylesheet" href="<?php echo base_url('public/admin/vendors/mdi/css/materialdesignicons.min.css');?>"> -->
  <link rel="stylesheet" href="<?php echo base_url('public/admin/vendors/flag-icon-css/css/flag-icon.min.css');?>">
  <link rel="stylesheet" href="<?php echo base_url('public/admin/vendors/css/vendor.bundle.base.css'); ?>">
  <!-- endinject -->
  <link rel="stylesheet" href="<?php echo base_url('public/admin/vendors/datatables.net-bs4/dataTables.bootstrap4.css'); ?>">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" >
  
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="<?php echo base_url('public/admin/vendors/flag-icon-css/css/flag-icon.min.css'); ?>"/>
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo base_url('public/admin/css/vertical-layout-light/style.css'); ?>">
  <!-- endinject -->
  <link rel="shortcut icon"  href="<?php echo base_url('public/admin/logo-dark.png'); ?>" />
</head>
<body class="sidebar-dark">
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="<?php echo base_url('dashboard'); ?>"><img src="<?php echo base_url('public/admin/logo-lg.png')?>" style="height: 70px !important;" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="<?php echo base_url('dashboard'); ?>"><img src="<?php echo base_url('public/admin/logo-ms.png')?>" alt="logo"/></a>
        
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link" href="#" data-toggle="dropdown" id="profileDropdown">
            <?php
            $this->db->select('*');
            $select = $this->db->get('admin');
            $data = $select->row();

            if(!empty($data->image))
            {
            ?>
              <img src="<?php echo base_url('public/admin/images/admin/').$data->image;?>" alt="profile"/>
            <?php
            }
            else
            {
            ?>
              <img src="<?php echo base_url('public/admin/images/faces/face5.jpg');?>" alt="profile"/>
            <?php
            }
            ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="<?php echo base_url('admin-profile'); ?>">
                <i class="mdi mdi-account text-primary"></i>
                Admin Profile
              </a>
              <a class="dropdown-item" href="<?php echo base_url('password'); ?>">
                <i class="mdi mdi-key text-primary"></i>
                Change Password
              </a>
              <a class="dropdown-item" href="<?php echo base_url('logout'); ?>">
                <i class="mdi mdi-logout text-primary"></i>
                Logout
              </a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">