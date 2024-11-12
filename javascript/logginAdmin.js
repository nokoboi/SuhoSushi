const API_URL = 'http://localhost/ProyectoSushi/controllers/usuarios.php';
const btnAdmin = document.getElementById('btnAdmin');

function validateLoginForm() {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorMessages = document.getElementById('errorMessages');
    errorMessages.innerHTML = '';

    let isValid = true;

    // Validar nombre de usuario
    if (username === '') {
        errorMessages.innerHTML += 'El nombre de usuario es obligatorio.<br>';
        isValid = false;
    } else if (username.length < 3) {
        errorMessages.innerHTML += 'El nombre de usuario debe tener al menos 3 caracteres.<br>';
        isValid = false;
    }

    // Validar contraseña
    if (password === '') {
        errorMessages.innerHTML += 'La contraseña es obligatoria.<br>';
        isValid = false;
    } else if (password.length < 4) {
        errorMessages.innerHTML += 'La contraseña debe tener al menos 4 caracteres.<br>';
        isValid = false;
    }

    return isValid;
}

btnAdmin.addEventListener('click', function(event) {
    event.preventDefault(); // Evita que el formulario se envíe de inmediato

    if (validateLoginForm()) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        fetch(API_URL)
            .then(response => response.json())
            .then(users => {
                console.log(users)
                // Suponiendo que `users` es un array de objetos con `username` y `password`
                const foundUser = users.find(user => user.nombre_usuario === username && user.contrasena === password);

                if (foundUser) {
                    console.log("Inicio de sesión exitoso. Redirigiendo...");
                    // Redirigir a la página de administración
                    window.location.href = 'prueba.html'; // Cambia 'admin.html' por la URL correcta
                } else {
                    document.getElementById('errorMessages').innerHTML = 'Nombre de usuario o contraseña incorrectos.<br>';
                    console.log("Error en el inicio de sesión.");
                }
            })
            .catch(error => {
                console.error('Error al consultar la API:', error);
                document.getElementById('errorMessages').innerHTML = 'Hubo un error al verificar las credenciales.<br>';
            });
    } else {
        console.log("El formulario tiene errores.");
    }
});
