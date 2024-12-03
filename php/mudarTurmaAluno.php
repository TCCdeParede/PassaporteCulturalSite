<?php
require_once 'conexao.php';

$conexao->autocommit(false);

try {
    $sqlAlunos = "SELECT rmalu, nometur FROM alunos WHERE status = 'ativo'";
    $resultAlunos = $conexao->query($sqlAlunos);

    if ($resultAlunos->num_rows > 0) {
        while ($aluno = $resultAlunos->fetch_assoc()) {
            $rmalu = $aluno['rmalu'];
            $nometur = $aluno['nometur'];

            $anoAtual = (int) $nometur[0];

            if ($anoAtual === 3) {
                $sqlInativarAluno = "UPDATE alunos 
                                     SET status = 'inativo', data_formatura = CURDATE() 
                                     WHERE rmalu = ?";
                $stmtInativarAluno = $conexao->prepare($sqlInativarAluno);
                $stmtInativarAluno->bind_param("i", $rmalu);
                $stmtInativarAluno->execute();

                $sqlAtualizarTurma = "UPDATE turma SET qtdalu = qtdalu - 1 WHERE nometur = ?";
                $stmtAtualizarTurma = $conexao->prepare($sqlAtualizarTurma);
                $stmtAtualizarTurma->bind_param("s", $nometur);
                $stmtAtualizarTurma->execute();
            } else {
                $novoAno = $anoAtual + 1;
                $novaTurma = $novoAno . substr($nometur, 1);

                $sqlAtualizarAluno = "UPDATE alunos SET nometur = ? WHERE rmalu = ?";
                $stmtAtualizarAluno = $conexao->prepare($sqlAtualizarAluno);
                $stmtAtualizarAluno->bind_param("si", $novaTurma, $rmalu);
                $stmtAtualizarAluno->execute();

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

    $conexao->commit();
    echo "Atualização de turmas realizada com sucesso!";
} catch (Exception $e) {
    $conexao->rollback();
    echo "Erro ao atualizar turmas: " . $e->getMessage();
}
