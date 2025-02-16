<?php
require_once '../controller/Controller.php';
$controller = new Controller();
$users = $controller->listUsers();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Membre</title>
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
        <h2 class="text-center">Ajouter un Membre au Groupe</h2>
        <form action="../add_member.php" method="post">
            <input type="hidden" name="group_id" value="<?= htmlspecialchars($_GET['group_id']) ?>">
            <div class="form-group">
                <label for="user_id">Sélectionnez l'Utilisateur</label>
                <select class="form-control" name="user_id" id="user_id">
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Ajouter</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
