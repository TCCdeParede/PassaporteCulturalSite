<?php
// Conexão com o banco de dados
include 'conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber dados do formulário e validar
    $rmprof = $_POST['inputRM'] ?? '';
    $nomeprof = $_POST['inputNome'] ?? '';
    $emailprof = $_POST['inputEmail'] ?? '';
    $profsenha = $_POST['inputPassword'] ?? '';

    // Verificar se os campos estão preenchidos
    if (!empty($rmprof) && !empty($nomeprof) && !empty($emailprof) && !empty($profsenha)) {
        // Inserir dados na tabela de professores
        $sql = "INSERT INTO professor (rmprof, nomeprof, emailprof, profsenha, nvauto) VALUES (?, ?, ?, ?, 1)";

        // Preparar a declaração
        $stmt = $conexao->prepare($sql);

        if ($stmt) {
            // Bind dos parâmetros
            $stmt->bind_param("isss", $rmprof, $nomeprof, $emailprof, $profsenha);

            // Executar a declaração
            if ($stmt->execute()) {
                header("Location: cadastro.php?status=success&message=Cadastro%20realizado%20com%20sucesso!");
                exit();
            } else {
                header("Location: cadastro.php?status=error&message=" . urlencode("Erro: " . $stmt->error));
                exit();
            }

            // Fechar a declaração
            $stmt->close();
        } else {
            header("Location: cadastro.php?status=error&message=" . urlencode("Erro na preparação da consulta: " . $conexao->error));
            exit();
        }
    } else {
        header("Location: cadastro.php?status=error&message=" . urlencode("Por favor, preencha todos os campos."));
        exit();
    }
}

// Fechar a conexão
$conexao->close();
