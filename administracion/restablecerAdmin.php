<?php
include_once '../data/Usuario.php';

$usuario = new Usuario();
// verificar si se ha proporcionado un token
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['nuevaPassword'])) {
        $resultado = $usuario->restablecerPassword($token, $_POST['nuevaPassword']);
        $mensaje = $resultado['message'];
    }
} else {
    header("Location: loginAdmin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
</head>

<body>
    <div class="container">
        <h1>Restablecer contraseña</h1>
        <?php
        if (!empty($mensaje)): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
            <?php if ($resultado['success']): ?>
                <a href="loginAdmin.php" class="boton">Ir a iniciar sesión</a>
            <?php endif;
        else:
            ?>
            <form method="POST">
                <input type="password" name="nuevaPassword" required placeholder="Nueva contraseña">
                <input type="password" name="confirmarPassword" required placeholder="Repite la contraseña">
                <input type="submit" value="Restablecer contraseña">
            </form>
        <?php endif; ?>
    </div>

    <script src="restablecer.js"></script>
</body>

</html>