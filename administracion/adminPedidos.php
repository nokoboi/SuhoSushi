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
    <title>Pedidos</title>
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
    <h2>Pedidos</h2>

    <!-- Barra de búsqueda -->
    <div class="filtrarFecha">
        <div>
            <label for="startDate">Desde:</label>
            <input type="date" id="startDate">
        </div>
        <div>
            <label for="endDate">Hasta:</label>
            <input type="date" id="endDate">
        </div>
        <div>
            <button class="boton" id="filtro">Filtrar</button>
            <button class="boton" id="borrarFiltro">Limpiar</button>
        </div>
    </div>

    <table id="prodTable">
        <thead>
            <th>ID</th>
            <th>Nº Mesa</th>
            <th>Nº Personas</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <tbody>
        </tbody>
    </table>

    <div id="detallesPedido" style="display: none;">
        
    </div>


    <script src="../javascript/adminPedidos.js"></script>
</body>

</html>