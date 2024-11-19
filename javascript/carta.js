const api_product = 'https://kvnsc.es/controllers/Productos.php';
const api_pedido = 'https://kvnsc.es/ProyectoSushi/controllers/Pedidos.php';

// Obtener el parámetro 'mesa' de la URL
const urlParams = new URLSearchParams(window.location.search);
const numeroMesa = urlParams.get('mesa');
const numPersonas = urlParams.get('personas');

console.log(numeroMesa); // Esto mostrará '5' en la consola
console.log('Personas: ', numPersonas);

// Array para almacenar los productos seleccionados en el carrito
const carrito = [];

// Función para obtener productos de la API y mostrar en el contenedor
async function fetchProducts() {
    try {
        const response = await fetch(api_product);
        const productos = await response.json();
        const productContainer = document.querySelector('.product-container');

        // Limpiar el contenedor antes de agregar productos
        productContainer.innerHTML = '';

        // Separar productos en dos listas
        const productosComida = [];
        const productosBebida = [];

        productos.forEach(producto => {
            // Quitar los "../" de la ruta de la imagen
            const imagenUrl = producto.imagen.replace(/\.\.\//g, '');

            // Crear el contenido del producto
            const productoDiv = document.createElement('div');
            productoDiv.classList.add('producto');

            productoDiv.innerHTML = `
                <img src="${imagenUrl}" alt="${producto.nombre}">
                <h2>${producto.nombre}</h2>
                <p>${producto.descripcion}</p>
                ${producto.precio && producto.precio !== '0.00' ? `<p>Precio: ${producto.precio} €</p>` : ''}
            `;

            // Agregar el evento de clic para añadir al carrito al hacer clic en el producto
            productoDiv.addEventListener('click', () => {
                agregarAlCarrito(producto.id, producto.nombre, producto.precio);
            });

            // Agregar el producto a la lista correspondiente
            if (producto.tipo === 'bebida') {
                productosBebida.push(productoDiv);
            } else {
                productosComida.push(productoDiv);
            }

            // Añadir el producto al contenedor
            productContainer.appendChild(productoDiv);
        });

        // Agregar primero los productos de comida y luego los de bebida
        productosComida.forEach(div => productContainer.appendChild(div));
        productosBebida.forEach(div => productContainer.appendChild(div));

    } catch (error) {
        console.error('Error al obtener los productos:', error);
    }
}

// Función para agregar productos al carrito
function agregarAlCarrito(id, nombre, precio) {
    const productoExistente = carrito.find(item => item.id === id);
    if (productoExistente) {
        // Si el producto ya está en el carrito, aumentar la cantidad
        productoExistente.cantidad += 1;
    } else {
        // Si no está, agregarlo al carrito
        carrito.push({ id, nombre, precio, cantidad: 1 });
    }
    mostrarCarrito();
}

// Función para mostrar el carrito
function mostrarCarrito() {
    const cartItemsContainer = document.querySelector('.cart-items');
    cartItemsContainer.innerHTML = ''; // Limpiar contenido anterior

    carrito.forEach((item, index) => {
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('cart-item');

        // Verificar si el precio es 0.00 y ajustar la visualización
        if (parseFloat(item.precio) === 0.00) {
            itemDiv.innerHTML = `${item.nombre} - Cantidad: ${item.cantidad}`;
        } else {
            itemDiv.innerHTML = `${item.nombre} - Cantidad: ${item.cantidad} - Precio: ${(item.precio * item.cantidad).toFixed(2)}`;
        }

        // Crear botón de disminución de cantidad
        const decreaseButton = document.createElement('button');
        decreaseButton.textContent = '➖';
        decreaseButton.classList.add('decrease-button');

        // Agregar evento de clic al botón para disminuir la cantidad
        decreaseButton.addEventListener('click', () => {
            disminuirCantidad(index);
        });

        itemDiv.appendChild(decreaseButton);
        cartItemsContainer.appendChild(itemDiv);
    });
}


function disminuirCantidad(index) {
    if (carrito[index].cantidad > 1) {
        carrito[index].cantidad -= 1; // Disminuir la cantidad del producto
    } else {
        carrito.splice(index, 1); // Eliminar el producto si la cantidad es 0
    }
    mostrarCarrito(); // Actualizar la visualización del carrito
}

let pedidoBloqueado = false;
let tiempoBloqueo;

document.addEventListener('DOMContentLoaded', () => {
    fetch('controllers/getTiempoBloqueo.php')
        .then(response => response.json())
        .then(data => {
            tiempoBloqueo = data.precio; // Asigna el valor de la base de datos
            console.log(`Tiempo de bloqueo obtenido: ${tiempoBloqueo}`);
        })
        .catch(error => console.error('Error al obtener el tiempo de bloqueo:', error));
});

// Función para confirmar el pedido
async function confirmarPedido() {
    if (pedidoBloqueado) {
        alert('Espera un momento antes de confirmar otro pedido.');
        return;
    }

    try {
        const pedidoData = {
            mesa_id: numeroMesa,
            n_personas: numPersonas,
            fecha: new Date().toISOString().slice(0, 10), // Formato de fecha YYYY-MM-DD
            detalles: carrito.map(item => ({
                producto_id: item.id,
                cantidad: item.cantidad
            }))
        };

        const response = await fetch(`${api_pedido}?metodo=nuevo`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(pedidoData)
        });

        if (!response.ok) {
            throw new Error('Error al confirmar el pedido.');
        }

        const resultado = await response.json();
        console.log('Pedido confirmado:', resultado.id.total);

        if (resultado.id.total) {
            alert(`Pedido confirmado con éxito`);
        } else {
            alert('Pedido confirmado, pero no se pudo calcular el total.');
        }
    } catch (error) {
        console.error('Error en la solicitud de confirmación del pedido:', error);
    }

    // Bloquear el botón y mostrar el temporizador
    bloquearBotonPedido();
}

