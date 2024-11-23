<?php
session_start();
include 'conexao.php';

// Receber dados do formulário
$rm = trim($_POST['rm'] ?? '');
$senha = trim($_POST['senha'] ?? '');
$tipoLogin = $_POST['tipoLogin'] ?? '';

// Verificar se os campos foram preenchidos
if (empty($rm) || empty($senha)) {
    $_SESSION['errorRm'] = 'Preencha todos os campos.';
    header("Location: login.php");
    exit();
}

// Verificação para login como administrador
if ($tipoLogin === 'administrador') {
    // O admid pode ser um valor genérico de 5 dígitos
    $sql = "SELECT admid, admsenha FROM admin WHERE admid = ?";
    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        $_SESSION['errorRm'] = 'Erro no sistema: ' . $conexao->error;
        header("Location: login.php");
        exit();
    }

    $stmt->bind_param("s", $rm);  
    $stmt->execute();
    $stmt->bind_result($admid, $admsenha);

    if ($stmt->fetch()) {
        if ($senha === $admsenha) {
            // Senha correta
            $_SESSION['admid'] = $admid;
            $_SESSION['admin'] = true;

            $stmt->close();
            $conexao->close();

            header("Location: ../dashboardAdmin.php");
            exit();
        } else {
            // Senha inválida
            $_SESSION['errorSenha'] = 'Senha inválida.';
        }
    } else {
        $_SESSION['errorRm'] = 'Login de administrador não encontrado.';
    }

    $stmt->close();
    $conexao->close();
} else {
    // Verificação de login de professor
    if (!preg_match('/^\d{5}$/', $rm)) {
        $_SESSION['errorRm'] = 'O RM deve conter exatamente 5 dígitos.';
        header("Location: login.php");
        exit();
    }

    if (strlen($senha) < 8) {
        $_SESSION['errorSenha'] = 'A senha deve ter pelo menos 8 caracteres.';
        header("Location: login.php");
        exit();
    }

    $sql = "SELECT rmprof, profsenha, nomeprof FROM professor WHERE rmprof = ?";
    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        $_SESSION['errorRm'] = 'Erro no sistema: ' . $conexao->error;
        header("Location: login.php");
        exit();
    }

    $stmt->bind_param("i", $rm);
    $stmt->execute();
    $stmt->bind_result($rmprof, $profsenha, $nomeprof);

    if ($stmt->fetch()) {
        // Verificar a senha criptografada
        if (password_verify($senha, $profsenha)) {
            $_SESSION['rmprof'] = $rmprof;
            $_SESSION['nomeprof'] = $nomeprof;

            $stmt->close();
            $conexao->close();

            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['errorSenha'] = 'Senha inválida.';
        }
    } else {
        $_SESSION['errorRm'] = 'RM não encontrado.';
    }

    $stmt->close();
    $conexao->close();
}

header("Location: login.php");
exit();
