<?php
// Affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controller/Controller.php';

session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: view/login.html');
    exit;
}

$controller = new Controller();
$userGroups = $controller->getUserGroups($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussions et Groupes</title>
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
                <?php if (isset($_SESSION['username'])): ?>
                    Connecté en tant que: <?= htmlspecialchars($_SESSION['username']) ?>
                <?php else: ?>
                    Utilisateur non connecté
                <?php endif; ?>
            </span>
            <a href="logout.php" class="btn btn-outline-danger">Déconnexion</a>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Vos Groupes</h2>
        <ul class="list-group">
            <?php if (!empty($userGroups)): ?>
                <?php foreach ($userGroups as $group): ?>
                    <li class="list-group-item">
                        <a href="view/group_discussion.php?group_id=<?= $group['id'] ?>">Groupe: <?= htmlspecialchars($group['name']) ?></a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item">Vous n'êtes inscrit à aucun groupe.</li>
            <?php endif; ?>
        </ul>
        <a href="view/add_group.html" class="btn btn-primary mt-3">Ajouter un Groupe</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
