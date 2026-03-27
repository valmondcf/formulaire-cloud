USE formulaire_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    mail VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role TINYINT NOT NULL DEFAULT 0,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_connexion TIMESTAMP NULL DEFAULT NULL,
    desactive TIMESTAMP DEFAULT NULL
);

CREATE TABLE characters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    species VARCHAR(100),
    gender VARCHAR(50),
    status ENUM('active', 'abstracted', 'deleted') DEFAULT 'active',
    first_appearance VARCHAR(150),
    voice_actor VARCHAR(100),
    description TEXT,
    birthday DATE,
    avatar_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    door_url VARCHAR(255),
);

CREATE TABLE character_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    character_id INT NOT NULL,
    personality TEXT,
    abilities TEXT,
    relationships TEXT,
    trivia TEXT,
    quote VARCHAR(500),
    theories TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_character
        FOREIGN KEY (character_id)
        REFERENCES characters(id)
        ON DELETE CASCADE
);