<footer class="main-footer">
    <strong><a href="https://sixty.cl">Sixty</a> Copyright &copy; 2025 </strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <!-- <b>Version</b> 3.2.0 -->
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
<?php
  $is_login_page = isset($body) && strpos($body, 'login-page') !== false;
  if (!$is_login_page) {
      echo "</div>\n<!-- ./wrapper -->";
  }
?>
<?php if ($is_login_page): ?>

<style>
  body.login-page .main-footer {
    margin-left: 0 !important;
    width: 100%;
    text-align: center;
    background-color: transparent;
    border-top: none;
    color: #6c757d;
    padding: 1rem 0;
  }
  body.login-page .login-box {
    margin-bottom: 2rem;
  }
  /* Evitar desplazamientos por reglas de AdminLTE en tamaños md+ */
  @media (min-width: 768px) {
    body.login-page .main-footer {
      margin-left: 0 !important;
    }
  }
  /* Alinear el bloque derecho del footer en móviles si existiera */
  body.login-page .main-footer .float-right {
    float: none !important;
    display: inline-block;
  }
  body.login-page .main-footer strong, 
  body.login-page .main-footer a {
    color: inherit;
  }
  /* Opcional: limitar ancho visual del footer para alinearse con la card */
  /* body.login-page .main-footer { max-width: 370px; margin: 0 auto; } */
  /* Si deseas que el footer quede fijo abajo, descomenta:
  body.login-page .main-footer { position: fixed; bottom: 0; left: 0; right: 0; }
  */
</style>
<?php endif; ?>

<!-- jQuery -->
<script src="<?= base_url('plugins/jquery/jquery.min.js') ?>"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url('plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- ChartJS -->
<script src="<?= base_url('plugins/chart.js/Chart.min.js') ?>"></script>
<!-- Sparkline -->
<script src="<?= base_url('plugins/sparklines/sparkline.js') ?>"></script>
<!-- JQVMap -->
<script src="<?= base_url('plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= base_url('plugins/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
<!-- jQuery Knob Chart -->
<script src="<?= base_url('plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
<!-- daterangepicker -->
<script src="<?= base_url('plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('plugins/daterangepicker/daterangepicker.js') ?>"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<!-- Summernote -->
<script src="<?= base_url('plugins/summernote/summernote-bs4.min.js') ?>"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('dist/js/adminlte.js') ?>"></script>
<!-- AdminLTE for demo purposes -->
<!-- <script src="<?= base_url('dist/js/demo.js') ?>"></script> -->
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<?php if ($this->router->fetch_class() == 'MainController'): ?>    
    <script src="<?= base_url('dist/js/pages/dashboard.js') ?>"></script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>

<script>var base_url = "<?= base_url(); ?>";</script>

<?php if (isset($current_board_id)): ?>
    <script>var currentBoardId = "<?= html_escape($current_board_id) ?>";</script>
<?php endif; ?>

<!-- Select2 -->
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>

<!-- SweetAlert2 -->
<script src="<?= base_url('plugins/sweetalert2/sweetalert2.all.min.js') ?>"></script>


<script src="<?= base_url('dist/js/notifications.js') ?>?v=<?= time(); ?>"></script>
<script src="<?= base_url('dist/js/select2.js') ?>?v=<?= time(); ?>"></script>


<script src="<?= base_url('dist/js/ajax.js') ?>?v=<?= time(); ?>"></script>

<script src="<?= base_url('plugins/popper/popper.min.js') ?>?v=<?= time(); ?>"></script>

<script src="<?= base_url('dist/js/kanban.js') ?>?v=<?= time(); ?>"></script>

<script src="<?= base_url('dist/js/mailjet.js') ?>?v=<?= time(); ?>"></script>

<?php if ($this->router->fetch_class() == 'MapaController'): ?>    
    <script src="<?= base_url('dist/js/mapa_dea.js') ?>?v=<?= time(); ?>"></script>
<?php endif; ?>

</body>
</html>