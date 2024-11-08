-- Crear base de datos tienda
CREATE DATABASE tienda;

-- --------------------------------------------------------

CREATE TABLE carritos (
  id SERIAL PRIMARY KEY,
  producto INT NOT NULL,
  usuario INT NOT NULL,
  stock SMALLINT NOT NULL,
  precio_total INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

CREATE TABLE productos (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  activo SMALLINT NOT NULL DEFAULT 1,
  precio SMALLINT NOT NULL,
  stock SMALLINT NOT NULL,
  ean_13 VARCHAR(20) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(60) NOT NULL,
  correo VARCHAR(60) NOT NULL,
  password VARCHAR(120) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Volcado de datos para la tabla users
INSERT INTO users (nombre, correo, password, created_at) VALUES
('prueba@gmail.com', 'prueba@gmail.com', '$2b$10$cjOUjWz5dQI9vNFGoO178O2oA6/daGQaTsKfb30RTVQMiZZQr5rv2', '2024-11-08 02:00:19');
