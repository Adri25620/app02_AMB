<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Productos;
use MVC\Router;

class ProductoController extends ActiveRecord
{

    public function index(Router $router)
    {
        $router->render('productos/index', []);
    }


    public static function guardarAPI()
    {

        getHeadersApi();

        $_POST['pro_nombre'] = htmlspecialchars($_POST['pro_nombre']);
        $cantidad_nombres = strlen($_POST['pro_nombre']);
        if ($cantidad_nombres < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el nombre debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['pro_precio'] = filter_var($_POST['pro_precio'], FILTER_VALIDATE_FLOAT);
        if ($_POST['pro_precio'] < 0 || $_POST['pro_precio'] > 999999.99) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ingreso una cantidad invalida'
            ]);
            return;
        }


        $_POST['pro_disponible'] = filter_var($_POST['pro_disponible'], FILTER_VALIDATE_INT);
        if (strlen($_POST['pro_disponible']) < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe ingresar la cantidad de productos'
            ]);
            return;
        } else {

            try {

                $data = new Productos([
                    'pro_nombre' => $_POST['pro_nombre'],
                    'pro_precio' => $_POST['pro_precio'],
                    'pro_disponible' => $_POST['pro_disponible'],
                    'pro_situacion' => 1
                ]);

                $crear = $data->crear();

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Exito, el producto ha sido registrado correctamente'
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar',
                    'detalle' => $e->getMessage(),
                ]);
                return;
            }
        }
    }




    public static function buscarAPI()
    {
        try {

            $sql = "SELECT * FROM productos where pro_situacion = 1";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos obtenidos satisfactoriamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los clientes',
                'detalle' => $e->getMessage(),
            ]);
        }
    }



    public static function modificarAPI()
    {

        getHeadersApi();

        $id = $_POST['pro_id'];
        $_POST['pro_nombre'] = htmlspecialchars($_POST['pro_nombre']);
        $cantidad_nombres = strlen($_POST['pro_nombre']);
        if ($cantidad_nombres < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el nombre debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['pro_precio'] = filter_var($_POST['pro_precio'], FILTER_VALIDATE_FLOAT);
        if ($_POST['pro_precio'] < 0 || $_POST['pro_precio'] > 999999.99) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ingreso una cantidad invalida'
            ]);
            return;
        }


        $_POST['pro_disponible'] = filter_var($_POST['pro_disponible'], FILTER_VALIDATE_INT);
        if (strlen($_POST['pro_disponible']) < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe ingresar la cantidad de productos'
            ]);
            return;
        } else {

            try {

                $data = Productos::find($id);
                $data->sincronizar([
                    'pro_nombre' => $_POST['pro_nombre'],
                    'pro_precio' => $_POST['pro_precio'],
                    'pro_disponible' => $_POST['pro_disponible'],
                    'pro_situacion' => 1
                ]);
                $data->actualizar();

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Exito, se modificaron los datos'
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar',
                    'detalle' => $e->getMessage(),
                ]);
                return;
            }
        }
    }


    public static function EliminarAPI()
    {
        if ($_POST['pro_disponible'] = 0) {

            try {

                $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

                $ejecutar = Productos::EliminarProductos($id);

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'El registro ha sido eliminado correctamente'
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al Eliminar',
                    'detalle' => $e->getMessage(),
                ]);
                return;
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'No se puede eliminar porque existen productos en stock'
            ]);
            return;
        }
    }
}
