<?php
include "conexao.php";

// Ativar buffer para evitar saídas fora do JSON
ob_start();

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
        echo json_encode(["status" => "success", "message" => "Login bem-sucedido"]);
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
?>
