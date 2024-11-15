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
    <title>Miscelaneo</title>
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
    <h2>Miscelaneos</h2>
    <div>
        <form id="createForm">
            <input type="text" id="concepto" placeholder="Concepto" required>
            <input type="number" id="precioMisc" required placeholder="Precio" min="0" step="0.01">
            <button type="submit">Crear fila</button>
        </form>
    </div>
    <table id="prodTable">
        <thead>
            <th>ID</th>
            <th>Concepto</th>
            <th>Precio</th>
            <th>Acciones</th>
        </thead>
        <tbody>
        </tbody>
    </table>

    <script src="adminMisc.js"></script>
</body>

</html>