<?php
// Affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'controller/Controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $controller = new Controller();
    $result = $controller->registerUser($username, $email, $password);

    if ($result) {
        header('Location: view/login.html');
        exit;
    } else {
        echo "Erreur lors de l'inscription.";
    }
}

?>
