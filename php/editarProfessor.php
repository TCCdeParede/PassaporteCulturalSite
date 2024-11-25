<?php

include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rmprof = $_POST['rmprof'];
    $nomeprof = $_POST['nomeprof'];
    $emailprof = $_POST['emailprof'];

    if (!$rmprof || !$nomeprof || !$emailprof) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
        exit;
    }

    $sql = "UPDATE professor SET nomeprof = '$nomeprof', emailprof = '$emailprof' WHERE rmprof = '$rmprof'";

    if (mysqli_query($conexao, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Dados atualizados com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar os dados']);
    }
}

mysqli_close($conexao);
