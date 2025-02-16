<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controller/Controller.php';
session_start();

function getLoggedChanges() {
    $filePath = 'logs/message_changes.xml';
    $changes = [];

    if (file_exists($filePath)) {
        $xml = simplexml_load_file($filePath);

        foreach ($xml->change as $change) {
            $changes[] = [
                'id' => (string)$change['id'],
                'action' => (string)$change['action'],
                'timestamp' => (string)$change['timestamp'],
                'group_id' => (string)$change['group_id'],
                'original_content' => (string)$change['original_content'],
                'content' => (string)$change->content
            ];
        }
    }

    return $changes;
}

$changes = getLoggedChanges();
$groupedChanges = [];

foreach ($changes as $change) {
    $groupedChanges[$change['group_id']][] = $change;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurer les Messages</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="discussion.php"><img src="logo.png" alt="Logo" style="height: 30px;"></a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="discussion.php">Accueil</a>
                </li>
            </ul>
            <span class="navbar-text mr-3">
                Connecté en tant que: <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-danger">Déconnexion</a>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Restaurer les Messages</h2>
        <?php foreach ($groupedChanges as $groupId => $changes): ?>
            <h4>Groupe ID: <?= htmlspecialchars($groupId) ?></h4>
            <ul class="list-group mb-3">
                <?php foreach ($changes as $change): ?>
                    <li class="list-group-item">
                        <strong>Action:</strong> <?= htmlspecialchars($change['action']) ?><br>
                        <strong>Timestamp:</strong> <?= htmlspecialchars($change['timestamp']) ?><br>
                        <strong>Original Content:</strong> <?= htmlspecialchars($change['original_content']) ?><br>
                        <strong>Current Content:</strong> <?= htmlspecialchars($change['content']) ?><br>
                        <a href="restore_message.php?message_id=<?= $change['id'] ?>&group_id=<?= $groupId ?>" class="btn btn-sm btn-success">Restaurer</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
