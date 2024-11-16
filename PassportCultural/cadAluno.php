<?php
include "conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rmalu'], $_POST['nomealu'], $_POST['emailalu'], $_POST['alusenha'], $_POST['nometur'])) {
        $rmalu = $_POST['rmalu'];
        $nomealu = $_POST['nomealu'];
        $emailalu = $_POST['emailalu'];
        $alusenha = $_POST['alusenha'];
        $nometur = $_POST['nometur'];

        // Definindo valores no SQL
        $sql = "INSERT INTO alunos (rmalu, nomealu, emailalu, alusenha, pontmes, pontano, nometur)
                VALUES (?, ?, ?, ?, 0, 0, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("issss", $rmalu, $nomealu, $emailalu, $alusenha, $nometur);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Aluno cadastrado com sucesso!"]);
        } else {
            echo json_encode(["error" => "Erro ao cadastrar aluno: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Faltam dados para o cadastro."]);
    }
} else {
    echo json_encode(["error" => "Método inválido, use POST."]);
}

$conexao->close();
