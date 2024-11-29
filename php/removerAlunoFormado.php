<?php
require_once 'conexao.php';

// Período de retenção (em meses)
$periodoRetencao = 6;

// Excluir alunos inativos com mais de 6 meses de formatura
$sqlExcluirInativos = "
    DELETE FROM alunos 
    WHERE status = 'inativo' AND data_formatura <= DATE_SUB(CURDATE(), INTERVAL ? MONTH)";
$stmtExcluirInativos = $conexao->prepare($sqlExcluirInativos);
$stmtExcluirInativos->bind_param("i", $periodoRetencao);

try {
    $stmtExcluirInativos->execute();
    echo "Alunos inativos excluídos com sucesso!";
} catch (Exception $e) {
    echo "Erro ao excluir alunos inativos: " . $e->getMessage();
}
