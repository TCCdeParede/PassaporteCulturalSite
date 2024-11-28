<?php
include "conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rmalu'], $_POST['nomealu'], $_POST['emailalu'], $_POST['alusenha'], $_POST['nometur']) && isset($_FILES['fotoalu'])) {
        $rmalu = $_POST['rmalu'];
        $nomealu = $_POST['nomealu'];
        $emailalu = $_POST['emailalu'];
        $alusenha = $_POST['alusenha'];
        $nometur = $_POST['nometur'];

        $fotoalu = $_FILES['fotoalu'];
        $fotoNome = uniqid() . '.' . pathinfo($fotoalu['name'], PATHINFO_EXTENSION);
        $uploadDir = '../uploads/alunos/';
        $uploadPath = $uploadDir . $fotoNome;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Cria o diretório se não existir
        }

        if (move_uploaded_file($fotoalu['tmp_name'], $uploadPath)) {
            $relativePath = "/uploads/alunos/$fotoNome"; // Caminho relativo
            $sql = "INSERT INTO alunos (rmalu, nomealu, emailalu, fotoalu, alusenha, pontmesGeralAluno, pontanoGeralAluno, pontcompmesAluno, pontcompanoAluno, nometur)
                    VALUES (?, ?, ?, ?, ?, 0, 0, 0, 0, ?)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("isssss", $rmalu, $nomealu, $emailalu, $relativePath, $alusenha, $nometur);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Aluno cadastrado com sucesso!", "fotoPath" => $relativePath]);
            } else {
                echo json_encode(["error" => "Erro ao cadastrar aluno: " . $stmt->error]);
            }
        } else {
            echo json_encode(["error" => "Erro ao fazer upload da imagem"]);
        }
    } else {
        echo json_encode(["error" => "Faltam dados para o cadastro."]);
    }
} else {
    echo json_encode(["error" => "Método inválido, use POST."]);
}

$conexao->close();
?>