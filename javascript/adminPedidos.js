const API_URL = 'https://kvnsc.es/controllers/Pedidos.php';

function limpiarHTML(str) {
    return str.replace(/[^\w.@-]/gi, function (e) {
        return '&#' + e.charCodeAt(0) + ';';
    });
}

function getPedidos() {
    fetch(API_URL)
        .then(response => response.json())
        .then(pedidos => {
            renderPedidos(pedidos);
        })
        .catch(error => console.log('Error: ', error));
}

function renderPedidos(pedidos) {
    const tableBody = document.querySelector('#prodTable tbody');
    tableBody.innerHTML = '';

    pedidos.forEach(ped => {
        const sanitizedMesa = ped.mesa_id;
        const sanitizedPersona = ped.n_personas;
        const fecha = ped.fecha;

        tableBody.innerHTML += `
        <tr data-id="${ped.id}">
            <td>${ped.id}</td>
            <td>
                <span class="listado">${sanitizedMesa}</span>
                <input class="edicion" type="text" value="${sanitizedMesa}">
            </td>
            <td>
                <span class="listado">${sanitizedPersona}</span>
                <input class="edicion" type="text" value="${sanitizedPersona}">
            </td>
            <td>
                <span class="listado">${fecha}</span>
                <input class="edicion" type="date" value="${fecha}">
            </td>
            <td>
                <span class="listado">${ped.total}</span>
                <input class="edicion" type="number" value="${ped.total}">
            </td>
            <td>
                <span class="listado">${ped.estado}</span>
                <select class="edicion">
                    <option value="terminado">terminado</option>
                    <option value="en curso">en curso</option>
                </select>
            </td>
            <td class="td-btn">
                <button class="listado" title="Editar" onclick="editMode(${ped.id})">✏️</button>
                <button class="listado" title="Borrar pedido" onclick="deletePed(${ped.id})">🗑️</button>
                <button class="edicion" onclick="updatePed(${ped.id})">✅</button>
                <button class="edicion" onclick="cancelEdit(${ped.id})">❌</button>
                <button class="listado" title="Ver detalles" onclick="verDetallesPedido(${ped.id})">🔍</button>
            </td>
        </tr>
        `;
    });
}

