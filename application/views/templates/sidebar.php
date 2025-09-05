<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <!-- <a href="" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light ">Sixty9</span>
    </a> -->

    <!-- Sidebar -->
    <div class="sidebar">
      <?php if($this->session->has_userdata('authenticated')) { ?>
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 d-flex">
          <div class="image">
            <img src="<?= $this->session->userdata('auth_user')['picture']; ?>" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="<?= base_url('profile')?>" class="d-block">
              <?= $this->session->userdata('auth_user')['user']; ?>
            </a>
          </div>
        </div>
      <?php } ?>
      <!-- SidebarSearch Form -->
      <div class="form-inline mt-3">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?= base_url('welcome')?>" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inicio</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('proximamente')?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>...</p>
                </a>
              </li>
              <?php if($this->session->userdata('authenticated') == '1') { ?>
              <li class="nav-item">
                <a href="#" id="sendMailjetBtn" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Mailjet</p>
                </a>
              </li>
              <?php } ?>
              <?php if($this->session->userdata('authenticated') == '1') { ?>
              <li class="nav-item">
                <a href="#" id="wol-btn" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>WOL</p>
                </a>
              </li>
              <?php } ?>
            </ul>
          </li>
          <li class="nav-header">UTILITIES</li>
          <li class="nav-item">
            <a href="<?= base_url('proximamente')?>" class="nav-link">
              <i class="nav-icon far fa-calendar-alt"></i>
              <p>
                Calendar
                <span class="badge badge-info right">2</span>
              </p>
            </a>
          </li>
          <?php if($this->session->userdata('authenticated') == 'tester' || $this->session->userdata('authenticated') == '1') { ?>
            <li class="nav-item">
              <a href="<?= base_url('kanban') ?>" class="nav-link">
                <i class="nav-icon fas fa-columns"></i>
                <p>
                  Kanban Board
                </p>
              </a>
            </li>
          <?php } ?>
          <!-- <li class="nav-header">MISCELLANEOUS</li>
          <li class="nav-item">
            <a href="iframe.html" class="nav-link">
              <i class="nav-icon fas fa-ellipsis-h"></i>
              <p>Tabbed IFrame Plugin</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file"></i>
              <p>Documentation</p>
            </a>
          </li> -->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>