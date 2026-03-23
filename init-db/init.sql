USE formulaire_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    mail VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'moderateur', 'superadmin') NOT NULL DEFAULT 'user',
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_connexion TIMESTAMP NULL DEFAULT NULL,
    desactive TIMESTAMP DEFAULT NULL
);
