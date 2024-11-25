<?php

include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rmprof = $_POST['rmprof'];
    $nomeprof = $_POST['nomeprof'];
    $emailprof = $_POST['emailprof'];

    $nomeSemEspaco = str_replace(' ', '', $_POST['nomeprof']);
    $profsenha = strtolower($nomeSemEspaco) . $rmprof;

    $senha_hashed = password_hash($profsenha, PASSWORD_BCRYPT);

    $nvauto = 0;

    $query = "INSERT INTO professor (rmprof, nomeprof, emailprof, profsenha, nvauto) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conexao->prepare($query)) {
        $stmt->bind_param("isssi", $rmprof, $nomeprof, $emailprof, $senha_hashed, $nvauto);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Professor cadastrado com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar professor']);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro na preparação da consulta de cadastro do professor'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método inválido!'
    ]);
}
