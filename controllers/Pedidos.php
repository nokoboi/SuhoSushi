<?php

require_once '../data/Pedido.php';
require_once 'utilidades.php';
require_once '../data/DetallePedido.php';

header('Content-Type: application/json');

$pedido = new Pedido();
$detallePedido = new DetallePedido();

$method = $_SERVER['REQUEST_METHOD'];

$uri = $_SERVER['REQUEST_URI'];

$parametros = Utilidades::parseUriParameters($uri);

$id = Utilidades::getParameterValue($parametros, 'id');
$metodo = Utilidades::getParameterValue($parametros, 'metodo');

switch ($method) {
    case 'GET':
        if ($id) {
            if ($metodo == 'detalles') {
                // Función para obtener los detalles y el total
                $respuesta = $detallePedido->getDetallesConTotalById($id);
                echo json_encode($respuesta);
            } else {
                $respuesta = getPedidoById($pedido, $id);
                echo json_encode($respuesta);
            }
        } else {
            $respuesta = getAllPedidos($pedido);
            echo json_encode($respuesta); 
        }
        break;

    case 'POST':
        if ($metodo == 'nuevo') {
            setPedido($pedido);
        }
        if ($metodo == 'actualizar') {
            if ($id) {
                updatePedido($pedido, $id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID no proporcionado']);
            }
        }

        if($metodo == 'terminar'){
            if($id){
                terminarPedido($pedido,$id);
            }else {
                http_response_code(400);
                echo json_encode(['error' => 'ID no proporcionado']);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}


function getAllPedidos($pedido)
{
    return $pedido->getAllPedidos();
}

function getPedidoById($pedido, $id)
{
    return $pedido->getPedidoById($id);
}

function setPedido($pedido){
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['mesa_id']) && isset($data['n_personas']) && isset($data['fecha']) && isset($data['detalles'])) {
        $pedidoId = $pedido->createPedido($data['mesa_id'], $data['n_personas'], $data['fecha'], $data['detalles']);
        echo json_encode(['id' => $pedidoId]);
    } else {
        http_response_code(400);
        echo json_encode(['Error' => 'Datos insuficientes']);
    }
}

function updatePedido($pedido, $id)
{
    $data = $_POST;

    if(isset($data['mesa_id']) && isset($data['n_personas']) && isset($data['fecha'])){
        $affected = $pedido->updatePedido($id, $data['mesa_id'],$data['n_personas'],$data['fecha'],$data['estado'],$data['total']);
        echo json_encode(['affected' => $affected]);
    } else {
        echo json_encode(['Error' => 'Datos insuficientes']);
    }
}

function terminarPedido($pedido, $id){
    $affected = $pedido->pedidoTerminado($id);
    echo json_encode(['affected' => $affected]);
}