<?php
require_once 'config.php';

class Database{
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if($this->conexion->connect_error) {
            die('Error de conexión: '. $this->conexion->connect_error);
        }
    }

    public function query($sql, $params = []) {
        $statement = $this->conexion->prepare($sql);
        if($statement === false) {
            die('Error en la preparacion: '. $this->conexion->error);
        }

        if(!empty($params)) {
            $types = str_repeat('s', count($params));
            $statement->bind_param( $types, ...$params);
        }

        $statement->execute();
        return $statement->get_result();
    }

    public function close() {
        $this->conexion->close();
    }
}
