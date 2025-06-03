<?php

namespace Model;



class Ventas extends ActiveRecord
{

    public static $tabla = 'ventas';
    public static $columnasDB = [
        'ven_cliente',
        'ven_total',
        'ven_fecha',
        'ven_situacion'
    ];
    public static $idTabla = 'ven_id';

    public $ven_id;
    public $ven_cliente;
    public $ven_total;
    public $ven_fecha;
    public $ven_situacion;

    public function __construct($args = []){
        $this->ven_id = $args['ven_id'] ?? null;
        $this->ven_cliente = $args['ven_cliente'] ?? '';
        $this->ven_total = $args['ven_total'] ?? 0.00;
        $this->ven_fecha = $args['ven_fecha'] ?? date('Y-m-d H:i');
        $this->ven_situacion = $args['ven_situacion'] ?? 1;   
    }


    public static function EliminarVenta($id){
        $sql = "UPDATE ventas SET ven_situacion = 0 WHERE ven_id = $id";
        return self::SQL($sql);
    }


    public static function obtenerVentasConCliente(){
        $sql = "SELECT v.ven_id, v.ven_total, v.ven_fecha, 
                       c.cli_nombre, c.cli_apellidos, c.cli_telefono,
                       (SELECT COUNT(*) FROM venta_detalles vd WHERE vd.det_venta = v.ven_id AND vd.det_situacion = 1) as total_productos
                FROM ventas v 
                INNER JOIN clientes c ON v.ven_cliente = c.cli_id 
                WHERE v.ven_situacion = 1 
                ORDER BY v.ven_fecha DESC";
        return self::fetchArray($sql);
    }


    public static function obtenerVentaCompleta($venta_id){
        $sql = "SELECT v.*, c.cli_nombre, c.cli_apellidos, c.cli_telefono
                FROM ventas v 
                INNER JOIN clientes c ON v.ven_cliente = c.cli_id 
                WHERE v.ven_id = $venta_id AND v.ven_situacion = 1";
        return self::fetchArray($sql);
    }


    public static function guardarVentaCompleta($cliente_id, $productos_seleccionados) {
        try {
            if (empty($productos_seleccionados)) {
                throw new \Exception("No se han seleccionado productos");
            }


            foreach($productos_seleccionados as $producto) {
                if (!VentaDetalle::verificarStock($producto['id'], $producto['cantidad'])) {
                    throw new \Exception("Stock insuficiente para el producto ID: " . $producto['id']);
                }
            }


            $total_venta = 0;
            foreach($productos_seleccionados as $producto) {
                $total_venta += ($producto['cantidad'] * $producto['precio']);
            }


            $venta = new self([
                'ven_cliente' => $cliente_id,
                'ven_total' => $total_venta,
                'ven_fecha' => date('Y-m-d H:i'),
                'ven_situacion' => 1
            ]);

            $resultado = $venta->crear();
            $venta_id = $resultado['id'];


            foreach($productos_seleccionados as $producto) {
                $subtotal = $producto['cantidad'] * $producto['precio'];
                
                $detalle = new VentaDetalle([
                    'det_venta' => $venta_id,
                    'det_producto' => $producto['id'],
                    'det_cantidad' => $producto['cantidad'],
                    'det_precio_unitario' => $producto['precio'],
                    'det_subtotal' => $subtotal,
                    'det_situacion' => 1
                ]);

                $detalle->crear();


                VentaDetalle::actualizarStock($producto['id'], $producto['cantidad']);
            }

            return [
                'success' => true,
                'venta_id' => $venta_id,
                'mensaje' => 'Venta guardada exitosamente'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }


    public static function modificarVentaCompleta($venta_id, $cliente_id, $productos_seleccionados) {
        try {
 
            $detalles_actuales = VentaDetalle::obtenerDetallesVenta($venta_id);
            
            foreach($detalles_actuales as $detalle) {
                VentaDetalle::restaurarStock($detalle['det_producto'], $detalle['det_cantidad']);
            }


            VentaDetalle::EliminarDetallesVenta($venta_id);


            foreach($productos_seleccionados as $producto) {
                if (!VentaDetalle::verificarStock($producto['id'], $producto['cantidad'])) {
                    throw new \Exception("Stock insuficiente para el producto ID: " . $producto['id']);
                }
            }


            $total_venta = 0;
            foreach($productos_seleccionados as $producto) {
                $total_venta += ($producto['cantidad'] * $producto['precio']);
            }


            $venta = self::find($venta_id);
            $venta->sincronizar([
                'ven_cliente' => $cliente_id,
                'ven_total' => $total_venta,
                'ven_situacion' => 1
            ]);
            $venta->actualizar();


            foreach($productos_seleccionados as $producto) {
                $subtotal = $producto['cantidad'] * $producto['precio'];
                
                $detalle = new VentaDetalle([
                    'det_venta' => $venta_id,
                    'det_producto' => $producto['id'],
                    'det_cantidad' => $producto['cantidad'],
                    'det_precio_unitario' => $producto['precio'],
                    'det_subtotal' => $subtotal,
                    'det_situacion' => 1
                ]);

                $detalle->crear();


                VentaDetalle::actualizarStock($producto['id'], $producto['cantidad']);
            }

            return [
                'success' => true,
                'mensaje' => 'Venta modificada exitosamente'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }


    public static function eliminarVentaCompleta($venta_id) {
        try {

            $detalles = VentaDetalle::obtenerDetallesVenta($venta_id);
            

            foreach($detalles as $detalle) {
                VentaDetalle::restaurarStock($detalle['det_producto'], $detalle['det_cantidad']);
            }


            VentaDetalle::EliminarDetallesVenta($venta_id);

 
            self::EliminarVenta($venta_id);

            return [
                'success' => true,
                'mensaje' => 'Venta eliminada exitosamente'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }
}