function updatePed(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const newMesa = row.querySelector('td:nth-child(2) input').value.trim();
    const newPersona = row.querySelector('td:nth-child(3) input').value.trim();
    const newFecha = row.querySelector('td:nth-child(4) input').value.trim();
    const newTotal = row.querySelector('td:nth-child(5) input').value.trim();
    const newTipo = row.querySelector('td:nth-child(6) select').value.trim();

    const formData = new FormData();
    formData.append('mesa_id', newMesa);
    formData.append('n_personas', newPersona);
    formData.append('fecha', newFecha);
    formData.append('estado',newTipo)
    formData.append('total',newTotal)
    console.log(newTipo)

    fetch(`${API_URL}?id=${id}&metodo=actualizar`, {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(result => {
            console.log('Pedido actualizado', result);
            getPedidos();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar el pedido. Por favor, inténtelo de nuevo.');
        });
}

function editMode(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.querySelectorAll('.edicion').forEach(ro => {
        ro.style.display = 'inline-block';
    });
    row.querySelectorAll('.listado').forEach(ro => {
        ro.style.display = 'none';
    });
}

function cancelEdit(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.querySelectorAll('.edicion').forEach(ro => {
        ro.style.display = 'none';
    });
    row.querySelectorAll('.listado').forEach(ro => {
        ro.style.display = 'inline-block';
    });
}

// función para obtener los detalles del pedido
function verDetallesPedido(pedidoId) {

    const detallesContainer = document.getElementById('detallesPedido');
    detallesContainer.style.display = 'block';

    const tablaPedidos = document.getElementById('prodTable')
    tablaPedidos.style.display = 'none'

    fetch(`${API_URL}?id=${pedidoId}&metodo=detalles`, {
        method: 'GET'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar los detalles en un modal o en una sección de la página
                mostrarDetallesPedido(data.detalles, data.total, pedidoId);
                console.log(data.detalles)
            } else {
                alert('Error al obtener los detalles del pedido: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un problema al obtener los detalles del pedido.');
        });
    
}

// Función para mostrar los detalles del pedido
function mostrarDetallesPedido(detalles, total, pedidoID) {
    const detallesContainer = document.getElementById('detallesPedido');
    detallesContainer.innerHTML = ''; // Limpiar antes de mostrar nuevos detalles

    // Crear la estructura de la tabla de la factura
    const table = document.createElement('table');
    table.classList.add('factura-table');

    // Crear la cabecera de la tabla
    const thead = document.createElement('thead');
    thead.innerHTML = `
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
        </tr>
    `;
    table.appendChild(thead);

    // Crear el cuerpo de la tabla con los detalles de los productos
    const tbody = document.createElement('tbody');

    detalles.forEach(detalle => {
        const tr = document.createElement('tr');

        // Mostrar cada producto de los detalles
        if (detalle.precio > 0) {
            tr.innerHTML = `
                <td>${detalle.producto_nombre}</td>
                <td>${detalle.cantidad}</td>
                <td>$${detalle.precio}</td>
                <td>$${(detalle.precio * detalle.cantidad).toFixed(2)}</td>
            `;
        }
        tbody.appendChild(tr);
    });

    console.log(detalles.find(detalle => detalle.producto_nombre === "Buffet"))
    // Si el precio del buffet es parte de los detalles
    const buffetDetalle = detalles.find(detalle => detalle.producto_nombre === "Buffet");
    if (buffetDetalle) {
        const buffetRow = document.createElement('tr');
        buffetRow.innerHTML = `
            <td>${buffetDetalle.producto_nombre}</td>
            <td>${buffetDetalle.cantidad}</td>
            <td>$${buffetDetalle.precio}</td>
            <td>$${(buffetDetalle.precio * buffetDetalle.cantidad).toFixed(2)}</td>
        `;
        tbody.appendChild(buffetRow);
    }

    table.appendChild(tbody);

    // Añadir una fila para el total
    const totalRow = document.createElement('tr');
    totalRow.innerHTML = `
        <td colspan="3" style="text-align: right; font-weight: bold;">Total</td>
        <td>$${total}</td>
    `;
    table.appendChild(totalRow);

    // Añadir una fila con el botón de impresión
    const printRow = document.createElement('tr');
    printRow.innerHTML = `
        <td colspan="4" style="text-align: center;">
            <button id="imprimirFacturaBtn">Imprimir Factura</button>
        </td>
    `;
    table.appendChild(printRow);

    // Añadir la tabla al contenedor
    detallesContainer.appendChild(table);

    // Función para imprimir la factura
    document.getElementById('imprimirFacturaBtn').addEventListener('click', function () {
        const tablaPedidos = document.getElementById('prodTable')
        tablaPedidos.style.display = 'table'

        const detallesContainer = document.getElementById('detallesPedido');
        detallesContainer.style.display = 'none';

        fetch(`${API_URL}?id=${pedidoID}&metodo=terminar`, {
            method: 'POST',  // Usar POST si necesitas manejar datos sensibles
        })
        .then(response => response.json())
        .then(data => {
            console.log("Pedido marcado como terminado.", data);
            getPedidos()
        })
        .catch(error => {
            console.error('Error:', error);
        });

        imprimirFactura(table);
    });
}


// Función para imprimir la factura
function imprimirFactura(contenedor) {
    const ventanaImpresion = window.open('', '', 'width=800,height=600');

    // Agregar el encabezado y el estilo CSS a la ventana de impresión
    ventanaImpresion.document.write('<html><head><title>Factura</title>');
    ventanaImpresion.document.write('<style>');
    ventanaImpresion.document.write(`
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .factura-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .factura-table th,
        .factura-table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .factura-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .factura-table td {
            color: #555;
        }
        .factura-table tr:last-child td {
            display:none
        }
    `);
    ventanaImpresion.document.write('</style>');
    ventanaImpresion.document.write('</head><body>');
    ventanaImpresion.document.write('<h2>Factura - SUHOSUSHI</h2>');
    console.log(contenedor)

    // Asegurarse de que el contenido de la tabla se inserte correctamente como HTML
    ventanaImpresion.document.write(contenedor.outerHTML);  // Usamos outerHTML para incluir la tabla con los elementos hijos

    ventanaImpresion.document.write('</body></html>');
    ventanaImpresion.document.close();
    ventanaImpresion.print();
}

// Filtrar pedidos por rango de fecha
document.getElementById('filtro').addEventListener('click', filterPedidosByDateRange);

// Nueva función para filtrar por fecha
function filterPedidosByDateRange() {
    const startDate = document.querySelector('#startDate').value;
    const endDate = document.querySelector('#endDate').value;

    if (startDate && endDate) {
        fetch(API_URL)
            .then(response => response.json())
            .then(pedidos => {
                const filteredPedidos = pedidos.filter(ped => {
                    const pedidoFecha = new Date(ped.fecha);
                    return pedidoFecha >= new Date(startDate) && pedidoFecha <= new Date(endDate);
                });
                renderPedidos(filteredPedidos);
            })
            .catch(error => console.log('Error: ', error));
    } else {
        getPedidos();
    }
}

document.getElementById('borrarFiltro').addEventListener('click', clearDateFilter);

function clearDateFilter() {
    document.querySelector('#startDate').value = '';
    document.querySelector('#endDate').value = '';
    getPedidos();
}

getPedidos();
