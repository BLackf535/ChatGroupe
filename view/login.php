<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 500px;">
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <form action="../loginTraite.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control <?= isset($_SESSION['email_error']) ? 'is-invalid' : '' ?>">
            <?php if(isset($_SESSION['email_error'])): ?>
                <div class="invalid-feedback"><?= $_SESSION['email_error']; unset($_SESSION['email_error']); ?></div>
            <?php endif; ?>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control <?= isset($_SESSION['password_error']) ? 'is-invalid' : '' ?>">
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Connexion</button>
    </form>
    
    <div class="mt-3 text-center">
        <a href="register.php" class="text-decoration-none">Créer un compte</a>
    </div>
</div>
</body>
</html>
