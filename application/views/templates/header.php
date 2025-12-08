<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="google-adsense-account" content="ca-pub-2719792487651208">
  <title><?php echo isset($titulo) ? $titulo : 'Sixty'; ?></title>

  <link rel="icon" href="<?= base_url('favicon.png') ?>" type="image/x-icon">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('plugins/fontawesome-free/css/all.min.css') ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?= base_url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?= base_url('plugins/jqvmap/jqvmap.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= base_url('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= base_url('plugins/daterangepicker/daterangepicker.css') ?>">
  <!-- summernote -->
  <link rel="stylesheet" href="<?= base_url('plugins/summernote/summernote-bs4.min.css') ?>">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
  
  <link rel="stylesheet" href="<?= base_url('dist/css/kanban_styles.css?v=' . time()) ?>">
  <link rel="stylesheet" href="<?= base_url('dist/css/styles.css?v=' . time()) ?>">
  <link rel="stylesheet" href="<?= base_url('dist/css/mapa_styles.css?v=' . time()) ?>">
</head>
<!-- <body class="hold-transition sidebar-mini layout-fixed"> -->
<body class="<?php echo isset($body) ? $body : 'hold-transition sidebar-mini layout-fixed'; ?>">
<?php
  $is_login_page = isset($body) && strpos($body, 'login-page') !== false;
  if (!$is_login_page) {
      echo '<div class="wrapper">';
  }
?>

  <!-- Preloader -->
  <?php if (!$is_login_page): ?>
  <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="<?= base_url('favicon.png') ?>" alt="SixtyLogo" height="60" width="60">
  </div> -->
  <?php endif; ?>
