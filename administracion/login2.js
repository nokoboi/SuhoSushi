// const formulario = document.getElementById('loginAdminForm');

// formulario.addEventListener('submit', function(event){
//     event.preventDefault();
//     // Es para para codificar una parte de una URL, asegurando que caracteres especiales no interfieran con la estructura de la URL
//     const inputname = encodeURIComponent(document.getElementById('username').value);
//     const inputpassword = encodeURIComponent(document.getElementById('password').value);  
    
//     console.log(inputname)
//     console.log(inputpassword)

//     // Mandar datos a php
//     fetch('login.php',{
//         method: 'POST',
//         headers: {
//             'Content-type':'application/x-www-form-urlencoded'
//         },
//         body: `username=${inputname}&password=${inputpassword}`
//     })
//     .then(response => response.json())
//     .then(data => {
//         if(data.success){
//             window.location.href = `adminProductos.php`;
//         }else{
//             alert('Inicio de sesión fallido, por favor inténtalo otra vez');
//         }
//     })
//     .catch(error =>{
//         console.error('Error: '+error)
//         alert('Ups, algo anda mal. Inténtalo de nuevo más tarde');
//     });
// });

// Obtener los modales
const modalRecuperar = document.getElementById('miModalRecuperar');
const inicioSesion = document.querySelector('.container')

// Botones que abre el modal
const btnRecuperar = document.querySelector('.abrir-modal-recuperar');

// Obtener el elemento span que cierra el modal
const spanRecuperar = document.querySelector('.cerrarRecuperar');

// Abrir modal registro
btnRecuperar.onclick = function(){
    modalRecuperar.style.display = "flex"
    inicioSesion.style.display = "none"
}

spanRecuperar.onclick = function(){
    modalRecuperar.style.display = "none"
    inicioSesion.style.display="block"
}

// Cerrar el modal cuando el usuario hace click fuera del modal

window.onclick = function(event){
    if(event.target==modalRecuperar){
        modalRecuperar.style.display = "none"
        inicioSesion.style.display="flex"
    }
}