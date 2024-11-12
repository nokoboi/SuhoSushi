<?php
// Sesión: Almacenar y persistir datos del usuario mientras navega por un sitio web hasta que se cierra la sesión o el navegador
// Los datos de la sesion se guardan en el servidor, son transparentes para el cliente
// Hay que utilizar session_start() en todos los archivos donde se quiera utilizar $_SESSION
session_start();

function is_logged_in(){
    return isset($_SESSION['user']);
}

function require_login(){
    if(!is_logged_in()){
        header('Location: loginAdmin.php');
    }
}