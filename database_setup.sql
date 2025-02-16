-- Création de la base de données
CREATE DATABASE IF NOT EXISTS news_group CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE news_group;

-- Création de la table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table des groupes (ajout de backticks)
CREATE TABLE IF NOT EXISTS `groups` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    creator_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Création de la table des relations groupe-utilisateur
CREATE TABLE IF NOT EXISTS group_users (
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (group_id, user_id),
    FOREIGN KEY (group_id) REFERENCES `groups`(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Création de la table des news
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    group_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES `groups`(id) ON DELETE CASCADE
);

-- Insertion de données fictives
INSERT INTO users (username, email, password) VALUES
('Alice', 'alice@example.com', '$2y$10$eImiTXuWVxfM37uY4JANjQ=='),
('Bob', 'bob@example.com', '$2y$10$eImiTXuWVxfM37uY4JANjQ==');

INSERT INTO `groups` (name, creator_id) VALUES
('Groupe A', 1),
('Groupe B', 2);

INSERT INTO group_users (group_id, user_id) VALUES
(1, 1),
(1, 2),
(2, 1);

INSERT INTO news (title, content, author_id, group_id) VALUES
('News 1', 'Contenu de la news 1', 1, 1),
('News 2', 'Contenu de la news 2', 2, 2);
