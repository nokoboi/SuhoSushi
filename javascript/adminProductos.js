const API_URL = 'http://localhost/ProyectoSushi/controllers/Productos.php';
//const errorElement = document.getElementById('createError');

function limpiarHTML(str) {
    return str.replace(/[^\w.@-]/gi, function (e) {
        return '&#' + e.charCodeAt(0) + ';';
    });
}

function validarProd(producto) {
    return producto.length >= 2 && producto.length <= 50;
}

function getProductos() {
    fetch(API_URL)
        .then(response => response.json())
        .then(productos => {
            const tableBody = document.querySelector('#prodTable tbody')
            tableBody.innerHTML = '';

            productos.forEach(prod => {
                const sanitizedProd = limpiarHTML(prod.nombre)
                const sanitizedDesc = limpiarHTML(prod.descripcion) ? prod.descripcion : 'No tiene descripción';
                const precio = prod.precio ? prod.precio : 0.0

                tableBody.innerHTML += `
                <tr data-id="${prod.id}">
                    <td>${prod.id}</td>
                    <td>
                        <span class="listado">${sanitizedProd}</span>
                        <input class="edicion" type="text" value="${sanitizedProd}">
                    </td>
                    <td>
                        <span class="listado">${sanitizedDesc}</span>
                        <textarea class="edicion" id="descripcion">${sanitizedDesc}</textarea>
                    </td>
                    <td>
                        <span class="listado">${prod.tipo}</span>
                        <select name="tipos" class="edicion">
                            <option value="buffet">Buffet</option>
                            <option value="bebida">Bebida</option>
                        </select>
                    </td>
                    <td>
                        <span class="listado">${precio}</span>
                        <input class="edicion" type="text" value="${precio}">
                        
                    </td>
                    <td>
                        <span class="listado"><img src="${prod.imagen}"/></span>
                        <input class="edicion" type="file">
                    </td>
                    <td class="td-btn">
                        <button class="listado" onclick="editMode(${prod.id})">✏️</button>
                        <button class="listado" onclick="deleteProd(${prod.id})">🗑️</button>
                        <button class="edicion" onclick="updateProd(${prod.id})">✅</button>
                        <button class="edicion" onclick="cancelEdit(${prod.id})">❌</button>
                    </td>
                </tr>
            `
            });
        })
        .catch(error => console.log('Error: ', error));
}

function createProduct(event) {
    event.preventDefault();
    const nomProd = document.getElementById('createProducto').value.trim();
    const precio = document.getElementById('precioProducto').value;
    const desc = document.getElementById('descripcion').value.trim();
    const tipos = document.getElementById('selectTipo').value;
    const imgInput = document.getElementById('file-upload');

    // Crear un objeto FormData
    const formData = new FormData();
    formData.append('nombre', nomProd);
    formData.append('descripcion', desc);
    formData.append('tipo', tipos);
    formData.append('precio', precio);
    formData.append('imagen', imgInput.files[0]); // Agregar el archivo de imagen

    // Enviamos los datos al controlador
    fetch(`${API_URL}?metodo=nuevo`, {
        method: 'POST',
        body: formData // Enviar FormData directamente
    })
    .then(response => response.json())
    .then(result => {
        console.log('Producto creado: ', result);
        getProductos();
        event.target.reset();
    })
    .catch(error => {
        console.log('Error: ', JSON.stringify(error));
    });
}

function updateProd(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const newNombre = row.querySelector('td:nth-child(2) input').value.trim();
    const newDes = row.querySelector('td:nth-child(3) textarea').value.trim();
    const newTipo = row.querySelector('td:nth-child(4) select').value.trim();
    const newPrecio = row.querySelector('td:nth-child(5) input').value.trim();
    const newImagen = row.querySelector('td:nth-child(6) input[type="file"]');

    const formData = new FormData();
    formData.append('nombre', newNombre);
    formData.append('descripcion', newDes);
    formData.append('tipo', newTipo);
    formData.append('precio', newPrecio);

    // Manejo de imagen
    if (newImagen.files.length > 0) {
        // Si hay una nueva imagen, agregarla al FormData
        formData.append('imagen', newImagen.files[0]);
    } else {
        // Si no hay nueva imagen, mantener la ruta relativa de la imagen existente
        const existingImageSrc = row.querySelector('img').src;
        const imageName = existingImageSrc.split('/').pop();
        formData.append('imagen', `../assets/${imageName}`);
    }

    fetch(`${API_URL}?id=${id}&metodo=actualizar`, {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
        console.log('Producto actualizado', result);
        getProductos();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el producto. Por favor, inténtelo de nuevo.');
    });
}

function deleteProd(id){
    if(confirm('¿Estás seguro de que quieres eliminar este producto?')){
        fetch(`${API_URL}?id=${id}&metodo=eliminar`, {
             method: 'POST',
        })
        .then(response => response.json())
        .then(result => {
             console.log('Producto eliminado: ', result);
             getProductos();
        })
        .catch(error => console.error('Error: ', error));
     }
}

function editMode(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.querySelectorAll('.edicion').forEach(ro => {
        ro.style.display = 'inline-block';
    })
    row.querySelectorAll('.listado').forEach(ro => {
        ro.style.display = 'none';
    })
}
function cancelEdit(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.querySelectorAll('.edicion').forEach(ro => {
        ro.style.display = 'none';
    })
    row.querySelectorAll('.listado').forEach(ro => {
        ro.style.display = 'inline-block';
    })
}

function mostrarErrores(errores){
    arrayErrores = Object.values(errores);
    errorElement.innerHTML = '<ul>';
    arrayErrores.forEach(error => {
        return errorElement.innerHTML += `<li>${error}</li>`;
    });
    errorElement.innerHTML += '</ul>';
}

document.getElementById('createForm').addEventListener('submit', createProduct);
getProductos()
