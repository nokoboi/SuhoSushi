CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL
);

CREATE TABLE mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_mesa VARCHAR(5) NOT NULL UNIQUE
);


CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT,
    n_personas INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id)
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tipo ENUM('buffet', 'bebida') NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    imagen varchar(100) NOT NULL,
);


CREATE TABLE detalle_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);


CREATE TABLE miscelaneo{
    id INT PRIMARY KEY,
    concepto VARCHAR(30) NOT NULL,
    precio DECIMAL(4,2) NOT NULL
}