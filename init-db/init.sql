USE formulaire_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    mail VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role TINYINT NOT NULL DEFAULT 0,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_connexion TIMESTAMP NULL DEFAULT NULL,
    desactive TIMESTAMP DEFAULT NULL,
    avatar VARCHAR(255) NOT NULL
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

CREATE TABLE forum (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(100) NOT NULL,
    libelle VARCHAR(100),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ordre INT
);

CREATE TABLE topics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_forum INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_users INT NOT NULL,
    statut TINYINT DEFAULT 0,
    CONSTRAINT fk_topic_forum
        FOREIGN KEY (id_forum)
        REFERENCES forum(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_topic_user
        FOREIGN KEY (id_users)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE commentaires (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_topic INT NOT NULL,
    id_users INT NOT NULL,
    contenu TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_comment_topic
        FOREIGN KEY (id_topic)
        REFERENCES topics(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_comment_user
        FOREIGN KEY (id_users)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE annonces (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_users INT NOT NULL,
    CONSTRAINT fk_annonce_user
        FOREIGN KEY (id_users)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE roles (
    id INT NOT NULL AUTO_INCREMENT,
    role INT NOT NULL,
    libelle VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    PRIMARY KEY (id)
);