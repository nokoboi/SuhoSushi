<?php
// Inicia sesión sin ningún dato
session_start();
header("Content-type: application/json");

require_once '../data/Usuario.php';

// Crear instancia de Usuario para obtener el usuario y contraseña desde la base de datos
$usuarioObj = new Usuario();
$usuarios = $usuarioObj->getAll();

// Verificar que haya al menos un usuario en la base de datos
if (count($usuarios) > 0) {
    $usuarioValido = $usuarios[0]['nombre_usuario'];
    $contrasenaValida = $usuarios[0]['contrasena'];
} else {
    echo json_encode(['success' => false, 'message' => 'No hay usuarios registrados']);
    exit();
}

// Recibir el nombre de usuario y la contraseña del formulario
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Verificar si el usuario y contraseña coinciden
if ($username === $usuarioValido && $password === $contrasenaValida) {
    // Crear la sesión con el nombre de usuario
    $_SESSION['user'] = $username;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
}

