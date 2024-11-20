<?php

include "conexao.php";

$direction = $_GET['direction'];
$currentId = $_GET['currentId'];
$rmalu = $_GET['rmalu'];

if ($direction === 'prev') {
    $sql = "SELECT idfoto FROM visita WHERE idfoto < '$currentId' AND rmalu = '$rmalu' ORDER BY idfoto DESC LIMIT 1";
} elseif ($direction === 'next') {
    $sql = "SELECT idfoto FROM visita WHERE idfoto > '$currentId' AND rmalu = '$rmalu' ORDER BY idfoto ASC LIMIT 1";
} else {
    echo json_encode(['sucess' => false]);
    exit;
}

$result = $conexao->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'idvisita' => $row['idfoto']]);
} else {
    echo json_encode(['success' => false]);
}
?>
