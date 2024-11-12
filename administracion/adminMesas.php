<?php
require_once 'checkSession.php';
// si previamente está logeado entonces esto no se carga
require_login();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <nav>
        <a href="adminProductos.php">Productos</a>
        <a href="adminMesas.php">Mesas</a>
        <a href="adminPedidos.php">Pedidos</a>
        <a href="adminMisc.php">Miscelaneo</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>

    <h2>Mesas</h2>
    <div class="container">
        <div class="grid" id="gridMesas"></div>
        <div class="añadirMesa">
            <input type="number" id="numeroMesaInput" placeholder="Número de mesa">
            <button id="botonCrearMesa" class="boton-emoji">+</button>
        </div>


    </div>

    <script src="adminMesas.js"></script>
</body>

</html>