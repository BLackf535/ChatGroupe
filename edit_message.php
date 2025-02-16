<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controller/Controller.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageId = $_POST['message_id'];
    $newContent = $_POST['content'];
    $title = $_POST['title'] ?? 'Untitled';
    $authorId = $_SESSION['user_id'];
    $groupId = $_POST['group_id'];

    $controller = new Controller();
    $originalContent = $controller->getMessageById($messageId)['content'];
    $controller->editMessage($messageId, $newContent, $title, $authorId, $groupId);

    // Log the modification in an XML file
    logMessageChange($messageId, 'edit', $originalContent, $newContent, $groupId);

    header('Location: view/group_discussion.php?group_id=' . $groupId);
    exit;
}

function logMessageChange($messageId, $action, $originalContent, $newContent, $groupId) {
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;

    $filePath = 'logs/message_changes.xml';

    if (file_exists($filePath)) {
        $xml->load($filePath);
        $root = $xml->documentElement;
    } else {
        $root = $xml->createElement('changes');
        $xml->appendChild($root);
    }

    $change = $xml->createElement('change');
    $change->setAttribute('id', $messageId);
    $change->setAttribute('action', $action);
    $change->setAttribute('timestamp', date('Y-m-d H:i:s'));
    $change->setAttribute('group_id', $groupId);

    // Ajouter le contenu avant modification
    $change->setAttribute('original_content', htmlspecialchars($originalContent));

    $contentElement = $xml->createElement('content', htmlspecialchars($newContent));
    $change->appendChild($contentElement);

    $root->appendChild($change);

    $xml->save($filePath);
}

?>
