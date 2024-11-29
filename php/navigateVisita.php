<?php
include "conexao.php";

header('Content-Type: application/json');

$direction = $_GET['direction'] ?? null;
$currentId = intval($_GET['currentId'] ?? 0);
$rev = $_GET['rev'] ?? null;

if (!$direction || $currentId === 0 || !$rev) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

if ($direction === 'prev') {
    $sql = "
        SELECT idfoto, rmalu
        FROM visita 
        WHERE idfoto < $currentId AND rev = '$rev' 
        ORDER BY idfoto DESC 
        LIMIT 1";
} elseif ($direction === 'next') {
    $sql = "
        SELECT idfoto, rmalu
        FROM visita 
        WHERE idfoto > $currentId AND rev = '$rev' 
        ORDER BY idfoto ASC 
        LIMIT 1";
} else {
    echo json_encode(['success' => false, 'message' => 'Direção inválida']);
    exit;
}

$result = $conexao->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Erro na consulta SQL: ' . $conexao->error]);
    exit;
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'idvisita' => $row['idfoto'],
        'rmalu' => $row['rmalu']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Não há visitas pendentes na direção selecionada.']);
}
