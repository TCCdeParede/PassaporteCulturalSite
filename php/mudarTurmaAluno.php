<?php
require_once 'conexao.php';

// Desativar o autocommit para iniciar a transação
$conexao->autocommit(false);

try {
    // Buscar todos os alunos ativos
    $sqlAlunos = "SELECT rmalu, nometur FROM alunos WHERE status = 'ativo'";
    $resultAlunos = $conexao->query($sqlAlunos);

    if ($resultAlunos->num_rows > 0) {
        while ($aluno = $resultAlunos->fetch_assoc()) {
            $rmalu = $aluno['rmalu'];
            $nometur = $aluno['nometur'];

            // Extrair o ano atual da turma (ex.: 1DSB -> 1)
            $anoAtual = (int) $nometur[0];

            if ($anoAtual === 3) {
                // Aluno terminou o 3º ano, marcar como inativo e registrar formatura
                $sqlInativarAluno = "UPDATE alunos 
                                     SET status = 'inativo', data_formatura = CURDATE() 
                                     WHERE rmalu = ?";
                $stmtInativarAluno = $conexao->prepare($sqlInativarAluno);
                $stmtInativarAluno->bind_param("i", $rmalu);
                $stmtInativarAluno->execute();

                // Atualizar a quantidade de alunos na turma
                $sqlAtualizarTurma = "UPDATE turma SET qtdalu = qtdalu - 1 WHERE nometur = ?";
                $stmtAtualizarTurma = $conexao->prepare($sqlAtualizarTurma);
                $stmtAtualizarTurma->bind_param("s", $nometur);
                $stmtAtualizarTurma->execute();
            } else {
                // Promover o aluno para o próximo ano
                $novoAno = $anoAtual + 1;
                $novaTurma = $novoAno . substr($nometur, 1); // Ex.: 1DSB -> 2DSB

                $sqlAtualizarAluno = "UPDATE alunos SET nometur = ? WHERE rmalu = ?";
                $stmtAtualizarAluno = $conexao->prepare($sqlAtualizarAluno);
                $stmtAtualizarAluno->bind_param("si", $novaTurma, $rmalu);
                $stmtAtualizarAluno->execute();

                // Atualizar a quantidade de alunos nas turmas antiga e nova
                $sqlAtualizarTurma = "
                    UPDATE turma 
                    SET qtdalu = CASE 
                        WHEN nometur = ? THEN qtdalu - 1 
                        WHEN nometur = ? THEN qtdalu + 1 
                    END
                    WHERE nometur IN (?, ?)";
                $stmtAtualizarTurma = $conexao->prepare($sqlAtualizarTurma);
                $stmtAtualizarTurma->bind_param("ssss", $nometur, $novaTurma, $nometur, $novaTurma);
                $stmtAtualizarTurma->execute();
            }
        }
    }

    // Commit da transação
    $conexao->commit();
    echo "Atualização de turmas realizada com sucesso!";
} catch (Exception $e) {
    // Rollback em caso de erro
    $conexao->rollback();
    echo "Erro ao atualizar turmas: " . $e->getMessage();
}
