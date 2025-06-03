import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import { data } from "jquery";

const FormVentas = document.getElementById('FormVentas');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const InputCliente = document.getElementById('ven_cliente');

let productosSeleccionados = [];


const mostrarProductos = () => {
    const mensajeCliente = document.getElementById('mensaje_seleccionar_cliente');
    const seccionProductos = document.getElementById('seccion_productos');
    const seccionResumen = document.getElementById('seccion_resumen');
    
    if (InputCliente.value) {
        mensajeCliente.classList.add('d-none');
        seccionProductos.classList.remove('d-none');
        seccionResumen.classList.remove('d-none');
    } else {
        mensajeCliente.classList.remove('d-none');
        seccionProductos.classList.add('d-none');
        seccionResumen.classList.add('d-none');
        limpiarProductos();
    }
}

const seleccionarProducto = (event) => {
    const checkbox = event.target;
    const productoId = checkbox.dataset.productoId;
    const precio = parseFloat(checkbox.dataset.precio);
    const stock = parseInt(checkbox.dataset.stock);
    const inputCantidad = document.getElementById(`cantidad_${productoId}`);
    
    if (checkbox.checked) {
        inputCantidad.disabled = false;
        
        const producto = {
            id: parseInt(productoId),
            precio: precio,
            cantidad: parseInt(inputCantidad.value),
            stock: stock
        };
        
        productosSeleccionados.push(producto);
        calcularSubtotal(productoId);
        
    } else {
        inputCantidad.disabled = true;
        inputCantidad.value = 1;
        
        productosSeleccionados = productosSeleccionados.filter(p => p.id != productoId);
        
        const subtotal = document.getElementById(`subtotal_${productoId}`);
        subtotal.textContent = 'Q. 0.00';
    }
    
    actualizarTotal();
}

const cambiarCantidad = (event) => {
    const input = event.target;
    const productoId = input.dataset.productoId;
    const cantidad = parseInt(input.value);
    const stock = parseInt(input.max);
    
    if (cantidad <= 0) {
        input.value = 1;
        return;
    }
    
    if (cantidad > stock) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Stock insuficiente",
            text: `Solo hay ${stock} unidades disponibles`,
            showConfirmButton: true,
        });
        input.value = stock;
        return;
    }
    
    const producto = productosSeleccionados.find(p => p.id == productoId);
    if (producto) {
        producto.cantidad = parseInt(input.value);
    }
    
    calcularSubtotal(productoId);
    actualizarTotal();
}

const calcularSubtotal = (productoId) => {
    const inputCantidad = document.getElementById(`cantidad_${productoId}`);
    const subtotalSpan = document.getElementById(`subtotal_${productoId}`);
    const checkbox = document.getElementById(`check_${productoId}`);
    
    if (checkbox.checked) {
        const cantidad = parseInt(inputCantidad.value);
        const precio = parseFloat(checkbox.dataset.precio);
        const subtotal = cantidad * precio;
        
        subtotalSpan.textContent = `Q. ${subtotal.toFixed(2)}`;
    }
}

const actualizarTotal = () => {
    const totalProductos = productosSeleccionados.length;
    let totalVenta = 0;
    
    productosSeleccionados.forEach(producto => {
        totalVenta += (producto.cantidad * producto.precio);
    });
    
    document.getElementById('total_productos').textContent = totalProductos;
    document.getElementById('total_venta').textContent = `Q. ${totalVenta.toFixed(2)}`;
    document.getElementById('ven_total').value = totalVenta.toFixed(2);
}

const limpiarTodo = () => {
    FormVentas.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    limpiarProductos();
    
    const mensajeCliente = document.getElementById('mensaje_seleccionar_cliente');
    const seccionProductos = document.getElementById('seccion_productos');
    const seccionResumen = document.getElementById('seccion_resumen');
    
    mensajeCliente.classList.remove('d-none');
    seccionProductos.classList.add('d-none');
    seccionResumen.classList.add('d-none');
}

const limpiarProductos = () => {
    productosSeleccionados = [];
    
    document.querySelectorAll('.producto-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.disabled = true;
        input.value = 1;
    });
    
    document.querySelectorAll('.subtotal').forEach(span => {
        span.textContent = 'Q. 0.00';
    });
    
    document.getElementById('total_productos').textContent = '0';
    document.getElementById('total_venta').textContent = 'Q. 0.00';
    document.getElementById('ven_total').value = '0';
}

const GuardarVenta = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!InputCliente.value) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe seleccionar un cliente",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (productosSeleccionados.length === 0) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "PRODUCTOS REQUERIDOS",
            text: "Debe seleccionar al menos un producto",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormVentas);
    body.append('productos_seleccionados', JSON.stringify(productosSeleccionados));

    const url = '/app02_AMB/ventas/guardarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarVentas();

        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error)
    }
    BtnGuardar.disabled = false;
}

const BuscarVentas = async () => {
    const url = '/app02_AMB/ventas/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            datatable.clear().draw();
            datatable.rows.add(data).draw();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error)
    }
}

