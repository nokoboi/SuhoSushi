const API_MESAS = 'http://localhost/ProyectoSushi/controllers/Mesas.php';

const gridMesas = document.getElementById('gridMesas');

async function cargarMesas() {
    try {
        const response = await fetch(API_MESAS);
        const mesas = await response.json();

        gridMesas.innerHTML = '';

        mesas.forEach(mesa => {
            const divMesa = document.createElement('div');
            divMesa.className = 'mesa admin';
            divMesa.innerHTML = `Mesa ${mesa.numero_mesa}`;
            
            // Crear botón de eliminación
            const botonEliminar = document.createElement('button');
            botonEliminar.className = 'edicionMesa';
            botonEliminar.innerHTML = '🗑️';
            botonEliminar.addEventListener('click', (event) => {
                event.stopPropagation(); // Evita que el clic seleccione la mesa
                eliminarMesa(mesa.id);
            });
            
            divMesa.appendChild(botonEliminar);
            divMesa.addEventListener('click', () => seleccionarMesa(mesa.numero_mesa));
            gridMesas.appendChild(divMesa);
        });
    } catch (error) {
        console.error('Error al cargar las mesas', error);
    }
}

function seleccionarMesa(numeroMesa) {
    const mesas = document.querySelectorAll('.mesa');
    mesas.forEach(mesa => mesa.classList.remove('seleccionada'));

    const mesaSeleccionada = document.querySelector(`.mesa:nth-child(${numeroMesa})`);
    mesaSeleccionada.classList.add('seleccionada');
}

// Función para eliminar la mesa
async function eliminarMesa(id) {
    try {
        const response = await fetch(`${API_MESAS}?id=${id}&metodo=eliminar`, {
            method: 'POST'
        });

        if (response.ok) {
            console.log(`Mesa ${id} eliminada correctamente`);
            // Recargar la lista de mesas después de eliminar
            cargarMesas();
        } else {
            console.error('Error al eliminar la mesa');
        }
    } catch (error) {
        console.error('Error en la solicitud de eliminación:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    cargarMesas();
    
    // Agregar el evento al botón para crear una nueva mesa
    document.getElementById('botonCrearMesa').addEventListener('click', () => {
        const numeroMesa = document.getElementById('numeroMesaInput').value;
        if (numeroMesa >0) {
            crearMesa(numeroMesa);
            document.getElementById('numeroMesaInput').value = 0;
        } else if(numeroMesa <=0){
            alert('Ingresa un numero de mesa correcto')
            console.log('Por favor, ingresa un número de mesa');
        }
    });
});

async function crearMesa(numeroMesa) {
    try {
        const response = await fetch(`${API_MESAS}?metodo=nuevo`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ numero_mesa: numeroMesa })
        });

        if (response.ok) {
            console.log(`Mesa ${numeroMesa} creada correctamente`);
            // Recargar la lista de mesas después de crear una nueva
            cargarMesas();
        } else {
            console.error('Error al crear la mesa');
        }
    } catch (error) {
        console.error('Error en la solicitud de creación:', error);
    }
}