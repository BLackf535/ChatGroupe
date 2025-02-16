<?php

class Model {
    private $db;

    public function __construct() {
        $this->connectDB();
    }

    private function connectDB() {
        // Configuration de la connexion à la base de données
        $host = 'localhost';
        $dbname = 'news_group';
        $username = 'black';
        $password = 'black';

        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connexion échouée : ' . $e->getMessage();
        }
    }

    // Méthodes pour les opérations CRUD

    // Méthodes pour les utilisateurs
    public function createUser($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        if (!$stmt->execute()) {
            echo "Erreur lors de l'insertion : " . implode(" ", $stmt->errorInfo());
            return false;
        }
        return true;
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Méthodes pour les groupes
    public function createGroup($groupName, $creatorId) {
        $stmt = $this->db->prepare("INSERT INTO groups (name, creator_id) VALUES (:name, :creator_id)");
        $stmt->bindParam(':name', $groupName);
        $stmt->bindParam(':creator_id', $creatorId);
        return $stmt->execute();
    }

    public function addUserToGroup($groupId, $userId) {
        $stmt = $this->db->prepare("INSERT INTO group_users (group_id, user_id) VALUES (:group_id, :user_id)");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    // Méthodes pour les news
    public function createNews($title, $content, $authorId, $groupId) {
        $stmt = $this->db->prepare("INSERT INTO news (title, content, author_id, group_id) VALUES (:title, :content, :author_id, :group_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':author_id', $authorId);
        $stmt->bindParam(':group_id', $groupId);
        return $stmt->execute();
    }

    public function getNewsByGroup($groupId) {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE group_id = :group_id ORDER BY created_at DESC");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDB() {
        return $this->db;
    }

}

?>
