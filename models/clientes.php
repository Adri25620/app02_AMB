<?php

namespace Model;

class Clientes extends ActiveRecord
{

    public static $tabla = 'clientes';
    public static $columnasDB = [

        'cli_nombre',
        'cli_apellidos',
        'cli_telefono',
        'cli_situacion'
    ];
    public static $idTabla = 'cli_id';

    public $cli_id;
    public $cli_nombre;
    public $cli_apellidos;
    public $cli_telefono;
    public $cli_situacion;


    public function __construct($args = []){
        $this->cli_id = $args['cli_id'] ?? null;
        $this->cli_nombre = $args['cli_nombre'] ?? '';
        $this->cli_apellidos = $args['cli_apellidos'] ?? '';
        $this->cli_telefono = $args['cli_telefono'] ?? '';
        $this->cli_situacion = $args['cli_situacion'] ?? 1;   
        
    }

    public static function EliminarProductos($id){
        $sql = "UPDATE clientes SET cli_situacion = 0 WHERE cli_id = $id";
        return self::SQL($sql);
    }

}
