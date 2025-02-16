<?php

require_once 'controller/Controller.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupName = $_POST['group_name'] ?? '';
    $creatorId = $_SESSION['user_id'] ?? 0;

    $controller = new Controller();
    $result = $controller->createGroup($groupName, $creatorId);

    if ($result) {
        header('Location: discussion.php');
        exit;
    } else {
        echo "Erreur lors de la création du groupe.";
    }
}

?>
