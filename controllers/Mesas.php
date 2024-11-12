<?php
require_once '../data/Mesa.php';
require_once 'utilidades.php';

header('Content-Type: application/json');

$mesas = new Mesa();
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$parametros = Utilidades::parseUriParameters($uri);

$id = Utilidades::getParameterValue($parametros, 'id');
$metodo = Utilidades::getParameterValue($parametros, 'metodo');

switch ($method) {
    case 'GET':
        if ($id) {
            $respuesta = getMesaById($mesa, $id);
        } else {
            $respuesta = getAllMesas($mesas);
        }
        echo json_encode($respuesta);
        break;
    case 'POST':
        if ($metodo == 'nuevo') {
            setMesa($mesas);
        }
        if ($metodo == 'actualizar') {
            if ($id) {
                updateMesa($mesas, $id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID no proporcionado']);
            }
        }
        if ($metodo == 'eliminar') {
            if ($id) {
                deleteMesa($mesas, $id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID no proporcionado']);
            }
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);

}

function getAllMesas($mesas)
{
    return $mesas->getAllMesas();
}

function getMesaById($mesa, $id)
{
    return $mesa->getMesaById($id);
}

function setMesa($mesa)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['numero_mesa'])) {
        $id = $mesa->createMesa($data['numero_mesa']);
        echo json_encode(['id' => $id]);
    } else {
        echo json_encode(['Error' => 'Datos insuficientes']);
    }
}

function updateMesa($mesa, $id)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['numero_mesa'])) {
        $affected = $mesa->updateMesa($id, $data['numero_mesa']);
        echo json_encode(['affected' => $affected]);
    } else {
        echo json_encode(['Error' => 'Datos insuficientes']);
    }
}

function deleteMesa($mesa, $id)
{
    $affected = $mesa->deleteMesa($id);
    echo json_encode(['affected' => $affected]);
}