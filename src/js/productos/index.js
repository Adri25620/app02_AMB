import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import { data } from "jquery";

const FormProductos = document.getElementById('FormProductos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const InputPrecio = document.getElementById('pro_precio');




const ValidarPrecio = () => {
    const precio = parseFloat(InputPrecio.value);

    if (InputPrecio.value.length < 1) {
        InputPrecio.classList.remove('is-valid', 'is-invalid');
    } else {
        if (isNaN(precio) || precio < 0 || precio > 999999.99) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Revise el precio del producto",
                text: "Debe ingresar un precio válido entre 0 y 999,999.99",
                showConfirmButton: true,
            });

            InputPrecio.classList.remove('is-valid');
            InputPrecio.classList.add('is-invalid');
        } else {
            InputPrecio.classList.remove('is-invalid');
            InputPrecio.classList.add('is-valid');
        }
    }
}



const GuardarProducto = async (event) => {

    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormProductos, ['pro_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
    }

    const body = new FormData(FormProductos);

    const url = '/app02_AMB/productos/guardarAPI';
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
            BuscarProductos();

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

const BuscarProductos = async () => {

    const url = '/app02_AMB/productos/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {

            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

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


const datatable = new DataTable('#TablaProductos', {
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
            data: 'pro_id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Nombre', data: 'pro_nombre' },
        { title: 'Precio', data: 'pro_precio' },
        { title: 'Stock', data: 'pro_disponible' },
        {
            title: 'Acciones',
            data: 'pro_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.pro_nombre}"  
                         data-precio="${row.pro_precio}"  
                         data-disponible="${row.pro_disponible}">   
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});


const llenarFormulario = (event) => {

    const datos = event.currentTarget.dataset

    document.getElementById('pro_id').value = datos.id
    document.getElementById('pro_nombre').value = datos.nombre
    document.getElementById('pro_precio').value = datos.precio
    document.getElementById('pro_disponible').value = datos.disponible

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
    top: 0
});
}


const limpiarTodo = () => {

    FormProductos.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}



const ModificarProducto = async (event) => {

    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormProductos, [''])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
    }

    const body = new FormData(FormProductos);

    const url = '/app02_AMB/productos/modificarAPI';
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
            BuscarProductos();

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

const EliminarProducto = async (e) => {

    const idProducto = e.currentTarget.dataset.id

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

        const url = `/app02_AMB/productos/eliminar?id=${idProducto}`;
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
                
                BuscarProductos();
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

BuscarProductos();
datatable.on('click', '.eliminar', EliminarProducto);
datatable.on('click', '.modificar', llenarFormulario);
FormProductos.addEventListener('submit', GuardarProducto);
InputPrecio.addEventListener('change', ValidarPrecio);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarProducto);