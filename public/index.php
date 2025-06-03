<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\ClienteController;
use Controllers\ProductoController;
use Controllers\VentaController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

//url's clientes
$router->get('/clientes', [ClienteController::class, 'index']);
$router->post('/clientes/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->get('/clientes/buscarAPI', [ClienteController::class, 'buscarAPI']);
$router->post('/clientes/modificarAPI', [ClienteController::class, 'modificarAPI']);
$router->get('/clientes/eliminar', [ClienteController::class, 'eliminarAPI']);

//url's productos
$router->get('/productos', [ProductoController::class, 'index']);
$router->post('/productos/guardarAPI', [ProductoController::class, 'guardarAPI']);
$router->get('/productos/buscarAPI', [ProductoController::class, 'buscarAPI']);
$router->post('/productos/modificarAPI', [ProductoController::class, 'modificarAPI']);
$router->get('/productos/eliminar', [ProductoController::class, 'eliminarAPI']);

//url's ventas
$router->get('/ventas', [VentaController::class, 'index']);
$router->post('/ventas/guardarAPI', [VentaController::class, 'guardarAPI']);
$router->get('/ventas/buscarAPI', [VentaController::class, 'buscarAPI']);
$router->get('/ventas/buscarDetalleAPI', [VentaController::class, 'buscarDetalleAPI']);
$router->post('/ventas/modificarAPI', [VentaController::class, 'modificarAPI']);
$router->get('/ventas/eliminar', [VentaController::class, 'eliminarAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
