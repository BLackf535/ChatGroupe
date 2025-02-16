<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'controller/Controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupId = $_POST['group_id'] ?? null;
    $messageContent = $_POST['message'] ?? '';

    if ($groupId && !empty($messageContent)) {
        $controller = new Controller();
        $userId = $_SESSION['user_id'];

        // Assurez-vous que l'utilisateur est actif avant d'envoyer le message
        $userStatus = $controller->getUserStatusInGroup($userId, $groupId);
        if ($userStatus === 'active') {
            $controller->sendMessage($userId, $groupId, $messageContent);
            header('Location: view/group_discussion.php?group_id=' . $groupId);
            exit;
        } else {
            echo "Vous êtes suspendu et ne pouvez pas envoyer de messages.";
        }
    } else {
        echo "Erreur : Le message ne peut pas être vide.";
    }
} else {
    echo "Méthode de requête invalide.";
}

?>
