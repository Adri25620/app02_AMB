<div class="row justify-content-center p-3">
    <div class="col-lg-6">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid;">
            <div class="card-body">
                <div class="row mb-3">
                    <h4 class="text-center mb-2">REGISTRO DE CLIENTES</h4>
                </div>

                <div class="row justify-content-center">

                    <form id="FormClientes">
                        <input type="hidden" id="cli_id" name="cli_id">

                        <div class="row mb-3 justify-content-center">

                            <label for="cli_nombre" class="form-label">Ingrese el nombre del cliente:</label>
                            <input type="text" class="form-control" id="cli_nombre" name="cli_nombre" placeholder="Ingrese el nombre...">


                            <label for="cli_apellidos" class="form-label">Ingrese los apellidos:</label>
                            <input type="text" class="form-control" id="cli_apellidos" name="cli_apellidos" placeholder="Ingrese los apellidos...">


                            <label for="cli_telefono" class="form-label">Ingrese el telefono:</label>
                            <input type="number" class="form-control" id="cli_telefono" name="cli_telefono" placeholder="Ingrese el telefono...">


                            <div class="row justify-content-center mt-5">
                                <div class="col-auto">
                                    <button class="btn btn-success" type="submit" id="BtnGuardar">
                                        Guardar
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                        Modificar
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-dark" type="reset" id="BtnLimpiar">
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center">CLIENTES REGISTRADOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TablaClientes">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="<?= asset('build/js/clientes/index.js') ?>"></script>