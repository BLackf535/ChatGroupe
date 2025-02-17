<?php
session_start();

// Affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controller/Controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $_SESSION['old_username'] = $username;
    $_SESSION['old_email'] = $email;

    // Vérification des champs
    if (empty($username)) {
        $_SESSION['username_error'] = "Le nom d'utilisateur est requis.";
    }

    if (empty($email)) {
        $_SESSION['email_error'] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['email_error'] = "L'email n'est pas valide.";
    }

    if (empty($password)) {
        $_SESSION['password_error'] = "Le mot de passe est requis.";
    } elseif (strlen($password) < 6) {
        $_SESSION['password_error'] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // Redirection si des erreurs sont détectées
    if (!empty($_SESSION['username_error']) || !empty($_SESSION['email_error']) || !empty($_SESSION['password_error'])) {
        header('Location: view/register.php');
        exit;
    }

    $controller = new Controller();
    $result = $controller->registerUser($username, $email, $password);

    if ($result) {
        $_SESSION['success'] = "Inscription réussie.";
        // Supprimer les valeurs de session après une inscription réussie
        unset($_SESSION['old_username'], $_SESSION['old_email']);
        header('Location: view/login.php');
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de l'inscription.";
        header('Location: view/register.php');
        exit;
    }
}
?>
