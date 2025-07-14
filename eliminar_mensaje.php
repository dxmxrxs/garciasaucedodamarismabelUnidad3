<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM mensajes WHERE id = $id";
    $conn->query($sql);
}
header("Location: admin.php");