const datatable = new DataTable('#TablaVentas', {
    dom: `
        <"row mt-3 justify-content-between" 
            <"col" l> 
            <"col" B> 
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between" 
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'ven_id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Cliente', 
            data: null,
            render: (data, type, row) => `${row.cli_nombre} ${row.cli_apellidos}`
        },
        { title: 'Teléfono', data: 'cli_telefono' },
        { title: 'Productos', data: 'total_productos' },
        { 
            title: 'Total', 
            data: 'ven_total',
            render: (data) => `Q. ${parseFloat(data).toFixed(2)}`
        },
        { title: 'Fecha', data: 'ven_fecha' },
        {
            title: 'Acciones',
            data: 'ven_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-cliente="${row.ven_cliente}">   
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                     <button class='btn btn-info ver-detalles mx-1' 
                         data-id="${data}">
                        <i class="bi bi-eye me-1"></i>Ver Detalles
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = async (event) => {
    const datos = event.currentTarget.dataset;
    const ventaId = datos.id;

    const url = `/app02_AMB/ventas/buscarDetalleAPI?id=${ventaId}`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const resultado = await respuesta.json();
        const { codigo, mensaje, venta, detalles } = resultado;

        if (codigo == 1) {
            if (!venta || !detalles) {
                throw new Error('Datos de venta incompletos');
            }

            document.getElementById('ven_id').value = ventaId;
            
            const clienteId = venta.ven_cliente || datos.cliente;
            InputCliente.value = clienteId;
            

            mostrarProductos();
            
            limpiarProductos();
            
            detalles.forEach(detalle => {
                const checkbox = document.getElementById(`check_${detalle.det_producto}`);
                const inputCantidad = document.getElementById(`cantidad_${detalle.det_producto}`);
                
                if (checkbox) {
                    checkbox.checked = true;
                    

                    inputCantidad.disabled = false;
                    inputCantidad.value = detalle.det_cantidad;
                    
                    productosSeleccionados.push({
                        id: parseInt(detalle.det_producto),
                        precio: parseFloat(detalle.det_precio_unitario),
                        cantidad: parseInt(detalle.det_cantidad),
                        stock: parseInt(inputCantidad.max)
                    });
                    
                    calcularSubtotal(detalle.det_producto);
                }
            });
            
            actualizarTotal();
            
            BtnGuardar.classList.add('d-none');
            BtnModificar.classList.remove('d-none');
            
            window.scrollTo({
                top: 0
            });

        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log('Error completo:', error);
        console.log('Respuesta recibida:', resultado);
        
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo cargar la venta",
            showConfirmButton: true,
        });
    }
}

const ModificarVenta = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!InputCliente.value) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe seleccionar un cliente",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    if (productosSeleccionados.length === 0) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "PRODUCTOS REQUERIDOS",
            text: "Debe seleccionar al menos un producto",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormVentas);
    body.append('productos_seleccionados', JSON.stringify(productosSeleccionados));

    const url = '/app02_AMB/ventas/modificarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarVentas();

        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error)
    }
    BtnModificar.disabled = false;
}

const EliminarVentas = async (e) => {
    const idVenta = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: 'Esta completamente seguro que desea eliminar este registro',
        showConfirmButton: true,
        confirmButtonText: 'Si, Eliminar',
        confirmButtonColor: 'red',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/app02_AMB/ventas/eliminarAPI?id=${idVenta}`;
        const config = {
            method: 'GET'
        }

        try {
            const consulta = await fetch(url, config);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Exito",
                    text: mensaje,
                    showConfirmButton: true,
                });
                
                BuscarVentas();
            } else {
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error",
                    text: mensaje,
                    showConfirmButton: true,
                });
            }

        } catch (error) {
            console.log(error)
        }
    }
}

const VerDetalles = async (e) => {
    const ventaId = e.currentTarget.dataset.id;

    const url = `/app02_AMB/ventas/buscarDetalleAPI?id=${ventaId}`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, venta, detalles } = datos;

        if (codigo == 1) {
            let detallesHTML = `
                <h5>Venta #${venta.ven_id}</h5>
                <p><strong>Cliente:</strong> ${venta.cli_nombre} ${venta.cli_apellidos}</p>
                <p><strong>Fecha:</strong> ${venta.ven_fecha}</p>
                <p><strong>Total:</strong> Q. ${parseFloat(venta.ven_total).toFixed(2)}</p>
                <hr>
                <h6>Productos:</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            detalles.forEach(detalle => {
                detallesHTML += `
                    <tr>
                        <td>${detalle.pro_nombre}</td>
                        <td>${detalle.det_cantidad}</td>
                        <td>Q. ${parseFloat(detalle.det_precio_unitario).toFixed(2)}</td>
                        <td>Q. ${parseFloat(detalle.det_subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });
            
            detallesHTML += `</tbody></table>`;
            
            Swal.fire({
                title: 'Detalles de Venta',
                html: detallesHTML,
                width: '600px',
                showConfirmButton: true,
                confirmButtonText: 'Cerrar'
            });

        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error)
    }
}

const configurarEventosProductos = () => {
    document.querySelectorAll('.producto-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', seleccionarProducto);
    });
    
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.addEventListener('input', cambiarCantidad);
    });
}


BuscarVentas();
configurarEventosProductos();
FormVentas.addEventListener('submit', GuardarVenta);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarVenta);
InputCliente.addEventListener('change', mostrarProductos);
datatable.on('click', '.modificar', llenarFormulario);
datatable.on('click', '.eliminar', EliminarVentas);
datatable.on('click', '.ver-detalles', VerDetalles);