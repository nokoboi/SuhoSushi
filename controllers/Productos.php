<?php

require_once '../data/Producto.php';
require_once 'utilidades.php';

header('Content-Type: application/json');

$products = new Producto();

$method = $_SERVER['REQUEST_METHOD'];

$uri = $_SERVER['REQUEST_URI'];

$parametros = Utilidades::parseUriParameters($uri);

$id = Utilidades::getParameterValue($parametros, 'id');
$metodo = Utilidades::getParameterValue($parametros, 'metodo');


switch ($method) {
    case 'GET':
        if ($id) {
            $respuesta = getProductoById($products, $id);
        } else {
            $respuesta = getAllProducts($products);
        }
        echo json_encode($respuesta);
        break;
    case 'POST':
        if ($metodo == 'nuevo') {
            setProducto($products);
        }
        if ($metodo == 'actualizar') {
            if ($id) {
                updateProducto($products, $id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID no proporcionado']);
            }
        }
        if ($metodo == 'eliminar') {
            if ($id) {
                deleteProducto($products, $id);
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


function getAllProducts($products)
{
    return $products->getAllProducts();
}

function getProductoById($products, $id)
{
    return $products->getProductById($id);
}

function setProducto($producto)
{
    $data = $_POST; // Capturamos los datos del formulario
    $file = $_FILES['imagen']; // Capturamos el archivo subido

    // Verificar si el archivo se subió correctamente
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['Error' => 'Error al subir el archivo.']);
        return;
    }

    // Ruta donde deseas guardar la imagen
    $uploadDir = '../assets/'; // Asegúrate de que esta carpeta exista y tenga permisos de escritura
    $fileName = $uploadDir . basename($file['name']);
    $uploadFilePath = $uploadDir . $fileName;

    if (isset($data['nombre']) && isset($data['tipo']) && isset($fileName)) {
        if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            // Usar fusión nula para establecer el precio como null si no está presente
            $descripcion = $data['descripcion'] ?? null;
            $precio = $data['precio'] ?? null;

            // Crear el producto pasando los valores necesarios
            $id = $producto->createProduct($data['nombre'], $descripcion, $data['tipo'], $precio, $fileName);

            echo json_encode(['id' => $id]);
        }

    } else {
        echo json_encode(['Error' => 'Datos insuficientes']);
    }
}

function updateProducto($producto, $id) {
    if (isset($_POST['nombre']) && isset($_POST['tipo'])) {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'] ?? null;
        $tipo = $_POST['tipo'];
        $precio = $_POST['precio'] ?? null;

        // Manejo de la imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagen = $_FILES['imagen'];
            $nombreImagen = basename($imagen['name']);
            $rutaDestino = "../assets/$nombreImagen";

            if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
                $rutaImagen = $rutaDestino; // Usar la nueva imagen
            } else {
                echo json_encode(['Error' => 'Error al mover la imagen a la carpeta de destino.']);
                return;
            }
        } else {
            // Mantener la ruta existente si no se sube nueva imagen
            $rutaImagen = $_POST['imagen'];
        }

        // Actualizar el producto en la base de datos
        $affected = $producto->updateProducto($id, $nombre, $descripcion, $tipo, $precio, $rutaImagen);
        echo json_encode(['affected' => $affected]);
    } else {
        echo json_encode(['Error' => 'Datos insuficientes']);
    }
}

function deleteProducto($producto, $id) {
    // Primero obtén la ruta de la imagen
    $rutaImagen = $producto->getImagebyId($id);

    // Verifica que la ruta de la imagen sea válida
    if (!$rutaImagen) {
        echo json_encode(['Error' => 'La ruta de la imagen es incorrecta.']);
        exit; // Salir si la ruta es null
    }

    // Intenta eliminar el producto
    $affected = $producto->deleteProducto($id);

    // Solo intenta borrar la imagen si el producto fue eliminado correctamente
    if ($affected) {
        // Verifica si la imagen existe y luego intenta eliminarla
        if ($rutaImagen && file_exists($rutaImagen)) {
            if (unlink($rutaImagen)) {
                echo json_encode(['affected' => $affected, 'message' => 'Producto y imagen eliminados.']);
            } else {
                echo json_encode(['Error' => 'No se pudo borrar la imagen.']);
            }
        } else {
            echo json_encode(['Error' => 'La imagen no existe o la ruta es incorrecta.']);
        }
    } else {
        echo json_encode(['Error' => 'No se pudo eliminar el producto']);
    }
}

// Consulta que muestre la factura o ticket
// modificar y ver los pedidos
// un filtro de fechas de los pedidos y consulta para mostrar el total del pedido

