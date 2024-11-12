<?php
    require_once 'checkSession.php';
    // si previamente está logeado entonces esto no se carga
    require_login();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
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
    <h2>Productos</h2>
    <div>
        <form id="createForm">
            <input type="text" id="createProducto" placeholder="Producto" required>
            <input type="number" id="precioProducto" required placeholder="precio" min="0" step="0.01">
            <textarea rows="5" cols="40" id="descripcion" placeholder="Descripcion"></textarea>
            <select name="tipos" id="selectTipo">
                <option value="buffet">Buffet</option>
                <option value="bebida">Bebida</option>
            </select>
            <label for="file-upload" class="custom-file-upload">Seleccionar archivo</label>
            <input id="file-upload" type="file" />
            <button type="submit">Crear Producto</button>
        </form>
    </div>

    <h2>Productos</h2>
    <table id="prodTable">
        <thead>
            <th>ID</th>
            <th>Producto</th>
            <th>Descripcion</th>
            <th>Tipo</th>
            <th>Precio</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </thead>
        <tbody>
        </tbody>
    </table>

    <script src="../javascript/adminProductos.js"></script>
</body>

</html>