<?php
session_start();
require_once 'dbconexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    $stmt = $cnnPDO->prepare("SELECT * FROM usuarios WHERE correo = :correo");
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {

        $session_token = bin2hex(random_bytes(32));

        $update = $cnnPDO->prepare("UPDATE usuarios SET session_token = :token WHERE id = :id");
        $update->bindParam(':token', $session_token);
        $update->bindParam(':id', $usuario['id']);
        $update->execute();

        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'token' => $session_token 
        ];
        if (strtolower(trim($correo)) === 'admin@gmail.com') {
            echo "<script>alert('🎉 Bienvenido administrador {$usuario['nombre']}'); window.location.href='admin.php';</script>";
        } else {
            echo "<script>alert('🎉 Bienvenido {$usuario['nombre']}'); window.location.href='panel.php';</script>";
        }
    } else {
        echo "<script>alert('❌ Correo o contraseña incorrectos'); window.history.back();</script>";
    }
}
?>
