<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../controller/Controller.php';
session_start();

$messageId = $_GET['message_id'];
$controller = new Controller();
$message = $controller->getMessageById($messageId);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Message</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="../discussion.php"><img src="../logo.png" alt="Logo" style="height: 30px;"></a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../discussion.php">Accueil</a>
                </li>
            </ul>
            <span class="navbar-text mr-3">
                Connecté en tant que: <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-danger">Déconnexion</a>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Modifier le Message</h2>
        <form action="../edit_message.php" method="post">
            <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
            <input type="hidden" name="group_id" value="<?= $_GET['group_id'] ?>">
            <div class="form-group">
                <textarea class="form-control" name="content" rows="3"><?= htmlspecialchars($message['content']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
