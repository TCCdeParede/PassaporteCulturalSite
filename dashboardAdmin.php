<?php
session_start();

// Verifica se o administrador está logado
if (!isset($_SESSION['admid'])) {
  header("Location: php/login.php");
  exit();
}

$admid = isset($_SESSION['admid']) ? $_SESSION['admid'] : 'Administrador';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Geral</title>
</head>
<body>
    <p>teste</p>
    <a href="./php/logout.php">Logout</a>
</body>
</html>