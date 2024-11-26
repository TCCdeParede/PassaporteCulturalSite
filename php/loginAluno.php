<?php
include "conexao.php";

// Ativar buffer para evitar saídas fora do JSON
ob_start();
header('Content-Type: application/json; charset=utf-8'); // Definir o tipo de conteúdo como JSON

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validar entrada para evitar SQL Injection
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        echo json_encode(["status" => "error", "message" => "Campos obrigatórios não preenchidos"]);
        exit;
    }

    // Preparar a consulta
    $stmt = $conexao->prepare("SELECT * FROM alunos WHERE emailalu = ? AND alusenha = ?");
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Buscar os dados do usuário
        echo json_encode([
            "status" => "success",
            "message" => "Login bem-sucedido",
            "nome" => $user['nomealu'], // Ajuste os nomes das colunas conforme seu banco
            "turma" => $user['nometur'],
            "pontos" => $user['pontanoGeral'],
            "rm" => $user['rmalu']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Credenciais inválidas"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Método inválido"]);
}
$conexao->close();

// Limpar buffer
ob_end_flush();
