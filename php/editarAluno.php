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
        // 1. Obter a turma atual do aluno antes de atualizar os dados
        $queryGetTurmaAntiga = "SELECT nometur FROM alunos WHERE rmalu = ?";
        $stmtGetTurmaAntiga = $conexao->prepare($queryGetTurmaAntiga);
        $stmtGetTurmaAntiga->bind_param('i', $rmalu);
        $stmtGetTurmaAntiga->execute();
        $stmtGetTurmaAntiga->bind_result($turmaAntiga);
        $stmtGetTurmaAntiga->fetch();
        $stmtGetTurmaAntiga->close();

        // Verificar se houve mudança de turma
        $turmaAlterada = ($turmaAntiga !== $nometur);

        // Iniciar transação
        $conexao->begin_transaction();

        // 2. Atualizar os dados do aluno
        $sql = "UPDATE alunos SET nomealu = ?, emailalu = ?, nometur = ? WHERE rmalu = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('sssi', $nomealu, $emailalu, $nometur, $rmalu);

        if (!$stmt->execute()) {
            throw new Exception('Erro ao atualizar os dados do aluno');
        }

        // 3. Se houve mudança de turma, ajustar qtdalu nas turmas antigas e novas
        if ($turmaAlterada) {
            // Subtrair 1 da turma antiga
            $querySubtrair = "UPDATE turma SET qtdalu = qtdalu - 1 WHERE nometur = ?";
            $stmtSubtrair = $conexao->prepare($querySubtrair);
            $stmtSubtrair->bind_param('s', $turmaAntiga);
            if (!$stmtSubtrair->execute()) {
                throw new Exception('Erro ao subtrair 1 da qtdalu da turma antiga');
            }

            // Adicionar 1 na nova turma
            $queryAdicionar = "UPDATE turma SET qtdalu = qtdalu + 1 WHERE nometur = ?";
            $stmtAdicionar = $conexao->prepare($queryAdicionar);
            $stmtAdicionar->bind_param('s', $nometur);
            if (!$stmtAdicionar->execute()) {
                throw new Exception('Erro ao adicionar 1 na qtdalu da nova turma');
            }
        }

        // 4. Commit da transação
        $conexao->commit();

        // Retorno de sucesso
        echo json_encode(['success' => true, 'message' => 'Dados do aluno atualizados com sucesso']);
    } catch (Exception $e) {
        // Se algo deu errado, faz rollback e exibe o erro
        $conexao->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    } finally {
        // Fecha os statements
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($stmtSubtrair)) {
            $stmtSubtrair->close();
        }
        if (isset($stmtAdicionar)) {
            $stmtAdicionar->close();
        }
        // Fecha a conexão
        $conexao->close();
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método de requisição inválido.',
    ]);
}