function bloquearBotonPedido() {
    pedidoBloqueado = true;
    const botonConfirmar = document.getElementById('confirmarPedido');
    const cartContainer = document.querySelector('.cart-container');
    
    // Ocultar el botón y mostrar el temporizador
    botonConfirmar.style.display = 'none';

    // Crear un div de temporizador si no existe
    let temporizadorDiv = document.getElementById('temporizador');
    if (!temporizadorDiv) {
        temporizadorDiv = document.createElement('div');
        temporizadorDiv.id = 'temporizador';
        cartContainer.appendChild(temporizadorDiv);
    }

    temporizadorDiv.style.display = 'block';
    let tiempoRestante = tiempoBloqueo;
    temporizadorDiv.textContent = `Espera ${tiempoRestante} segundos`;

    const intervalo = setInterval(() => {
        tiempoRestante -= 1;
        if (tiempoRestante > 0) {
            clearCart()
            temporizadorDiv.textContent = `Espera ${tiempoRestante} segundos`;
        } else {
            clearInterval(intervalo);
            temporizadorDiv.style.display = 'none'; // Ocultar el temporizador
            botonConfirmar.style.display = 'block'; // Mostrar el botón de nuevo
            pedidoBloqueado = false;
        }
    }, 1000);
}

function clearCart() {
    carrito.length = 0; // Método rápido para vaciar el array

    // Actualizar la visualización del carrito
    mostrarCarrito();
}

// Evento para confirmar el pedido
document.getElementById('confirmarPedido').addEventListener('click', () => {
    if (carrito.length === 0) {
        alert('Debes añadir algún producto primero.');
        return; // Evitar que continúe si el carrito está vacío
    }

    confirmarPedido(); // Llama a la función que gestiona la confirmación del pedido
});
// Asegúrate de que el temporizador esté inicialmente oculto al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    const temporizadorDiv = document.createElement('div');
    temporizadorDiv.id = 'temporizador';
    temporizadorDiv.style.display = 'none'; // Inicialmente oculto
    document.querySelector('.cart-container').appendChild(temporizadorDiv);
});

// Llamar a la función para cargar los productos al cargar la página
fetchProducts();
