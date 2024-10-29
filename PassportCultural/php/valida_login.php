<?php
session_start();
// Conexão com o banco de dados
include 'conexao.php';

// Receber dados do formulário
$rm = $_POST['rm'] ?? '';
$senha = $_POST['senha'] ?? '';

// Preparar a consulta
$sql = "SELECT rmprof, profsenha FROM professor WHERE rmprof = ?";
$stmt = $conexao->prepare($sql);

if ($stmt) {
    // Bind dos parâmetros
    $stmt->bind_param("i", $rm);
    
    // Executar a consulta
    $stmt->execute();
    
    // Obter o resultado
    $stmt->bind_result($rmprof, $profsenha);
    $stmt->fetch();
    
    // Verificar se a consulta retornou algum resultado
    if ($rmprof) {
        // Verificar se a senha está correta
        if (password_verify($senha, $profsenha)) {
            // Caso as senhas no banco estejam criptografadas
            $_SESSION['rmprof'] = $rmprof;
            header("Location: classes.php");
            exit();
        } elseif ($senha === $profsenha) {
            // Caso as senhas não estejam criptografadas (comparação simples)
            $_SESSION['rmprof'] = $rmprof;
            header("Location: ../index.php");
            exit();
        } else {
            echo "RM ou senha incorretos!";
        }
    } else {
        echo "RM não encontrado!";
    }
    
    // Fechar a declaração
    $stmt->close();
} else {
    echo "Erro na preparação da consulta: " . $conexao->error;
}

$conexao->close();
?>
