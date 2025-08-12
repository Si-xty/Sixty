<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <!-- <h1>Actualización de perfil</h1> -->
            </div>
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="welcome">Home</a></li>
                <li class="breadcrumb-item active">Perfil</li>
            </ol>
            </div>
        </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid ">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8">
                    <div class="card card-secondary">
                        <div class="card-header">
                        <h3 class="card-title">Actualizar datos</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="<?= base_url('update_profile')?>" method="post">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName">Nombre</label>
                                        <input type="text" class="form-control" id="firstName" placeholder="Nombre">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName">Apellido</label>
                                        <input type="text" class="form-control" id="lastName" placeholder="Apellido">
                                    </div>
                                    <!-- <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Usuario</label>
                                        <input type="text" class="form-control" id="username" placeholder="Usuario">
                                    </div> -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email">Correo electrónico</label>
                                        <input type="email" class="form-control" id="email" placeholder="Correo electrónico">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Número telefónico</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                                    <option selected="selected" data-select2-id="3">+56</option>
                                                    <option data-select2-id="35">+56</option>
                                                    <option data-select2-id="36">+52</option>
                                                    <option data-select2-id="37">+147</option>
                                                    <option data-select2-id="38">+pe</option>
                                                </select>
                                            </div>
                                            <input type="number" class="form-control" id="phone" placeholder="Número telefónico">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="locate" class="form-label">Ubicación</label>
                                        <input type="text" class="form-control" id="locate" placeholder="Ubicación">
                                    </div>
                                    <!-- <div class="form-group">
                                    <label for="exampleInputFile">File input</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                        </div>
                                        <div class="input-group-append">
                                        <span class="input-group-text">Upload</span>
                                        </div>
                                    </div>
                                    </div> -->
                                </div>
                                <div class="row">
                                        <div class="col-md-6 mb-3 mt-3">
                                            <button type="button" class="btn btn-block btn-primary">Cambiar usuario</button>
                                        </div>
                                        <div class="col-md-6 mb-3 mt-3">
                                            <button type="button" class="btn btn-block btn-danger">Cambiar contraseña</button>
                                        </div>
                                </div>
                            <!-- /.card-body -->
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <button type="submit" class="btn btn-secondary">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>