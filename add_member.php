<?php

require_once 'controller/Controller.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupId = $_POST['group_id'] ?? 0;
    $userId = $_POST['user_id'] ?? 0;

    $controller = new Controller();
    $result = $controller->addMemberToGroup($groupId, $userId);

    if ($result) {
        header('Location: discussion.php');
        exit;
    } else {
        echo "Erreur lors de l'ajout du membre au groupe.";
    }
}

?>
