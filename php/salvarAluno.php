<?php
include "conexao.php";

$response = ["success" => false, "message" => "Erro desconhecido"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rmalu = $_POST['rmalu'] ?? '';
    $nomealu = $_POST['nomealu'] ?? '';
    $emailalu = $_POST['emailalu'] ?? '';
    $nometur = $_POST['nometur'] ?? '';
    $editId = $_POST['editId'] ?? '';

    if (!empty($editId)) {
        $sql = "UPDATE alunos SET rmalu='$rmalu', nomealu='$nomealu', emailalu='$emailalu', nometur='$nometur' WHERE rmalu='$editId'";
        $response['message'] = "Aluno atualizado com sucesso!";
    } else {
        $sql = "INSERT INTO alunos (rmalu, nomealu, emailalu, nometur) VALUES ('$rmalu', '$nomealu', '$emailalu', '$nometur')";
        $response['message'] = "Aluno adicionado com sucesso!";
    }

    if ($conexao->query($sql)) {
        $response['success'] = true;
    } else {
        $response['message'] = "Erro no banco de dados: " . $conexao->error;
    }
}

echo json_encode($response);
