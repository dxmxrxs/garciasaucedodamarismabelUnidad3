<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $tipo = $_POST['tipo_usuario'];

    $sql = "UPDATE usuarios SET nombre=?, email=?, tipo_usuario=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $email, $tipo, $id);
    $stmt->execute();
}
header("Location: admin.php");
