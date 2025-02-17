<?php

require_once __DIR__ . '/../model/Model.php';

class Controller {
    private $model;

    public function __construct() {
        $this->model = new Model();
    }

    public function handleRequest() {
        // Logique pour gérer les différentes requêtes
    }

    public function registerUser($username, $email, $password) {
        // Vérifier si l'email existe déjà
        if ($this->model->getUserByEmail($email)) {
            echo "Erreur : L'adresse email est déjà utilisée.";
            return false;
        }

        $result = $this->model->createUser($username, $email, $password);
        if ($result) {
            echo "Inscription réussie pour l'utilisateur : $username";
        } else {
            echo "Erreur lors de l'inscription de l'utilisateur : $username";
        }
        return $result;
    }

    public function loginUser($email, $password) {
        $user = $this->model->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            echo "Connexion réussie pour l'utilisateur : " . $user['username'];
            return $user;
        }
        echo "Échec de la connexion pour l'email : $email";
        return false;
    }

    public function createGroup($groupName, $creatorId) {
        $stmt = $this->model->getDB()->prepare("INSERT INTO `groups` (name, creator_id) VALUES (:name, :creator_id)");
        $stmt->bindParam(':name', $groupName);
        $stmt->bindParam(':creator_id', $creatorId);
        if ($stmt->execute()) {
            // Récupérer l'ID du groupe nouvellement créé
            $groupId = $this->model->getDB()->lastInsertId();
            // Ajouter le créateur comme membre du groupe
            return $this->addMemberToGroup($groupId, $creatorId);
        }
        return false;
    }

    public function addMemberToGroup($groupId, $userId) {
        $stmt = $this->model->getDB()->prepare("INSERT INTO group_users (group_id, user_id) VALUES (:group_id, :user_id)");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function createNews($title, $content, $authorId, $groupId) {
        return $this->model->createNews($title, $content, $authorId, $groupId);
    }

    public function getNewsByGroup($groupId) {
        $stmt = $this->model->getDB()->prepare("SELECT n.id, n.content, n.created_at, n.author_id as user_id, u.username FROM news n JOIN users u ON n.author_id = u.id WHERE n.group_id = :group_id ORDER BY n.created_at DESC");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMessagesByGroup($groupId) {
        $stmt = $this->model->getDB()->prepare("SELECT * FROM news WHERE group_id = :group_id ORDER BY created_at DESC");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserGroups($userId) {
        $stmt = $this->model->getDB()->prepare("SELECT g.id, g.name FROM `groups` g JOIN group_users gu ON g.id = gu.group_id WHERE gu.user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listUsers() {
        $stmt = $this->model->getDB()->prepare("SELECT id, username FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupDetails($groupId) {
        error_log("Fetching group details for group ID: " . $groupId);
        $stmt = $this->model->getDB()->prepare("SELECT g.name, g.creator_id, u.username as creator FROM `groups` g JOIN users u ON g.creator_id = u.id WHERE g.id = :group_id");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Group details result: " . print_r($result, true));
        return $result;
    }

    public function getGroupMembers($groupId) {
        $stmt = $this->model->getDB()->prepare("SELECT u.username, gu.status, u.id FROM group_users gu JOIN users u ON gu.user_id = u.id WHERE gu.group_id = :group_id");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function suspendMember($groupId, $userId) {
        $stmt = $this->model->getDB()->prepare("UPDATE group_users SET status = 'suspended' WHERE group_id = :group_id AND user_id = :user_id");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function restoreMember($groupId, $userId) {
        // Restore user status
        $stmt = $this->model->getDB()->prepare("UPDATE group_users SET status = 'active' WHERE group_id = :group_id AND user_id = :user_id");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        // Load XML file
        $xml = simplexml_load_file(__DIR__ . '/../logs/message_changes.xml');

        // Restore deleted messages for the active group
        foreach ($xml->change as $change) {
            if ((string)$change['action'] === 'delete' && (int)$change['group_id'] === $groupId) {
                $messageContent = (string)$change->content;
                $messageId = (int)$change['id'];
                $title = "Restored Message"; // Default title

                // Insert message back into the news table
                $stmt = $this->model->getDB()->prepare("INSERT INTO news (id, content, group_id, author_id, title) VALUES (:id, :content, :group_id, :author_id, :title)");
                $stmt->bindParam(':id', $messageId);
                $stmt->bindParam(':content', $messageContent);
                $stmt->bindParam(':group_id', $groupId);
                $stmt->bindParam(':author_id', $userId);
                $stmt->bindParam(':title', $title);
                $stmt->execute();
            }
        }

        return true;
    }

    public function editMessage($messageId, $newContent, $groupId, $title = 'Untitled', $authorId = 0) {
        // Check if the message exists
        $stmt = $this->model->getDB()->prepare("SELECT COUNT(*) FROM news WHERE id = :message_id");
        $stmt->bindParam(':message_id', $messageId);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Update existing message
            $stmt = $this->model->getDB()->prepare("UPDATE news SET content = :content WHERE id = :message_id");
        } else {
            // Insert new message with default title, author_id and group_id
            $stmt = $this->model->getDB()->prepare("INSERT INTO news (id, content, title, author_id, group_id) VALUES (:message_id, :content, :title, :author_id, :group_id)");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':author_id', $authorId);
            $stmt->bindParam(':group_id', $groupId);
        }
        $stmt->bindParam(':content', $newContent);
        $stmt->bindParam(':message_id', $messageId);
        $result = $stmt->execute();
        // Log the result of the database operation
        error_log("Message ID $messageId insertion result: " . ($result ? 'Success' : 'Failure'));
        return $result;
    }

    public function deleteMessage($messageId) {
        $stmt = $this->model->getDB()->prepare("DELETE FROM news WHERE id = :message_id");
        $stmt->bindParam(':message_id', $messageId);
        return $stmt->execute();
    }

    public function getMessageById($messageId) {
        $stmt = $this->model->getDB()->prepare("SELECT id, content FROM news WHERE id = :message_id");
        $stmt->bindParam(':message_id', $messageId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserStatusInGroup($userId, $groupId) {
        $stmt = $this->model->getDB()->prepare("SELECT status FROM group_users WHERE user_id = :user_id AND group_id = :group_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['status'] : null;
    }

    public function sendMessage($userId, $groupId, $content) {
        $title = "Message"; // Default title
        $stmt = $this->model->getDB()->prepare("INSERT INTO news (author_id, group_id, content, title) VALUES (:author_id, :group_id, :content, :title)");
        $stmt->bindParam(':author_id', $userId);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':title', $title);
        return $stmt->execute();
    }
}

?>
