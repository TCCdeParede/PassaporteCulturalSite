<?php
// Definindo as configurações de conexão
$servername = "localhost"; // ou o nome do servidor, se não for local
$username = "root"; // seu nome de usuário do banco de dados
$password = "";   // sua senha do banco de dados
$dbname = "passaporte_cultural"; // nome do banco de dados que você criou

// Criar a conexão
$conexao = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conexao->connect_error) {
    die("Conexão falhou: " . $conexao->connect_error);
}

// Definir o charset para UTF-8 (opcional)
$conexao->set_charset("utf8");

?>
