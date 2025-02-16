<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controller/Controller.php';

if (isset($_GET['group_id']) && isset($_GET['user_id'])) {
    $groupId = $_GET['group_id'];
    $userId = $_GET['user_id'];

    $controller = new Controller();
    $controller->restoreMember($groupId, $userId);

    header('Location: view/group_discussion.php?group_id=' . $groupId);
    exit;
}

?>
