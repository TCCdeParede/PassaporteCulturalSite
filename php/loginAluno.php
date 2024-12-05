<?php
include "conexao.php";

ob_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        echo json_encode(["status" => "error", "message" => "Campos obrigatórios não preenchidos"]);
        exit;
    }

    $stmt = $conexao->prepare("SELECT * FROM alunos WHERE emailalu = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifica a senha com password_verify
        if (password_verify($senha, $user['alusenha'])) {
            echo json_encode([
                "status" => "success",
                "message" => "Login bem-sucedido",
                "nome" => $user['nomealu'],
                "turma" => $user['nometur'],
                "pontMesGeral" => $user['pontmesGeralAluno'],
                "pontAnoGeral" => $user['pontanoGeralAluno'],
                "pontMesComputado" => $user['pontcompmesAluno'],
                "pontAnoComputado" => $user['pontcompanoAluno'],
                "rm" => $user['rmalu'],
                "foto" => $user['fotoalu']
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Credenciais inválidas"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Credenciais inválidas"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Método inválido"]);
}
$conexao->close();

ob_end_flush();
