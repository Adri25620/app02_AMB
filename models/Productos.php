<?php

namespace Model;

class Productos extends ActiveRecord
{

    public static $tabla = 'productos';
    public static $columnasDB = [

        'pro_nombre',
        'pro_precio',
        'pro_disponible',
        'pro_situacion'
    ];
    public static $idTabla = 'pro_id';

    public $pro_id;
    public $pro_nombre;
    public $pro_precio;
    public $pro_disponible;
    public $pro_situacion;


    public function __construct($args = []){
        $this->pro_id = $args['pro_id'] ?? null;
        $this->pro_nombre = $args['pro_nombre'] ?? '';
        $this->pro_precio = $args['pro_precio'] ?? '';
        $this->pro_disponible = $args['pro_disponible'] ?? '';
        $this->pro_situacion = $args['pro_situacion'] ?? 1;   
        
    }

    public static function EliminarProductos($id){
        $sql = "UPDATE productos SET pro_situacion = 0 WHERE pro_id = $id";
        return self::SQL($sql);
    }

}