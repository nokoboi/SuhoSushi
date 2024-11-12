<?php
require_once 'Database.php';
require_once 'enviarCorreos.php';

class Usuario{
    private $conn;

    private $url = 'http://localhost/ProyectoSushi/administracion'; // URL base para verificar y recuperar cuentas
    public function __construct(){
        $this->conn = new mYsqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            die("Error en la conexion: " . $this->conn->connect_error);
        }
    }

    public function getAll(){
        $result = $this->conn->query("SELECT * FROM usuarios;");
        return $result->fetch_all(MYSQLI_ASSOC);    
    }

    public function generarToken()
    {
        return bin2hex(random_bytes(32));
    }

    public function inicioSesion($nombreUsuario, $password)
    {
        $sql = "SELECT id, nombre_usuario, contrasena FROM usuarios WHERE nombre_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $nombreUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $resultado = ['success' => 'info', "message" => 'Usuario no encontrado'];

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['contrasena'])) {
                $resultado = ["success" => "success", "message" => "Has iniciado sesión con " . $nombreUsuario, "id" => $row['id']];
            }
        } else {
            $resultado = ['success' => 'error', 'message' => 'Credenciales inválidas o cuenta no verificada'];
        }

        return $resultado;
    }

    /**
     * Verifica si un email ya existe en la base de datos.
     * 
     * @param string $email Email a verificar.
     * @return bool True si el email existe, False en caso contrario.
     */
    public function existeEmail($email)
    {
        $check_sql = "SELECT id FROM usuarios WHERE email = ?";
        $check_stmt = $this->conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();

        $result = $check_stmt->get_result();

        return $result->num_rows > 0;
    }

    /**
     * Inicia el proceso de recuperación de contraseña generando y enviando un token.
     * 
     * @param string $email Email del usuario.
     * @return array Resultado de la solicitud de recuperación, con mensaje de éxito o error.
     */
    public function recuperarPassword($email)
    {
        $existe = $this->existeEmail($email);

        $resultado = ["success" => 'info', "message" => "El correo electrónico proporcionado no corresponde a ningún usuario registrado."];

        if ($existe) {
            $token = $this->generarToken();

            $sql = "UPDATE usuarios SET token_recuperacion = ? WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $token, $email);

            if ($stmt->execute()) {
                $mensaje = "Para restablecer tu contraseña, haz clic en este enlace: $this->url/restablecerAdmin.php?token=$token";
                $mensaje = Correo::enviarCorreo($email, "Cliente", "Restablecer Contraseña", $mensaje);
                $resultado = ["success" => 'success', "message" => "Se ha enviado un enlace de recuperación a tu correo"];
            } else {
                $resultado = ["success" => 'error', "message" => "Error al procesar la solicitud"];
            }
        }
        return $resultado;
    }

    /**
     * Restablece la contraseña del usuario utilizando un token de recuperación.
     * 
     * @param string $token Token de recuperación.
     * @param string $nuevaPassword Nueva contraseña del usuario.
     * @return array Resultado de la operación, incluyendo éxito o error y mensaje.
     */
    public function restablecerPassword($token, $nuevaPassword)
    {
        $password = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        $sql = "SELECT id FROM usuarios WHERE token_recuperacion=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];

            $sql = "UPDATE usuarios SET contrasena=?, token_recuperacion=null WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $password, $user_id);

            if ($stmt->execute()) {
                $resultado = ["success" => 'success', "message" => "Contraseña restablecida con éxito. Ahora puedes iniciar sesión."];
            } else {
                $resultado = ["success" => 'error', "message" => "Hubo un problema al restablecer la contraseña. Inténtalo de nuevo más tarde."];
            }
        } else {
            $resultado = ["success" => 'error', "message" => "El token no es válido o ha expirado."];
        }
        return $resultado;
    }
}