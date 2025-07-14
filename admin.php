<?php
session_start();
require 'dbconexion.php';
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
$id = $_SESSION['usuario']['id'];
$token = $_SESSION['usuario']['token'] ?? null; 

if (!$token) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$stmt = $cnnPDO->prepare("SELECT session_token FROM usuarios WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$row || $row['session_token'] !== $token) {
    session_destroy();
    echo "<script>
            alert('⚠️ Tu sesión ha sido cerrada porque se inició en otro dispositivo.');
            window.location.href='login.php';
          </script>";
    exit;
}

$sqlUsuarios = "SELECT id, nombre, correo FROM usuarios";
$stmtUsuarios = $cnnPDO->query($sqlUsuarios);
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

$sqlMensajes = "SELECT id, nombre, mensaje, fecha FROM mensajes";
$stmtMensajes = $cnnPDO->query($sqlMensajes);
$mensajes = $stmtMensajes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <style>
        body {
  padding-top: 100px;
  background: rgb(212, 223, 233);
  color: #142a31;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 100vh;
}
        h1, h2 {
            color: #142a31;
            text-align: center;
        }
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #142a31;
            color: white;
        }
        .btn-edit {
            background-color: #ffc107;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
       .nav-container {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
 
nav {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background-color: #142a31;
  padding: 1em 2em;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
  z-index: 1000;
  color: #75d3ce;
}

.panel-box h1 {
  font-size: 1.4rem;
}

.panel-box p {
  font-size: 0.9rem;
  color: #b2e3e0;
}

nav ul {
  display: flex;
  align-items: center;
  list-style: none;
  gap: 15px;
}

.nav-link {
  text-decoration: none;
  font-weight: bold;
  color: #75d3ce;
  padding: 10px 15px;
  border-radius: 12px;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.nav-link:hover {
  background-color: #4a949b;
  color: white;
}

.btn-cerrar {
  background: #ffffff;
  color: #142a31;
  padding: 10px 16px;
  border: 2px solid #75d3ce;
  border-radius: 25px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-cerrar:hover {
  background-color: #75d3ce;
  color: #142a31;
}
    </style>
</head>
<body>
<nav>
  <div class="nav-container">
    <div class="panel-box">
      <h3>👤 ¡Hola, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>!</h3>
      <h3>Bienvenido sea al panel de administrador👑</h3>
    </div>
    <ul>
      <li>
        <form action="cerrar_sesion.php" method="POST" style="margin: 0;">
          <button type="submit" class="btn-cerrar">Cerrar Sesión</button>
        </form>
      </li>
    </ul>
  </div>
</nav>


<h1>👑 Panel de Administración</h1>

<h2>👥 Lista de Usuarios</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?= $usuario['id'] ?></td>
            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
            <td><?= htmlspecialchars($usuario['correo']) ?></td>
            <td>
                <form action="eliminar_usuario.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                    <button class="btn-delete">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>📬 Mensajes Recibidos</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Mensaje</th>
        <th>Fecha</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($mensajes as $mensaje): ?>
        <tr>
            <td><?= $mensaje['id'] ?></td>
            <td><?= htmlspecialchars($mensaje['nombre']) ?></td>
            <td><?= htmlspecialchars($mensaje['mensaje']) ?></td>
            <td><?= $mensaje['fecha'] ?></td>
            <td>
                <form action="eliminar_mensaje.php" method="POST" onsubmit="return confirm('¿Eliminar este mensaje?');">
                    <input type="hidden" name="id" value="<?= $mensaje['id'] ?>">
                    <button class="btn-delete">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
