<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controller/Controller.php';
session_start();

if (isset($_GET['message_id']) && isset($_GET['group_id'])) {
    $messageId = $_GET['message_id'];
    $groupId = $_GET['group_id'];

    $controller = new Controller();
    $originalContent = $controller->getMessageById($messageId)['content'];
    $controller->deleteMessage($messageId);

    // Log the deletion in an XML file
    logMessageChange($messageId, 'delete', $originalContent, $groupId);

    header('Location: view/group_discussion.php?group_id=' . $groupId);
    exit;
}

function logMessageChange($messageId, $action, $content, $groupId) {
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

    // Ajouter le contenu avant suppression
    $change->setAttribute('original_content', htmlspecialchars($content));

    $contentElement = $xml->createElement('content', htmlspecialchars(''));
    $change->appendChild($contentElement);

    $root->appendChild($change);

    $xml->save($filePath);
}

?>
