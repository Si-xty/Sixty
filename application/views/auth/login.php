<?php /* Content-only view: header/footer/templates provide head/body */ ?>
<div class="login-box">
  <div class="login-logo">
    <a href="../../welcome"><b>Sixty</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"></p>

      <form id="loginForm" data-login-url="<?= base_url("login")?>" action="<?php echo base_url('login') ?>" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="email" placeholder="Correo electrónico">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Contraseña">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-7">
            <div class="icheck-primary">
              <input class="form-check-input" type="checkbox" id="remember">
              <label class="form-check-label" for="remember">
                Recuérdame
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-5">
            <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <div class="social-auth-links text-center mb-3">
        <p>o</p>
        <a href="<?= base_url('googleauth')?>" class="btn btn-block google-btn">
          <img src="/dist/img/google.svg" alt="Google Logo" class="google-logo">
          Continuar con Google
        </a>

      </div>
      <!-- /.social-auth-links -->

      <p class="mb-1">
        <a href="forgot-password.html">Olvidé mi contraseña</a>
      </p>
      <!-- <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p> -->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>

<style>
  .google-btn {
    background-color: #ffffff;
    color: #000000;
    border: 1px solid #ccc;
    padding: 6px 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    text-decoration: none;
    border-radius: 20px;
    transition: background-color 0.3s ease;
}

.google-btn:hover {
    background-color: #f1f1f1;
}

.google-logo {
    width: 20px;
    height: 20px;
    margin-right: 10px;
}
</style>

<?php /* Scripts are loaded via global footer */ ?>
