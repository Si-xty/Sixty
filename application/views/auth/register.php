<div class="container">
  <div class="row justify-content-center align-items-center">
    <div class="col-3"></div>
    <div class="col-6">
      <div class="register-logo">
        <a href="../index2.html">Registro de Usuarios</a>
      </div>
      <div class="content">
        <div class="card">
          <div class="card-body register-card-body">
            <p class="register-box-msg"></p>
            <form id="registerForm" action="<?php echo base_url('register') ?>" method="post">
              <div class="container">
                <div class="row justify-content-center">
                  <div class="col-3"></div>
                  <div class="col-6">
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name= "user" placeholder="Usuario" />
                      <!-- <small><?php echo form_error('user'); ?></small> -->
                      <div class="input-group-text"><span class="bi bi-person"></span></div>
                    </div>
                  </div>
                  <div class="col-3"></div>
                </div>
                <div class="row">
                  <div class="col-6">
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" name= "first_name" placeholder="Nombre" />
                    <!-- <small><?php echo form_error('first_name'); ?></small> -->
                    <div class="input-group-text"><span class="bi bi-person"></span></div>
                  </div>
                  <div class="input-group mb-3">
                    <input type="email" class="form-control" name= "email" placeholder="Correo electrónico" />
                    <!-- <small><?php echo form_error('email'); ?></small> -->
                    <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                  </div>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" name= "rut" placeholder="Rut" />
                    <!-- <small><?php echo form_error('rut'); ?></small> -->
                    <div class="input-group-text"><span class="bi bi-person-badge"></span></div>
                  </div>
                  <div class="input-group mb-3">
                    <select class="form-control" id="roles" name="rol">
                      <option disabled selected style="color: gray;" class="">Rol ...</option>
                      <option value="1">Solicitante</option>
                      <option value="2">Trabajador</option>
                      <option value="3">Administrador</option>
                    </select>
                    <!-- <input type="text" class="form-control" name= "rol" placeholder="Rol" /> -->
                    <!-- <small><?php echo form_error('rol'); ?></small> -->
                    <div class="input-group-text"><span class="bi bi-card-text"></span></div>
                  </div>
                </div>
                <!-- END COL-6 -->

                <div class="col-6">
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" name= "last_name" placeholder="Apellido" />
                    <!-- <small><?php echo form_error('last_name'); ?></small> -->
                    <div class="input-group-text"><span class="bi bi-person-lines-fill"></span></div>
                  </div>
                  <div class="input-group mb-3">
                    <input type="password" class="form-control" name= "password" placeholder="Contraseña" />
                    <!-- <small><?php echo form_error('password'); ?></small> -->
                    <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
                  </div>
                  <div class="input-group mb-3">
                    <input type="integer" class="form-control" name= "phone_num" placeholder="Teléfono" />
                    <!-- <small><?php echo form_error('phone_num'); ?></small> -->
                    <div class="input-group-text"><span class="bi bi-telephone"></span></div>
                  </div>
                  <div class="input-group mb-3">
                    <select class="form-control" id="ubicacion" name="ubicacion">
                      <option disabled selected style="color: gray;" class="">Ubicación ...</option>
                      <option value="1">Talca</option>
                      <option value="2">Rancagua</option>
                    </select>
                    <!-- <input type="text" class="form-control" name= "rol" placeholder="Rol" /> -->
                    <!-- <small><?php echo form_error('rol'); ?></small> -->
                    <div class="input-group-text"><span class="bi bi-house"></span></div>
                  </div>
                </div>
                <!-- END COL-6 -->
                </div>
              </div>
              <!--begin::Row-->
              <div class="row">
                <div class="col-4"></div>
                <div class="col-4">
                  <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                  </div>
                </div>
                <!-- /.col -->
                  <div class="col-4"></div>
              </div>
              <!--end::Row-->
            </form>
            <div class=""></div>
          <!-- /.register-card-body -->
          </div>
        </div>
      </div>
        <!-- /.register-box -->
    </div>
    <div class="col-3"></div>
  </div>
</div>