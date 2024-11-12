<?php

require_once 'Database.php';
require_once 'validator.php';

class Pedido
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllPedidos()
    {
        $result = $this->db->query("SELECT * from pedidos");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPedidoById($id)
    {
        $result = $this->db->query("SELECT * from pedidos where id=?", [$id]);
        return $result->fetch_assoc();
    }

    public function createPedido($mesaId, $personas, $fecha, $detalles)
    {
        $data = ['mesa_id' => $mesaId, 'n_personas' => $personas, 'fecha' => $fecha];
        $dataSaneado = Validator::sanear($data);

        $mesaIdSaneado = $dataSaneado['mesa_id'];
        $personaSaneada = $dataSaneado['n_personas'];
        $fechaSaneada = $dataSaneado['fecha'];

        // Paso 1: Verificar si ya existe un pedido "en curso" para la mesa
        $pedidoExistente = $this->db->query(
            "SELECT id, total, n_personas FROM pedidos WHERE mesa_id = ? AND estado = 'en curso'",
            [$mesaIdSaneado]
        )->fetch_assoc();

        if ($pedidoExistente) {
            // Si ya existe un pedido en curso, se añaden solo los detalles de los productos sin el buffet
            $pedidoId = $pedidoExistente['id'];
            $totalAnterior = $pedidoExistente['total'];
            $personasExistentes = $pedidoExistente['n_personas'];
        } else {
            // Si no existe un pedido en curso, se crea un nuevo pedido
            $this->db->query(
                "INSERT INTO pedidos (mesa_id, n_personas, fecha, total, estado) VALUES (?, ?, ?, 0, 'en curso')",
                [$mesaIdSaneado, $personaSaneada, $fechaSaneada]
            );
            $pedidoId = $this->db->query("SELECT LAST_INSERT_ID() as id")->fetch_assoc()['id'];
            if (!$pedidoId) {
                return ['Error' => 'No se pudo crear el pedido'];
            }
            $totalAnterior = 0; // Si es un nuevo pedido, el total es 0
            $personasExistentes = $personaSaneada; // Si es un nuevo pedido, se usa el número de personas proporcionado
        }

        // Paso 2: Insertar los detalles de productos y calcular el total acumulado de productos
        $totalProductos = 0;
        foreach ($detalles as $detalle) {
            $productoId = $detalle['producto_id'];
            $cantidad = $detalle['cantidad'];

            // Obtener el precio del producto
            $producto = $this->db->query("SELECT precio FROM productos WHERE id = ?", [$productoId])->fetch_assoc();

            if (!$producto) {
                return ['Error' => 'Producto no encontrado'];
            }

            $precioProducto = $producto['precio'];
            $subtotalProducto = $precioProducto * $cantidad;
            $totalProductos += $subtotalProducto;

            // Insertar el detalle de pedido
            $this->db->query(
                "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)",
                [$pedidoId, $productoId, $cantidad]
            );
        }

        // Paso 3: Calcular el total del buffet solo si es un pedido nuevo (solo una vez)
        if ($totalAnterior == 0) {
            $precioBuffet = $this->db->query("SELECT precio FROM miscelaneo WHERE concepto = 'buffet'")->fetch_assoc()['precio'];
            $totalBuffet = $precioBuffet * $personaSaneada; // El precio se calcula solo al inicio del pedido
        } else {
            $totalBuffet = 0; // No se calcula el buffet si el pedido ya existe
        }

        // Paso 4: Calcular el total final (acumulando el total de productos y buffet) y actualizar el pedido
        $totalFinal = $totalAnterior + $totalProductos + $totalBuffet;
        $this->db->query("UPDATE pedidos SET total = ? WHERE id = ?", [$totalFinal, $pedidoId]);

        return ['Éxito' => 'Pedido añadido correctamente', 'total' => $totalFinal];
    }

    public function updatePedido($id, $mesaId, $personas, $fecha, $estado, $total)
    {
        $data = ['mesa_id' => $mesaId, 'n_personas' => $personas, 'fecha' => $fecha, 'estado' => $estado, 'total' => $total];
        $dataSaneado = Validator::sanear($data);

        $mesaIdSaneado = $dataSaneado['mesa_id'];
        $personaSaneada = $dataSaneado['n_personas'];
        $fechaSaneada = $dataSaneado['fecha'];
        $estadoSaneado = $dataSaneado['estado'];

        $result = $this->db->query("SELECT id from pedidos where id=?", [$id]);
        if ($result->num_rows == 0) {
            return ['Pedido' => 'El pedido no existe'];
        }

        $this->db->query(
            "UPDATE pedidos set mesa_id=?, n_personas=?, fecha=?, estado=?, total=? where id=?",
            [$mesaId, $personaSaneada, $fechaSaneada, $estado, $total, $id]
        );

        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];
    }

    public function pedidoTerminado($id)
    {
        // Ejecutar la consulta de actualización
        $result = $this->db->query("SELECT id from pedidos where id=?", [$id]);

        // Verificar si alguna fila fue afectada
        if ($result->num_rows == 0) {
            return ['success' => false, 'error' => 'No se encontró el pedido o no se pudo actualizar'];
        }

        $this->db->query('UPDATE pedidos SET estado = "terminado" WHERE id = ?', [$id]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];
    }


}