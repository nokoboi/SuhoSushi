<?php
require_once '../data/usuario.php';
require_once 'utilidades.php';

session_start();

header('Content-Type: application/json');

$usuario = new Usuario();

/**
 * La variable superglobal $_SERVER['REQUEST_METHOD'] contiene información sobre el método de solicitud HTTP realizado
 * REQUEST_METHOD :
 * -   GET     Para solicitar datos del servidor
 * -   POST    Para enviar datos al  servidor
 * -   PUT     Para actualizar datos existentes
 * -   DELETE  Para eliminar
 */
//obtener el método de la petición (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Obtener la URI de la petición
$uri = $_SERVER['REQUEST_URI'];

//obtener los parámetros de la petición
$parametros = Utilidades::parseUriParameters($uri);

//obtener el parámetro id
$id = Utilidades::getParameterValue($parametros, 'id');

switch ($method) {
    case 'GET':
        $respuesta = getAllUsuarios($usuario);

        echo json_encode($respuesta);
        break;
    case 'POST':
        if (isset($_POST['recuperar'])) {
            // Recibe el email enviado desde el formulario
            $email = $_POST['email'];
        
            // Llama al método de recuperación de contraseña y almacena el resultado
            $resultado = $usuario->recuperarPassword($email);
        
            // Redirige al usuario a la página principal con el resultado de la recuperación
            return redirigirConMensaje('../index.php', $resultado['success'], $resultado['message']);
        }

        if (isset($_POST['username'])) {
            // Recibe el email y la contraseña enviados desde el formulario
            $nombreUsuario = $_POST['username'];
            $password = $_POST['password'];
        
            // Llama al método de inicio de sesión y almacena el resultado
            $resultado = $usuario->inicioSesion($nombreUsuario, $password);
        
            // Si el inicio de sesión es exitoso, almacena el ID del usuario en la sesión
            if ($resultado['success'] == 'success') {
                $_SESSION['user'] = $resultado['id'];
            }
        
            // Redirige al usuario a la página principal con el resultado del inicio de sesión
            return redirigirConMensaje('../administracion/adminProductos.php', $resultado['success'], $resultado['message']);
        }
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}

function getAllUsuarios($usuario){
    return $usuario->getAll();
}

function redirigirConMensaje($url, $success, $mensaje)
{
    // Almacena el resultado en la sesión para mostrarlo en la redirección
    $_SESSION['success'] = $success;
    $_SESSION['message'] = $mensaje;

    // Redirige a la URL especificada
    header("Location: $url");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['recuperar'])) {
    // Recibe el email enviado desde el formulario
    $email = $_POST['email'];

    // Llama al método de recuperación de contraseña y almacena el resultado
    $resultado = $usuario->recuperarPassword($email);

    // Redirige al usuario a la página principal con el resultado de la recuperación
    return redirigirConMensaje('loginAdmin.php', $resultado['success'], $resultado['message']);
}
