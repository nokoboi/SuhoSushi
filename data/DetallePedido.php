<?php

require_once 'Database.php';
require_once 'validator.php';

class DetallePedido
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllDetalles()
    {
        $result = $this->db->query("SELECT * from detalle_pedidos");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDetalleById($id)
    {
        $result = $this->db->query("SELECT * from detalle_pedidos where id=?", [$id]);
        return $result->fetch_assoc();
    }

    public function createDetallePedido($pedidoId, $productoId, $cantidad)
    {
        // Validar si el pedido existe
        $pedido = $this->db->query("SELECT n_personas FROM pedidos WHERE id = ?", [$pedidoId])->fetch_assoc();

        if (!$pedido) {
            return ['Error' => 'Pedido no encontrado'];
        }

        // Obtener el precio del producto
        $producto = $this->db->query("SELECT precio FROM productos WHERE id = ?", [$productoId])->fetch_assoc();

        if (!$producto) {
            return ['Error' => 'Producto no encontrado'];
        }

        // Insertar el nuevo detalle de pedido
        $insert = $this->db->query(
            "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)",
            [$pedidoId, $productoId, $cantidad]
        );

        if ($insert) {
            return ['Éxito' => 'Detalle de pedido creado correctamente'];
        } else {
            return ['Error' => 'Error al crear el detalle de pedido'];
        }
    }

    public function getDetallesConTotalById($id)
{
    // Obtener los detalles del pedido
    $query = "SELECT p.nombre AS producto_nombre, dp.cantidad, p.precio
              FROM detalle_pedidos dp
              JOIN productos p ON dp.producto_id = p.id
              WHERE dp.pedido_id = ?";
    $detalles = $this->db->query($query, [$id])->fetch_all(MYSQLI_ASSOC);

    // Obtener el total calculado previamente del pedido desde la tabla 'pedidos'
    $totalQuery = "SELECT total FROM pedidos WHERE id = ?";
    $total = $this->db->query($totalQuery, [$id])->fetch_assoc();

    // Obtener el precio del buffet desde la tabla 'miscelaneo'
    $buffetQuery = "SELECT precio FROM miscelaneo WHERE id = 1";  // Asumiendo que el precio del buffet está en el primer registro de miscelaneo
    $buffet = $this->db->query($buffetQuery)->fetch_assoc();

    if ($detalles && $total && $buffet) {
        // Agregar el precio del buffet al arreglo de detalles
        $detalles[] = [
            'producto_nombre' => 'Buffet',
            'cantidad' => 1,  // El buffet se cuenta como 1 por cada persona, puedes ajustar según el diseño
            'precio' => $buffet['precio']
        ];

        return [
            'success' => true,
            'detalles' => $detalles,
            'total' => $total['total']  // Devuelve el total almacenado
        ];
    } else {
        return [
            'success' => false,
            'error' => 'Pedido o detalles no encontrados'
        ];
    }
}



}