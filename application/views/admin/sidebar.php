<nav class="sidebar sidebar-offcanvas " id="sidebar">
        <ul class="nav mt-3">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('dashboard'); ?>">
              <i class="mdi mdi-shield-half-full menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#editors" aria-expanded="false" aria-controls="editors">
              <i class="mdi mdi-cube-outline menu-icon"></i>
              <span class="menu-title">Content Management</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="editors">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('banner'); ?>">Manage Banner</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('privacy-security'); ?>">Privacy & Security</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('faq'); ?>">FAQs</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link"  href="<?php echo base_url('reps'); ?>" >
              <i class="mdi mdi-checkbox-blank-circle-outline menu-icon"></i>
              <span class="menu-title">Reps</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link"  href="<?php echo base_url('buyer'); ?>" >
              <i class="mdi mdi-shopping menu-icon"></i>
              <span class="menu-title">Buyers</span>
            </a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('report'); ?>" >
              <i class="mdi mdi-view-headline menu-icon"></i>
              <span class="menu-title">Reports</span>
            </a>
          </li> -->
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#repo" aria-expanded="false" aria-controls="repo">
              <i class="mdi mdi-file menu-icon"></i>
              <span class="menu-title">Reports</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="repo">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('sales-report'); ?>">Sales Report</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('purchase-report'); ?>">Purchase Report</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#editors2" aria-expanded="false" aria-controls="editors2">
              <i class="mdi mdi-pencil menu-icon"></i>
              <span class="menu-title">Orders</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="editors2">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('order-history/1'); ?>">Pending</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('order-history/2'); ?>">Delivered</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('order-history/3'); ?>">Canceled</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('category'); ?>">
              <i class="mdi mdi-lan menu-icon"></i>
              <span class="menu-title">Category</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('product'); ?>">
              <i class="mdi mdi-poll menu-icon"></i>
              <span class="menu-title">Products</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('document'); ?>">
              <i class="mdi mdi-book menu-icon"></i>
              <span class="menu-title">Documents</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('reward'); ?>">
              <i class="mdi mdi-guitar-pick menu-icon"></i>
              <span class="menu-title">Rewards</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('helpsupport'); ?>">
              <i class="mdi mdi-view-list menu-icon"></i>
              <span class="menu-title">Help & Support</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('contact');?>">
              <i class="mdi mdi-comment-alert menu-icon"></i>
              <span class="menu-title">Contact Us</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admi" aria-expanded="false" aria-controls="admi">
              <i class="mdi mdi-account-cog menu-icon"></i>
              <span class="menu-title">Admin Settings</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admi">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('admin-profile'); ?>">Admin Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('password'); ?>">Change Password</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url('logout');?>">
              <i class="mdi mdi-logout menu-icon"></i>
              <span class="menu-title">Log Out</span>
            </a>
          </li>
        </ul>
      </nav>