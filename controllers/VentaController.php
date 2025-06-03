<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Ventas;
use Model\VentaDetalle;
use Model\Clientes;
use Model\Productos;
use MVC\Router;

class VentaController extends ActiveRecord
{

    public function index(Router $router)
    {
        $clientes = Clientes::all();
        $productos = Productos::all();
        
        $router->render('ventas/index', [
            'clientes' => $clientes,
            'productos' => $productos
        ]);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        try {
            if (empty($_POST['ven_cliente'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar un cliente'
                ]);
                return;
            }

            if (empty($_POST['productos_seleccionados'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar al menos un producto'
                ]);
                return;
            }

            $productos_seleccionados = json_decode($_POST['productos_seleccionados'], true);

            if (empty($productos_seleccionados)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se han seleccionado productos válidos'
                ]);
                return;
            }

            foreach ($productos_seleccionados as $producto) {
                if (!isset($producto['id']) || !isset($producto['cantidad']) || !isset($producto['precio'])) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Datos de productos incompletos'
                    ]);
                    return;
                }

                if ($producto['cantidad'] <= 0) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'La cantidad debe ser mayor a 0'
                    ]);
                    return;
                }

                if (!VentaDetalle::verificarStock($producto['id'], $producto['cantidad'])) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Stock insuficiente para el producto seleccionado'
                    ]);
                    return;
                }
            }

            $resultado = Ventas::guardarVentaCompleta($_POST['ven_cliente'], $productos_seleccionados);

            if ($resultado['success']) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Venta registrada exitosamente',
                    'venta_id' => $resultado['venta_id']
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => $resultado['mensaje']
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarAPI()
    {
        try {
            $ventas = Ventas::obtenerVentasConCliente();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Ventas obtenidas satisfactoriamente',
                'data' => $ventas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las ventas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarDetalleAPI()
    {
        try {
            $venta_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            if (!$venta_id) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de venta inválido'
                ]);
                return;
            }

            $venta = Ventas::obtenerVentaCompleta($venta_id);
            $detalles = VentaDetalle::obtenerDetallesVenta($venta_id);

            if (empty($venta)) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Venta no encontrada'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalles de venta obtenidos exitosamente',
                'venta' => $venta[0],
                'detalles' => $detalles
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los detalles de la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        try {
            $venta_id = $_POST['ven_id'];

            if (empty($venta_id)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de venta requerido'
                ]);
                return;
            }

            if (empty($_POST['ven_cliente'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar un cliente'
                ]);
                return;
            }


            if (empty($_POST['productos_seleccionados'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar al menos un producto'
                ]);
                return;
            }

            $productos_seleccionados = json_decode($_POST['productos_seleccionados'], true);

            if (empty($productos_seleccionados)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se han seleccionado productos válidos'
                ]);
                return;
            }

            $resultado = Ventas::modificarVentaCompleta($venta_id, $_POST['ven_cliente'], $productos_seleccionados);

            if ($resultado['success']) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Venta modificada exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => $resultado['mensaje']
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function EliminarAPI()
    {
        try {
            $venta_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            if (!$venta_id) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de venta inválido'
                ]);
                return;
            }


            $resultado = Ventas::eliminarVentaCompleta($venta_id);

            if ($resultado['success']) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Venta eliminada exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => $resultado['mensaje']
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }
}