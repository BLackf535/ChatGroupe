<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controller/Controller.php';
session_start();

if (isset($_GET['message_id'])) {
    $messageId = $_GET['message_id'];

    $controller = new Controller();
    $content = getMessageContentFromXML($messageId);

    if ($content !== null) {
        $title = 'Untitled';  // Default title
        $authorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;  // Use logged-in user's ID
        $groupId = $_GET['group_id'];
        $controller->editMessage($messageId, $content, $groupId, $title, $authorId);
    }

    header('Location: view/group_discussion.php?group_id=' . $_GET['group_id']);
    exit;
}

function getMessageContentFromXML($messageId) {
    $filePath = 'logs/message_changes.xml';

    if (file_exists($filePath)) {
        $xml = simplexml_load_file($filePath);

        foreach ($xml->change as $change) {
            if ((string)$change['id'] === $messageId && ((string)$change['action'] === 'edit' || (string)$change['action'] === 'delete')) {
                $content = (string)$change['original_content'];
                // Log the content retrieved
                error_log("Retrieved content for message ID $messageId: $content");
                return $content;
            }
        }
    }
    return null;
}

?>
