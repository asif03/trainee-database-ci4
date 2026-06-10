<?php
    $auth = service('auth');
    $user = $auth->user();

    /*echo '<pre>';
    print_r($user);
    echo '</pre>';
    die;*/
?>
<div class="sidebar sidebar-style-2" data-background-color="white">
  <div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="white">
      <a href="<?php echo base_url(); ?>" class="logo">
        <img src="<?php echo base_url(); ?>public/logo.png" alt="navbar brand" class="navbar-brand" height="36"
          width="36" />
        <span class="fs-3 fw-bold ps-2">BCPS</span>
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        <li class="nav-item <?=set_active('dashboard', true)?>">
          <a href="<?=base_url('dashboard')?>">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <?php if ($user && $user->inGroup('superadmin', 'admin', 'rtm-admin', 'rtm-user', 'user')) {?>
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-user" aria-hidden="true"></i>
          </span>
          <h4 class="text-section">Training Database</h4>
        </li>
        <?php if ($user && $user->inGroup('superadmin', 'admin')) {?>
        <li class="nav-item <?=set_active('fcps-part-one/passed-candidates')?>">
          <a href="<?=base_url('fcps-part-one/passed-candidates')?>">
            <i class="fa fa-users" aria-hidden="true"></i>
            <p>Part-I Passed Candidates</p>
          </a>
        </li>
        <li class="nav-item <?=set_active('trainings/trainee-list')?>">
          <a href="<?=base_url('trainings/trainee-list')?>">
            <i class="fa fa-book" aria-hidden="true"></i>
            <p>Progress Reports</p>
          </a>
        </li>
        <?php }?>

        <?php if ($user && $user->inGroup('user')) {?>
        <li class="nav-item <?=set_active('trainings/basic-info')?>">
          <a href="<?=base_url('trainings/basic-info')?>">
            <i class="fa fa-user-md" aria-hidden="true"></i>
            <p>Basic Information</p>
          </a>
        </li>
        <?php if ($user && $user->inGroup('superadmin')) {?>
        <li class="nav-item <?=set_active('trainings/progress-reports')?>">
          <a href="<?=base_url('trainings/progress-reports')?>">
            <i class="fa fa-clipboard" aria-hidden="true"></i>
            <p>Progress Report</p>
          </a>
        </li>
        <?php }}}?>

        <?php if ($user && $user->inGroup('superadmin', 'admin', 'rtm-admin', 'rtm-user', 'user')) {?>
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">Honorarium Info</h4>
        </li>
        <?php if ($user && $user->inGroup('user')) {?>
        <li class="nav-item <?=set_active('trainings/training-application')?>">
          <a href="<?=base_url('trainings/training-application')?>">
            <i class="fa fa-stethoscope" aria-hidden="true"></i>
            <p>Training Application</p>
          </a>
        </li>
        <li class="nav-item <?=set_active('trainings/honorarium-bill-application')?>">
          <a href="<?=base_url('trainings/honorarium-bill-application')?>">
            <i class="fa fa-money-bill"></i>
            <p>Honararium Bill Application</p>
          </a>
        </li>
        <?php }?>
        <?php if ($user && $user->inGroup('superadmin', 'admin', 'rtm-admin', 'rtm-user')) {?>
        <li class="nav-item <?=set_active('applications')?> submenu">
          <a data-bs-toggle="collapse" href="#sidebarApplications">
            <i class="fas fa-th-list"></i>
            <p>Applications</p>
            <span class="caret"></span>
          </a>
          <div class="collapse <?=set_show('applications')?>" id="sidebarApplications">
            <ul class="nav nav-collapse">
              <li class="<?=set_active('applications')?>">
                <a href="<?=base_url('applications')?>">
                  <span class="sub-item">Applicant List</span>
                </a>
              </li>
              <!-- <li>
                <a href="icon-menu.html">
                  <span class="sub-item">Icon Menu</span>
                </a>
              </li> -->
            </ul>
          </div>
        </li>
        <li class="nav-item <?=set_active('bills')?> submenu">
          <a data-bs-toggle="collapse" href="#sidebarBills">
            <i class="fas fa-th-list"></i>
            <p>Bills</p>
            <span class="caret"></span>
          </a>
          <div class="collapse <?=set_show('bills')?>" id="sidebarBills">
            <ul class="nav nav-collapse">
              <li class="<?=set_active('bills')?>">
                <a href="<?=base_url('bills')?>">
                  <span class="sub-item">Honorarium List</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <?php }?>
        <?php if ($user && $user->inGroup('superadmin')) {?>
        <li class="nav-item <?=set_active('reports')?> submenu">
          <a data-bs-toggle="collapse" href="#sidebarReports">
            <i class="fas fa-file-alt"></i>
            <p>Reports</p>
            <span class="caret"></span>
          </a>
          <div class="collapse <?=set_show('reports')?>" id="sidebarReports">
            <ul class="nav nav-collapse">
              <li class="<?=set_active('reports/applications')?>">
                <a href="<?=base_url('reports/applications')?>">
                  <span class="sub-item">Application Report</span>
                </a>
              </li>
              <li class="<?=set_active('reports/bills')?>">
                <a href="<?=base_url('reports/bills')?>">
                  <span class="sub-item">Bill Report</span>
                </a>
              </li>
              <!-- <li>
                <a href="icon-menu.html">
                  <span class="sub-item">Icon Menu</span>
                </a>
              </li> -->
            </ul>
          </div>
        </li>
        <?php
            }}
        ?>
        <?php if ($user && $user->inGroup('superadmin')) {?>
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">Settings</h4>
        </li>
        <li class="nav-item <?=set_active('users')?>">
          <a data-bs-toggle="collapse" href="#userManagement">
            <i class='fas fa-user-cog'></i>
            <p>User Management</p>
            <span class="caret"></span>
          </a>
          <div class="collapse <?=set_show('users')?>" id="userManagement">
            <ul class="nav nav-collapse">
              <li>
                <a href="forms/forms.html">
                  <span class="sub-item">User List</span>
                </a>
              </li>
              <li class="<?=set_active('users/assign-user-role')?>">
                <a href="<?=base_url('users/assign-user-role')?>">
                  <span class="sub-item">Assign Role</span>
                </a>
              </li>
              <li class="<?=set_active('users/assign-user-role')?>">
                <a href="<?=base_url('superadmin/db-seed')?>">
                  <span class="sub-item">Part-I User Creation</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <?php }?>
      </ul>
    </div>
  </div>
</div>