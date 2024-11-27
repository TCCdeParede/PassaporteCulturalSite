<?php
include "conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rmalu'], $_POST['nomealu'], $_POST['emailalu'], $_POST['alusenha'], $_POST['nometur'], $_POST['fotoalu'])) {
        $rmalu = $_POST['rmalu'];
        $nomealu = $_POST['nomealu'];
        $emailalu = $_POST['emailalu'];
        $alusenha = $_POST['alusenha'];
        $nometur = $_POST['nometur'];
        $fotoalu = $_POST['fotoalu'];

        // Inicia uma transação para garantir integridade
        $conexao->begin_transaction();

        try {
            $sql = "INSERT INTO alunos (rmalu, nomealu, emailalu, fotoalu, alusenha, pontmesGeralAluno, pontanoGeralAluno, pontcompmesAluno, pontcompanoAluno, nometur)
                    VALUES (?, ?, ?, ?, ?, 0, 0, 0, 0, ?)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("isssss", $rmalu, $nomealu, $emailalu, $fotoalu, $alusenha, $nometur);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao cadastrar aluno: " . $stmt->error);
            }

            $updateSql = "UPDATE turma SET qtdalu = qtdalu + 1 WHERE nometur = ?";
            $updateStmt = $conexao->prepare($updateSql);
            $updateStmt->bind_param("s", $nometur);

            if (!$updateStmt->execute()) {
                throw new Exception("Erro ao atualizar turma: " . $updateStmt->error);
            }

            $conexao->commit();
            echo json_encode(["message" => "Aluno cadastrado com sucesso!"]);
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $conexao->rollback();
            echo json_encode(["error" => $e->getMessage()]);
        } finally {
            $stmt->close();
            if (isset($updateStmt)) {
                $updateStmt->close();
            }
        }
    } else {
        echo json_encode(["error" => "Faltam dados para o cadastro."]);
    }
} else {
    echo json_encode(["error" => "Método inválido, use POST."]);
}

$conexao->close();
