<?php
    require_once 'checkSession.php';
    // si previamente está logeado entonces esto no se carga
    if(is_logged_in()){
        header('Location: adminProductos.php');
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="logo">
        <img src="../assets/suhologo.png" alt="Logo">
    </div>
    
    <div class="container" class="inicioSesion">
        <h1>Iniciar Sesión</h1>
        <form method="POST" action="../controllers/Usuarios.php" id="loginAdminForm" class="login-form">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <a class="abrir-modal-recuperar">Recuperar Contraseña</a>

            <button type="submit" id="loginAdmin" class="btn-login">Ingresar</button>
        </form>
    </div>

    <div id="miModalRecuperar" class="modal">
        <div class="modal-contenido">
            <span class="cerrarRecuperar">&times;</span>
            <h2>Recuperar Contraseña</h2>
            <form method="POST" action="../controllers/Usuarios.php">
                <input type="email" name="email" required placeholder="Correo electrónico">
                <input type="submit" name="recuperar" value="Recuperar contraseña">
            </form>
        </div>
    </div>

    <script src="login2.js"></script>
</body>
</html>