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
            <?php 
              $auth = $this->session->userdata('auth_user');
              $userPicture = isset($auth['picture']) && !empty($auth['picture']) 
                ? $auth['picture'] 
                : base_url('favicon.png'); // Placeholder seguro si no hay foto
            ?>
            <img src="<?= $userPicture; ?>" class="img-circle elevation-2" alt="User Image">
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
        <?php $currentClass = $this->router->fetch_class(); ?>
          <?php 
            $currentClass = $this->router->fetch_class(); 
            $firstSegment = $this->uri->segment(1);
            $isInicioActive = (
              $firstSegment === null || $firstSegment === '' || $firstSegment === '/' ||
              in_array(strtolower($firstSegment), ['welcome', 'home', 'inicio']) ||
              in_array($currentClass, ['MainController', 'Welcome', 'WelcomeController'])
            );
          ?>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Menú
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?= base_url('welcome')?>" class="nav-link <?= $isInicioActive ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inicio</p>
                </a>
              </li>
              <?php if($this->session->userdata('authenticated') == '1') { ?>
              <li class="nav-item">
                <a href="<?= base_url('dashboard')?>" class="nav-link <?= ($currentClass === 'DashboardController') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <?php } ?>
              <?php if($this->session->userdata('authenticated') == '1') { ?>
              <li class="nav-item">
                <a href="#" id="sendMailjetBtn" class="nav-link <?= ($currentClass === 'MailController') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Mailjet</p>
                </a>
              </li>
              <?php } ?>
              <?php if($this->session->userdata('authenticated') == '1') { ?>
              <li class="nav-item">
                <a href="#" id="wol-btn" class="nav-link <?= ($currentClass === 'WolController') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>WOL</p>
                </a>
              </li>
              <?php } ?>
              <li class="nav-item">
                <a href="<?= base_url('proximamente')?>" class="nav-link <?= ($currentClass === 'ProximamenteController') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>...</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-header">UTILITIES</li>
          <li class="nav-item">
            <a href="<?= base_url('proximamente')?>" class="nav-link <?= ($currentClass === 'CalendarController') ? 'active' : '' ?>">
              <i class="nav-icon far fa-calendar-alt"></i>
              <p>
                Calendar
                <span class="badge badge-info right">2</span>
              </p>
            </a>
          </li>
          <?php if($this->session->userdata('authenticated') == 'tester' || $this->session->userdata('authenticated') == '1') { ?>
            <li class="nav-item">
              <a href="<?= base_url('kanban') ?>" class="nav-link <?= ($currentClass === 'KanbanController') ? 'active' : '' ?>">
                <i class="nav-icon fas fa-columns"></i>
                <p>
                  Kanban Board
                </p>
              </a>
            </li>
          <?php } ?>
          <li class="nav-item">
              <a href="<?= base_url('mapa') ?>" class="nav-link <?= ($currentClass === 'MapaController') ? 'active' : '' ?>">
                <i class="nav-icon fas fa-heartbeat"></i>
                <p>
                  Mapa DEA
                </p>
              </a>
            </li>
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