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
        // Obter a turma atual do aluno antes de atualizar os dados
        $queryGetTurmaAntiga = "SELECT nometur FROM alunos WHERE rmalu = ?";
        $stmtGetTurmaAntiga = $conexao->prepare($queryGetTurmaAntiga);
        $stmtGetTurmaAntiga->bind_param('i', $rmalu);
        $stmtGetTurmaAntiga->execute();
        $stmtGetTurmaAntiga->bind_result($turmaAntiga);
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

        // Se houve mudança de turma, ajustar qtdalu nas turmas antigas e novas
        if ($turmaAlterada) {
            $querySubtrair = "UPDATE turma SET qtdalu = qtdalu - 1 WHERE nometur = ?";
            $stmtSubtrair = $conexao->prepare($querySubtrair);
            $stmtSubtrair->bind_param('s', $turmaAntiga);
            if (!$stmtSubtrair->execute()) {
                throw new Exception('Erro ao subtrair 1 da qtdalu da turma antiga');
            }

            $queryAdicionar = "UPDATE turma SET qtdalu = qtdalu + 1 WHERE nometur = ?";
            $stmtAdicionar = $conexao->prepare($queryAdicionar);
            $stmtAdicionar->bind_param('s', $nometur);
            if (!$stmtAdicionar->execute()) {
                throw new Exception('Erro ao adicionar 1 na qtdalu da nova turma');
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
        if (isset($stmtSubtrair)) {
            $stmtSubtrair->close();
        }
        if (isset($stmtAdicionar)) {
            $stmtAdicionar->close();
        }
        $conexao->close();
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método de requisição inválido.',
    ]);
}
