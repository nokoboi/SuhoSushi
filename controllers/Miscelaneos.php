<?php
require_once '../data/Misc.php';
require_once 'utilidades.php';

header('Content-Type: application/json');

$misc = new Misc();
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$parametros = Utilidades::parseUriParameters($uri);

$id = Utilidades::getParameterValue($parametros, 'id');
$metodo = Utilidades::getParameterValue($parametros, 'metodo');

switch ($method) {
    case 'GET':
        if ($id) {
            $respuesta = getMiscById($misc, $id);
        } else {
            $respuesta = getAllMisc($misc);
        }
        echo json_encode($respuesta);
        break;
    case 'POST':
        if ($metodo == 'nuevo') {
            setMisc($misc);
        }
        if ($metodo == 'actualizar') {
            if ($id) {
                updateMisc($misc, $id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID no proporcionado']);
            }
        }
        if ($metodo == 'eliminar') {
            if ($id) {
                deleteMisc($misc, $id);
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

function getAllMisc($misc)
{
    return $misc->getAllMisc();
}

function getMiscById($misc, $id)
{
    return $misc->getMiscById($id);
}

function setMisc($misc)
{
    $data = $_POST;

    if (isset($data['concepto']) && isset($data['precio'])) {
        $id = $misc->createMisc($data['concepto'], $data['precio']);
        echo json_encode(['id' => $id]);
    } else {
        echo json_encode(['Error' => 'Datos insuficientes']);
    }
}

function updateMisc($misc, $id)
{
    $data = $_POST;

    if (isset($data['concepto']) && isset($data['precio'])) {
        $affected = $misc->updateMisc($id, $data['concepto'], $data['precio']);
        echo json_encode(['affected' => $affected]);
    } else {
        echo json_encode(['Error' => 'Datos insuficientes']);
    }
}

function deleteMisc($misc, $id)
{
    $affected = $misc->deleteMisc($id);
    echo json_encode(['affected' => $affected]);
}