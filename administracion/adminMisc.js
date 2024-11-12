const API_URL = 'http://localhost/ProyectoSushi/controllers/Miscelaneos.php';

function limpiarHTML(str) {
    return str.replace(/[^\w.@-]/gi, function (e) {
        return '&#' + e.charCodeAt(0) + ';';
    });
}

function getMisc() {
    fetch(API_URL)
        .then(response => response.json())
        .then(misc => {
            renderMisc(misc);
        })
        .catch(error => console.log('Error: ', error));
}

function renderMisc(miscelaneo) {
    const tableBody = document.querySelector('#prodTable tbody');
    tableBody.innerHTML = '';

    miscelaneo.forEach(misc => {
        const sanitizedId = misc.id;
        const sanitizedConcepto = misc.concepto;
        const precio = misc.precio;

        tableBody.innerHTML += `
        <tr data-id="${misc.id}">
            <td>${misc.id}</td>
            <td>
                <span class="listado">${sanitizedConcepto}</span>
                <input class="edicion" type="text" value="${sanitizedConcepto}">
            </td>
            <td>
                <span class="listado">${precio}</span>
                <input class="edicion" type="number" value="${precio}">
            </td>
            <td class="td-btn">
                <button class="listado" title="Editar" onclick="editMode(${misc.id})">✏️</button>
                <button class="listado" title="Borrar pedido" onclick="deleteMisc(${misc.id})">🗑️</button>
                <button class="edicion" onclick="updateMisc(${misc.id})">✅</button>
                <button class="edicion" onclick="cancelEdit(${misc.id})">❌</button>
            </td>
        </tr>
        `;
    });
}

function createMisc(event) {
    event.preventDefault();
    const concepto = document.getElementById('concepto').value.trim();
    const precio = document.getElementById('precioMisc').value;

    // Crear un objeto FormData
    const formData = new FormData();
    formData.append('concepto', concepto);
    formData.append('precio', precio);

    // Enviamos los datos al controlador
    fetch(`${API_URL}?metodo=nuevo`, {
        method: 'POST',
        body: formData // Enviar FormData directamente
    })
    .then(response => response.json())
    .then(result => {
        console.log('Fila creada: ', result);
        getMisc();
        event.target.reset();
    })
    .catch(error => {
        console.log('Error: ', JSON.stringify(error));
    });
}

function updateMisc(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const newConcepto = row.querySelector('td:nth-child(2) input').value.trim();
    const newPrecio = row.querySelector('td:nth-child(3) input').value.trim();
 

    const formData = new FormData();
    formData.append('concepto', newConcepto);
    formData.append('precio', newPrecio);

    fetch(`${API_URL}?id=${id}&metodo=actualizar`, {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(result => {
            console.log('Miscelaneo actualizado', result);
            getMisc();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar la fila. Por favor, inténtelo de nuevo.');
        });
}

async function deleteMisc(id) {
    try {
        const response = await fetch(`${API_URL}?id=${id}&metodo=eliminar`, {
            method: 'POST'
        });

        if (response.ok) {
            console.log(`Miscelaneo ${id} eliminada correctamente`);
            // Recargar la lista de mesas después de eliminar
            getMisc();
        } else {
            console.error('Error al eliminar la mesa');
        }
    } catch (error) {
        console.error('Error en la solicitud de eliminación:', error);
    }
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
document.getElementById('createForm').addEventListener('submit', createMisc);
getMisc()