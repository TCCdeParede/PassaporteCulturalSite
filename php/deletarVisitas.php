<?php
include 'conexao.php';

$intervalo = 60; // dias
$dataLimite = date('Y-m-d', strtotime("-$intervalo days"));

$conexao->begin_transaction();

try {
    $queryVisitasAntigas = "SELECT v.idfoto, vi.caminho_imagem FROM visita v
                            INNER JOIN visita_imagens vi ON v.idfoto = vi.idfoto
                            WHERE v.data < ?";
    $stmtVisitas = $conexao->prepare($queryVisitasAntigas);
    $stmtVisitas->bind_param('s', $dataLimite);
    $stmtVisitas->execute();
    $result = $stmtVisitas->get_result();

    while ($row = $result->fetch_assoc()) {
        $caminhoImagem = $row['caminho_imagem'];

        if (file_exists($caminhoImagem)) {
            unlink($caminhoImagem);
        }

        $queryExcluirImagem = "DELETE FROM visita_imagens WHERE idfoto = ?";
        $stmtExcluirImagem = $conexao->prepare($queryExcluirImagem);
        $stmtExcluirImagem->bind_param('i', $row['idfoto']);
        $stmtExcluirImagem->execute();
        $stmtExcluirImagem->close();
    }

    $queryExcluirVisitas = "DELETE FROM visita WHERE data < ?";
    $stmtExcluirVisitas = $conexao->prepare($queryExcluirVisitas);
    $stmtExcluirVisitas->bind_param('s', $dataLimite);
    $stmtExcluirVisitas->execute();
    $stmtExcluirVisitas->close();

    $conexao->commit();

    echo "Visitas e imagens excluídas com sucesso.";
} catch (Exception $e) {
    $conexao->rollback();
    echo "Erro: " . $e->getMessage();
} finally {
    $stmtVisitas->close();
    $conexao->close();
}
?>
