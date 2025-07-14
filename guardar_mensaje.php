<?php
session_start();
require 'dbconexion.php';  // Aquí tu conexión PDO

// Verificar que haya sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Validar que el formulario haya sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos
    $nombre = $_POST['nombre'] ?? '';
    $mensaje = $_POST['mensaje'] ?? '';

    // Validar datos (mínimo básico)
    if (empty($nombre) || empty($mensaje)) {
        // Puedes manejar el error o redirigir con error
        header('Location: enviar_mensaje.php?error=1');
        exit;
    }

    try {
        // Preparar la consulta
        $sql = "INSERT INTO mensajes (nombre, mensaje) VALUES (:nombre, :mensaje)";
        $stmt = $cnnPDO->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':mensaje', $mensaje);
        $stmt->execute();

        // Redirigir con éxito
        header('Location: error.php?exito=1');
        exit;

    } catch (PDOException $e) {
        // Manejo de error
        echo "Error al guardar el mensaje: " . $e->getMessage();
        exit;
    }
} else {
    // No es método POST, redirigir
    header('Location: enviar_mensaje.php');
    exit;
}
?>
