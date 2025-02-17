<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'controller/Controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email)) {
        $_SESSION['email_error'] = "L'email est requis.";
    }

    if (empty($password)) {
        $_SESSION['password_error'] = "Le mot de passe est requis.";
    }

    if (!empty($_SESSION['email_error']) || !empty($_SESSION['password_error'])) {
        header('Location: view/login.php');
        exit;
    }

    $controller = new Controller();
    $user = $controller->loginUser($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        // Rediriger vers la page de discussion
        header('Location: discussion.php');
        exit;
    } else {
        $_SESSION['error'] = "Échec de la connexion. Email ou mot de passe incorrect.";
        header('Location: view/login.php');
        exit;

    }
}

?>
