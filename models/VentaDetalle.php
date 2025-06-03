<?php

namespace Model;

class VentaDetalle extends ActiveRecord
{

    public static $tabla = 'venta_detalles';
    public static $columnasDB = [
        'det_venta',
        'det_producto',
        'det_cantidad',
        'det_precio_unitario',
        'det_subtotal',
        'det_situacion'
    ];
    public static $idTabla = 'det_id';

    public $det_id;
    public $det_venta;
    public $det_producto;
    public $det_cantidad;
    public $det_precio_unitario;
    public $det_subtotal;
    public $det_situacion;

    public function __construct($args = []){
        $this->det_id = $args['det_id'] ?? null;
        $this->det_venta = $args['det_venta'] ?? '';
        $this->det_producto = $args['det_producto'] ?? '';
        $this->det_cantidad = $args['det_cantidad'] ?? 1;
        $this->det_precio_unitario = $args['det_precio_unitario'] ?? 0.00;
        $this->det_subtotal = $args['det_subtotal'] ?? 0.00;
        $this->det_situacion = $args['det_situacion'] ?? 1;   
    }

  
    public static function obtenerDetallesVenta($venta_id){
        $sql = "SELECT d.*, p.pro_nombre
                FROM venta_detalles d 
                INNER JOIN productos p ON d.det_producto = p.pro_id 
                WHERE d.det_venta = $venta_id AND d.det_situacion = 1 
                ORDER BY p.pro_nombre";
        return self::fetchArray($sql);
    }

  
    public static function EliminarDetallesVenta($venta_id){
        $sql = "UPDATE venta_detalles SET det_situacion = 0 WHERE det_venta = $venta_id";
        return self::SQL($sql);
    }

 
    public static function productoEnVenta($venta_id, $producto_id){
        $sql = "SELECT * FROM venta_detalles 
                WHERE det_venta = $venta_id AND det_producto = $producto_id AND det_situacion = 1";
        $resultado = self::fetchArray($sql);
        return !empty($resultado);
    }


    public static function actualizarCantidadDetalle($venta_id, $producto_id, $nueva_cantidad, $nuevo_subtotal){
        $sql = "UPDATE venta_detalles 
                SET det_cantidad = $nueva_cantidad, det_subtotal = $nuevo_subtotal 
                WHERE det_venta = $venta_id AND det_producto = $producto_id";
        return self::SQL($sql);
    }

 
    public static function verificarStock($producto_id, $cantidad_requerida){
        $sql = "SELECT pro_disponible FROM productos WHERE pro_id = $producto_id";
        $resultado = self::fetchArray($sql);
        
        if(!empty($resultado)){
            return $resultado[0]['pro_disponible'] >= $cantidad_requerida;
        }
        return false;
    }


    public static function actualizarStock($producto_id, $cantidad_vendida){
        $sql = "UPDATE productos SET pro_disponible = pro_disponible - $cantidad_vendida WHERE pro_id = $producto_id";
        return self::SQL($sql);
    }


    public static function restaurarStock($producto_id, $cantidad_a_restaurar){
        $sql = "UPDATE productos SET pro_disponible = pro_disponible + $cantidad_a_restaurar WHERE pro_id = $producto_id";
        return self::SQL($sql);
    }
}