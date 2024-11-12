<?php

require_once 'Database.php';
require_once 'validator.php';

class Producto
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllProducts()
    {
        $result = $this->db->query("SELECT * from productos");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductById($id)
    {
        $result = $this->db->query("SELECT * from productos where id=?", [$id]);
        return $result->fetch_assoc();
    }

    public function getImagebyId($id)
    {
        $result = $this->db->query("SELECT imagen FROM productos WHERE id=?", [$id]);
        $row = $result->fetch_assoc();

        // Devuelve la ruta de la imagen, o null si no se encuentra
        return $row['imagen'] ?? null;
    }

    public function createProduct($nombre, $descripcion = null, $tipo, $precio = null, $imagen)
    {
        $data = ['nombre' => $nombre, 'descripcion' => $descripcion, 'tipo' => $tipo, 'precio' => $precio, 'imagen' => $imagen];
        $dataSaneado = Validator::sanear($data);

        $nombreSaneado = $dataSaneado['nombre'];
        $descripcionSaneada = $dataSaneado['descripcion'];
        $tipoSaneado = $data['tipo'];
        $precioSaneado = $data['precio'];

        $this->db->query(
            "INSERT INTO productos(nombre,descripcion,tipo,precio,imagen) values(?,?,?,?,?)",
            [$nombreSaneado, $descripcion, $tipoSaneado, $precioSaneado, $imagen]
        );

        return $this->db->query("SELECT LAST_INSERT_ID() as id")->fetch_assoc()['id'];
    }

    function updateProducto($id, $nombre, $descripcion, $tipo, $precio, $imagen)
    {
        $data = ['nombre' => $nombre, 'descripcion' => $descripcion, 'tipo' => $tipo, 'precio' => $precio, 'imagen' => $imagen];
        $dataSaneado = Validator::sanear($data);

        $nombreSaneado = $dataSaneado['nombre'];
        $descripcionSaneada = $dataSaneado['descripcion'];
        $tipoSaneado = $data['tipo'];
        $precioSaneado = $data['precio'];

        // Verificamos que el producto existe o no
        $result = $this->db->query("SELECT id from productos where id=?", [$id]);
        if ($result->num_rows == 0) {
            return ["producto" => "El producto no existe"];
        }

        $this->db->query(
            "UPDATE productos set nombre=?, descripcion=?, tipo=?, precio=?, imagen=? where id = ?"
            ,
            [$nombreSaneado, $descripcionSaneada, $tipoSaneado, $precioSaneado, $imagen, $id]
        );

        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];

    }

    function deleteProducto($id)
    {
        $this->db->query("DELETE FROM productos where id=?", [$id]);
        return $this->db->query('SELECT ROW_COUNT() as affected')->fetch_assoc()['affected'];
    }
}

