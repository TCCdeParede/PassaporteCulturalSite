<?php

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rmalu = $_POST['rmalu'];
    $nomealu = $_POST['nomealu'];
    $emailalu = $_POST['emailalu'];
    $nometur = $_POST['nometur'];

    if (!$rmalu || !$nomealu || !$emailalu || !$nometur) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
        exit;
    }

    try {
        $queryGetTurmaAntiga = "SELECT nometur, pontcompmesAluno, pontcompanoAluno, pontmesGeralAluno, pontanoGeralAluno FROM alunos WHERE rmalu = ?";
        $stmtGetTurmaAntiga = $conexao->prepare($queryGetTurmaAntiga);
        $stmtGetTurmaAntiga->bind_param('i', $rmalu);
        $stmtGetTurmaAntiga->execute();
        $stmtGetTurmaAntiga->bind_result($turmaAntiga, $pontcompmesAntiga, $pontcompanoAntiga, $pontmesGeralAntiga, $pontanoGeralAntiga);
        $stmtGetTurmaAntiga->fetch();
        $stmtGetTurmaAntiga->close();

        // Verificar se houve mudança de turma
        $turmaAlterada = ($turmaAntiga !== $nometur);

        $conexao->begin_transaction();
        $sql = "UPDATE alunos SET nomealu = ?, emailalu = ?, nometur = ? WHERE rmalu = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('sssi', $nomealu, $emailalu, $nometur, $rmalu);

        if (!$stmt->execute()) {
            throw new Exception('Erro ao atualizar os dados do aluno');
        }

        if ($turmaAlterada) {
            $querySubtrairPontos = "UPDATE turma SET pontcompmensalTurma = pontcompmensalTurma - ?, pontcompgeralTurma = pontcompgeralTurma - ?, pontmesGeralTurma = pontmesGeralTurma - ?, pontanualGeralTurma = pontanualGeralTurma - ? WHERE nometur = ?";
            $stmtSubtrairPontos = $conexao->prepare($querySubtrairPontos);
            $stmtSubtrairPontos->bind_param('iiiis', $pontcompmesAntiga, $pontcompanoAntiga, $pontmesGeralAntiga, $pontanoGeralAntiga, $turmaAntiga);
            if (!$stmtSubtrairPontos->execute()) {
                throw new Exception('Erro ao subtrair pontos da turma antiga');
            }

            $queryAdicionarPontos = "UPDATE turma SET pontcompmensalTurma = pontcompmensalTurma + ?, pontcompgeralTurma = pontcompgeralTurma + ?, pontmesGeralTurma = pontmesGeralTurma + ?, pontanualGeralTurma = pontanualGeralTurma + ? WHERE nometur = ?";
            $stmtAdicionarPontos = $conexao->prepare($queryAdicionarPontos);
            $stmtAdicionarPontos->bind_param('iiiis', $pontcompmesAntiga, $pontcompanoAntiga, $pontmesGeralAntiga, $pontanoGeralAntiga, $nometur);
            if (!$stmtAdicionarPontos->execute()) {
                throw new Exception('Erro ao adicionar pontos à nova turma');
            }
        }

        $conexao->commit();

        echo json_encode(['success' => true, 'message' => 'Dados do aluno atualizados com sucesso']);
    } catch (Exception $e) {
        $conexao->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($stmtSubtrairPontos)) {
            $stmtSubtrairPontos->close();
        }
        if (isset($stmtAdicionarPontos)) {
            $stmtAdicionarPontos->close();
        }
        $conexao->close();
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método de requisição inválido.',
    ]);
}
