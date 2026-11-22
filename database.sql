CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categorias (id, nombre) VALUES
(1, 'Familia'),
(2, 'Amigos'),
(3, 'Trabajo'),
(4, 'Universidad'),
(5, 'Otro');

CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    ciudad VARCHAR(80) NOT NULL,
    categoria_id INT NOT NULL,
    favorito TINYINT(1) NOT NULL DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO contactos (nombre, telefono, correo, ciudad, categoria_id, favorito) VALUES
('Patricia Alexandra Leon', '0991234567', 'pattyaleo@gmail.com', 'Riobamba', 4, 1),
('Samuel Leon', '0967599906', 'samaleo@outlook.com', 'Guayaquil', 3, 0),
('Luz Gaucha', '0981472555', 'luzgauchita@hotmail.com', 'Riobamba', 1, 1),
('Diego Puela', '0991414088', 'diegopuelar@yahoo.com', 'Loja', 2, 0);