<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'controller/Controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $controller = new Controller();
    $user = $controller->loginUser($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        // Rediriger vers la page de discussion
        header('Location: discussion.php');
        exit;
    } else {
        echo "Échec de la connexion. Veuillez vérifier vos identifiants.";
    }
}

?>
