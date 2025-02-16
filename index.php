<?php
// Entry point for the application

require_once 'controller/Controller.php';

$controller = new Controller();
$controller->handleRequest();
