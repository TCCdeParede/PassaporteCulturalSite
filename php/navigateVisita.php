<?php

include "conexao.php";

$direction = $_GET['direction'];
$currentId = intval($_GET['currentId']);
$rmalu = $_GET['rmalu'];

if ($direction === 'prev') {
    // Busca a visita pendente anterior à atual
    $sql = "
        SELECT idfoto 
        FROM visita 
        WHERE idfoto < $currentId AND rev = 'Pendente' 
        ORDER BY idfoto DESC 
        LIMIT 1";
} elseif ($direction === 'next') {
    // Busca a visita pendente posterior à atual
    $sql = "
        SELECT idfoto 
        FROM visita 
        WHERE idfoto > $currentId AND rev = 'Pendente' 
        ORDER BY idfoto ASC 
        LIMIT 1";
} else {
    echo json_encode(['success' => false, 'message' => 'Direção inválida']);
    exit;
}

$result = $conexao->query($sql);

if ($result->num_rows > 0) {
    // Se encontrar uma visita pendente na direção solicitada
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'idvisita' => $row['idfoto']]);
} else {
    // Caso não existam visitas pendentes na direção especificada
    echo json_encode([
        'success' => false,
        'message' => 'Não há visitas pendentes na direção selecionada.'
    ]);
}
