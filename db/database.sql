-- Active: 1754913625570@@127.0.0.1@3306@garden
CREATE DATABASE IF NOT EXISTS `garden`;

USE `garden`;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
);

INSERT INTO
    `users` (`name`, `email`, `password`)
VALUES (
        'adrian',
        'adrian@gmail.com',
        SHA2('h3ll0.', 512)
    );

INSERT INTO
    `users` (`name`, `email`, `password`)
VALUES (
        'ana',
        'ana@gmail.com',
        SHA2('h3ll0.', 512)
    );

DROP TABLE IF EXISTS `plantas`;

CREATE TABLE IF NOT EXISTS `plantas` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  categoria ENUM('cactus','ornamental','frutal') NOT NULL,
  familia VARCHAR(100) NOT NULL,
  proximo_riego DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `riegos`;
CREATE TABLE IF NOT EXISTS `riegos` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  planta_id INT NOT NULL,
  fecha_riego DATE NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (planta_id) REFERENCES plantas(id) ON DELETE CASCADE
);


INSERT INTO plantas (nombre, categoria, familia, proximo_riego) VALUES
('Aloe Vera','cactus','Asphodelaceae','2025-08-14'),
('Lavanda','ornamental','Lamiaceae','2025-08-07'),
('Fresa','frutal','Rosaceae','2025-08-09'),
('Lengua de suegra','ornamental','Asparagaceae','2025-08-07'),
('Nopal','cactus','Cactaceae','2025-08-14'),
('Tomatera','frutal','Solanaceae','2025-08-09'),
('Orquídea','ornamental','Orchidaceae','2025-08-07'),
('Higuera','frutal','Moraceae','2025-08-09'),
('Sansevieria','ornamental','Asparagaceae','2025-08-07'),
('Pitahaya','cactus','Cactaceae','2025-08-14');
