<?php
session_start();
require 'dbconexion.php';

if (isset($_SESSION['usuario'])) {
    $id = $_SESSION['usuario']['id'];
    $stmt = $cnnPDO->prepare("UPDATE usuarios SET session_token = NULL WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header('Location: login.php');
exit;
