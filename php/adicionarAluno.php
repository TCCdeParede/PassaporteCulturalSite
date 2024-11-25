<?php

include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rmalu = $_POST['rmalu'];
    $nomealu = $_POST['nomealu'];
    $emailalu = $_POST['emailalu'];
    $nometur = $_POST['nometur'];

    // Cria a senha do aluno
    $nomeSemEspaco = str_replace(' ', '', $nomealu);
    $alusenha = strtolower($nomeSemEspaco) . $rmalu;

    $fotoalu = 0;

    $queryAluno = "INSERT INTO alunos (rmalu, nomealu, emailalu, fotoalu, alusenha, nometur) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmtAluno = $conexao->prepare($queryAluno)) {
        $stmtAluno->bind_param('ississ', $rmalu, $nomealu, $emailalu, $fotoalu, $alusenha, $nometur);
        if ($stmtAluno->execute()) {
            $queryTurma = "UPDATE turma SET qtdalu = qtdalu + 1 WHERE nometur = ?";

            if ($stmtTurma = $conexao->prepare($queryTurma)) {
                $stmtTurma->bind_param('s', $nometur);
                if ($stmtTurma->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Aluno adicionado com sucesso e quantidade de alunos na turma atualizada!'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao atualizar quantidade de alunos na turma'
                    ]);
                }
                $stmtTurma->close();
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro na preparação da consulta de atualização da turma'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao adicionar aluno'
            ]);
        }
        $stmtAluno->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro na preparação da query de inserção do aluno'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método inválido!'
    ]);
}
