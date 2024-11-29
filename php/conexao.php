<?php
// Definindo as configurações de conexão
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "passaporte_cultural";

$conexao = new mysqli($servername, $username, $password, $dbname);

if ($conexao->connect_error) {
    die("Conexão falhou: " . $conexao->connect_error);
}

$conexao->set_charset("utf8");

?>
