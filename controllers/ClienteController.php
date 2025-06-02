<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Clientes;
use MVC\Router;

class ClienteController extends ActiveRecord
{

    public function index(Router $router)
    {
        $router->render('clientes/index', []);
    }


    public static function guardarAPI()
    {

        getHeadersApi();

        $_POST['cli_nombre'] = htmlspecialchars($_POST['cli_nombre']);
        $cantidad_nombres = strlen($_POST['cli_nombre']);
        if ($cantidad_nombres < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el nombre debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['cli_apellidos'] = htmlspecialchars($_POST['cli_apellidos']);
        $cantidad_apellidos = strlen($_POST['cli_apellidos']);
        if ($cantidad_apellidos < 1) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el apellido debe de ser mayor a uno'
            ]);
            return;
        }


        $_POST['cli_telefono'] = filter_var($_POST['cli_telefono'], FILTER_VALIDATE_INT);
        if (strlen($_POST['cli_telefono']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos de telefono debe de ser igual a 8'
            ]);
            return;
        } else {

            try {

                $data = new Clientes([
                    'cli_nombre' => $_POST['cli_nombre'],
                    'cli_apellidos' => $_POST['cli_apellidos'],
                    'cli_telefono' => $_POST['cli_telefono'],
                    'cli_situacion' => 1
                ]);

                $crear = $data->crear();

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Exito, el cliente ha sido registrado correctamente'
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

            $sql = "SELECT * FROM clientes where cli_situacion = 1";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes obtenidos satisfactoriamente',
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

        $id = $_POST['cli_id'];
        $_POST['cli_nombre'] = htmlspecialchars($_POST['cli_nombre']);
        $cantidad_nombres = strlen($_POST['cli_nombre']);
        if ($cantidad_nombres < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el nombre debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['cli_apellidos'] = htmlspecialchars($_POST['cli_apellidos']);
        $cantidad_apellidos = strlen($_POST['cli_apellidos']);
        if ($cantidad_apellidos < 1) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el apellido debe de ser mayor a uno'
            ]);
            return;
        }


        $_POST['cli_telefono'] = filter_var($_POST['cli_telefono'], FILTER_VALIDATE_INT);
        if (strlen($_POST['cli_telefono']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos de telefono debe de ser igual a 8'
            ]);
            return;
        } else {

            try {

                $data = Clientes::find($id);
                $data->sincronizar([
                    'cli_nombre' => $_POST['cli_nombre'],
                    'cli_apellidos' => $_POST['cli_apellidos'],
                    'cli_telefono' => $_POST['cli_telefono'],
                    'cli_situacion' => 1
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

        try {

            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $ejecutar = Clientes::EliminarClientes($id);

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
        }
    }
}
