<?php
session_start();
include 'conexao.php'; // Conexão com o banco de dados

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Receber dados do formulário
$rm = trim($_POST['rm'] ?? '');
$email = trim($_POST['email'] ?? '');
$nome = trim($_POST['nome'] ?? '');
$senha = trim($_POST['senha'] ?? '');

// Verificar se todos os campos foram preenchidos
if (empty($rm) || empty($email) || empty($nome) || empty($senha)) {
    echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos.']);
    exit();
}

// Validação do RM: deve conter exatamente 5 dígitos
if (!preg_match('/^\d{5}$/', $rm)) {
    echo json_encode(['status' => 'error', 'message' => 'O RM deve conter exatamente 5 dígitos.']);
    exit();
}

// Validação da senha: mínimo de 8 caracteres
if (strlen($senha) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'A senha deve ter pelo menos 8 caracteres.']);
    exit();
}

// Verificar se o RM ou e-mail já existem
$sql_check = "SELECT rmprof, emailprof FROM professor WHERE rmprof = ? OR emailprof = ?";
$stmt_check = $conexao->prepare($sql_check);
$stmt_check->bind_param("is", $rm, $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'RM ou e-mail já cadastrado.']);
    $stmt_check->close();
    $conexao->close();
    exit();
}
$stmt_check->close();

// Criptografar a senha
$senha_hashed = password_hash($senha, PASSWORD_BCRYPT);

// Inserir o novo professor
$sql_insert = "INSERT INTO professor (rmprof, nomeprof, emailprof, profsenha) VALUES (?, ?, ?, ?)";
$stmt_insert = $conexao->prepare($sql_insert);

if (!$stmt_insert) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao preparar consulta: ' . $conexao->error]);
    $conexao->close();
    exit();
}

$stmt_insert->bind_param("isss", $rm, $nome, $email, $senha_hashed);

if ($stmt_insert->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Professor cadastrado com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar professor: ' . $stmt_insert->error]);
}

$stmt_insert->close();
$conexao->close();
exit();
