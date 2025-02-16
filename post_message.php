<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controller/Controller.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupId = $_POST['group_id'] ?? 0;
    $message = $_POST['message'] ?? '';
    $userId = $_SESSION['user_id'] ?? 0;

    $controller = new Controller();
    $result = $controller->createNews('Message', $message, $userId, $groupId);

    if ($result) {
        header('Location: view/group_discussion.php?group_id=' . $groupId);
        exit;
    } else {
        echo "Erreur lors de l'envoi du message.";
    }
}

?>
