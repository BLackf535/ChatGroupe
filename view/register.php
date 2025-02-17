<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 500px;">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <h2 class="text-center">Inscription</h2>
        <form action="../registerTraite.php" method="post">
            <div class="mb-3">
                <label class="form-label">Nom d'utilisateur</label>
                <input type="text" name="username" class="form-control <?= isset($_SESSION['username_error']) ? 'is-invalid' : '' ?>" value="<?= $_SESSION['old_username'] ?? '' ?>" id="username" placeholder="Entrez votre nom d'utilisateur">
                <?php if(isset($_SESSION['username_error'])): ?>
                    <div class="invalid-feedback"><?= $_SESSION['username_error']; unset($_SESSION['username_error']); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control <?= isset($_SESSION['email_error']) ? 'is-invalid' : '' ?>" value="<?= $_SESSION['old_email'] ?? '' ?>" id="email" placeholder="Entrez votre email">
                <?php if(isset($_SESSION['email_error'])): ?>
                    <div class="invalid-feedback"><?= $_SESSION['email_error']; unset($_SESSION['email_error']); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control <?= isset($_SESSION['password_error']) ? 'is-invalid' : '' ?>" id="password" placeholder="Mot de passe">
                <?php if(isset($_SESSION['password_error'])): ?>
                    <div class="invalid-feedback"><?= $_SESSION['password_error']; unset($_SESSION['password_error']); ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
        </form>
        
        <div class="mt-3 text-center">
            <a href="login.php" class="text-decoration-none">Déjà un compte ? Connectez-vous</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
