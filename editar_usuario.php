<?php
session_start();
require 'config.php';

$id = $_POST['id'] ?? 0;
$sql = "SELECT * FROM usuarios WHERE id = $id";
$res = $conn->query($sql);
$user = $res->fetch_assoc();
?>

<form method="POST" action="actualizar_usuario.php">
  <input type="hidden" name="id" value="<?= $user['id'] ?>">
  Nombre: <input name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>"><br>
  Email: <input name="email" value="<?= htmlspecialchars($user['email']) ?>"><br>
  Tipo: 
  <select name="tipo_usuario">
    <option value="admin" <?= $user['tipo_usuario'] === 'admin' ? 'selected' : '' ?>>Admin</option>
    <option value="estudiante" <?= $user['tipo_usuario'] === 'estudiante' ? 'selected' : '' ?>>Estudiante</option>
  </select><br>
  <button type="submit">Actualizar</button>
</form>
