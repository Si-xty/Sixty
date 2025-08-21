<!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li> 
    </ul>
    <a>Mis planes</a>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <div class="col-sm-12 text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#createBoardModal">
                <i class="fas fa-plus"></i> Nuevo plan
            </button>
        </div>
      </li>

      <?php if($this->session->has_userdata('authenticated')) { ?>
      <li class="nav-item">
        <a class="nav-link" data-widget="logout" href="<?= base_url('logout'); ?>" role="button">
          <i class="fas fa-sign-out-alt"></i> 
        </a>
      </li>
      <?php } ?>
  </nav>
  <!-- /.navbar -->