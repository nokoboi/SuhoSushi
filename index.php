<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUHO sushi</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="assets/suhologo.png" type="image/x-icon">
</head>

<body>
    <div class="logo">
        <img src="assets/suhologo.png" alt="logo del restaurante suho">
    </div>
    <h1>Bienvenido a Suho Sushi</h1>

    <div class="container">
        <h2>🍱 Elige tu mesa primero 🍱</h2>
        <div class="grid" id="gridMesas"></div>
        <div id="mesaSeleccionada"></div>
        <div id="personas">
            <h2>¡Espera! ¿Cuántas personas sois?</h2>
            <input type="number" id="numeroPersonas" required min="0">
            <button id="botonMenu" onclick="irAlMenu()">Ver Menú 🍣</button>
        </div>
    </div>

    <script src="javascript/mesasJS.js"></script>
    <script src="javascript/logginAdmin.js"></script>
</body>

</html>