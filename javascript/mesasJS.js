const API_MESAS = 'http://localhost/ProyectoSushi/controllers/Mesas.php';

const gridMesas = document.getElementById('gridMesas');
const mesaSeleccionadaDiv = document.getElementById('mesaSeleccionada');
const botonMenu = document.getElementById('botonMenu');
const inputsPersonas = document.getElementById('personas')

let mesaSeleccionadaNumero = null;

async function cargarMesas() {
    try {
        const response = await fetch(API_MESAS);
        const mesas = await response.json();

        gridMesas.innerHTML = '';

        mesas.forEach(mesa => {
            const divMesa = document.createElement('div');
            divMesa.className = 'mesa';
            divMesa.setAttribute('data-id', mesa.id); // Añade el data-id
            divMesa.innerHTML = `Mesa ${mesa.numero_mesa}`;
            divMesa.addEventListener('click', () => seleccionarMesa(mesa.id));
            gridMesas.appendChild(divMesa);
        });
    } catch (error) {
        console.error('Error al cargar las mesas', error);
    }
}

function seleccionarMesa(idMesa) {
    const mesas = document.querySelectorAll('.mesa');
    mesas.forEach(mesa => mesa.classList.remove('seleccionada'));

    // Seleccionar el elemento por su data-id
    const mesaSeleccionada = document.querySelector(`.mesa[data-id='${idMesa}']`);
    if (mesaSeleccionada) {
        mesaSeleccionada.classList.add('seleccionada');
        console.log(mesaSeleccionada);

        // Obtener el número de la mesa desde el texto del elemento
        const numeroMesa = mesaSeleccionada.textContent.trim().split(' ')[1]; // Asume que el formato es "Mesa X"
        
        mesaSeleccionadaDiv.innerHTML = `¡Ñam! Has elegido la Mesa ${numeroMesa} 🎉`;
        mesaSeleccionadaNumero = idMesa; 

        inputsPersonas.style.display = 'block';
        botonMenu.style.display = 'inline-block';
    } else {
        console.error('No se encontró la mesa seleccionada');
    }
}

function irAlMenu() {
    const nPersonas = parseInt(document.getElementById('numeroPersonas').value, 10);
    
    // Comprueba si mesaSeleccionadaNumero es válido y nPersonas es un número positivo
    if (mesaSeleccionadaNumero && !isNaN(nPersonas) && nPersonas > 0) {
        const url = `carta.html?mesa=${mesaSeleccionadaNumero}&personas=${nPersonas}`;
        console.log(`Redirigiendo a: ${url}`);
        window.location.href = url;
    } else {
        alert('Tienes que indicar el número de personas de manera válida');
    }
}


function toggleMenu() {
    document.querySelector('.sidebar').classList.toggle('active');
}

// Cargamos las mesas cuando ya se haga cargado la pagina
document.addEventListener('DOMContentLoaded', cargarMesas);