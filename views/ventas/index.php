
<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid;">
            <div class="card-body">
                <div class="row mb-3">
                    <h4 class="text-center mb-2">REGISTRO DE VENTAS</h4>
                </div>

                <div class="row justify-content-center">
                    <form id="FormVentas">
                        <input type="hidden" id="ven_id" name="ven_id">

                        <div class="row mb-3 justify-content-center">
                            
                                <label for="ven_cliente" class="form-label">Seleccione el cliente:</label>
                                <select name="ven_cliente" id="ven_cliente" class="form-select" required>
                                    <option value="" selected disabled>Seleccione un cliente...</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <?php if ($cliente->cli_situacion == 1): ?>
                                            <option value="<?= $cliente->cli_id ?>"><?= $cliente->cli_nombre ?> <?= $cliente->cli_apellidos ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4" id="mensaje_seleccionar_cliente">
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Por favor, seleccione un cliente antes de elegir productos
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 d-none" id="seccion_productos">
                            <div class="col-12">
                                <h5 class="mb-3">Productos Disponibles</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Seleccionar</th>
                                                <th>Producto</th>
                                                <th>Precio</th>
                                                <th>Stock Disponible</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ProductosBody">
                                            <?php foreach ($productos as $producto): ?>
                                                <?php if ($producto->pro_situacion == 1 && $producto->pro_disponible > 0): ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <input type="checkbox" 
                                                                   class="form-check-input producto-checkbox" 
                                                                   id="check_<?= $producto->pro_id ?>"
                                                                   data-producto-id="<?= $producto->pro_id ?>"
                                                                   data-precio="<?= $producto->pro_precio ?>"
                                                                   data-stock="<?= $producto->pro_disponible ?>">
                                                        </td>
                                                        <td><?= $producto->pro_nombre ?></td>
                                                        <td>Q. <?= number_format($producto->pro_precio, 2) ?></td>
                                                        <td class="text-center"><?= $producto->pro_disponible ?></td>
                                                        <td>
                                                            <input type="number" 
                                                                   class="form-control cantidad-input" 
                                                                   id="cantidad_<?= $producto->pro_id ?>"
                                                                   min="1" 
                                                                   max="<?= $producto->pro_disponible ?>"
                                                                   value="1" 
                                                                   disabled
                                                                   data-producto-id="<?= $producto->pro_id ?>">
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="subtotal" id="subtotal_<?= $producto->pro_id ?>">Q. 0.00</span>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-4 d-none" id="seccion_resumen">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Resumen de Venta</h5>
                                        <div class="d-flex justify-content-between">
                                            <span>Productos seleccionados:</span>
                                            <span id="total_productos">0</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total a pagar:</strong>
                                            <strong id="total_venta">Q. 0.00</strong>
                                        </div>
                                        <input type="hidden" id="ven_total" name="ven_total" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Guardar Venta
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar Venta
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-dark" type="reset" id="BtnLimpiar">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                </button>
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
                <h3 class="text-center">VENTAS REGISTRADAS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TablaVentas">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/ventas/index.js') ?>"></script>