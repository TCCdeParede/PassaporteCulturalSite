<?php

include "conexao.php";

$idvisita = $_POST['idvisita'];
$motivo = $_POST['motivo'];
$rev = $_POST['rev'];

$update_queryVisita = "UPDATE visita SET rev = 'Não aceito', motivo = ? WHERE idfoto = ?";
$update_stmtVisita = $conexao->prepare($update_queryVisita);
$update_stmtVisita->bind_param("ss", $motivo, $idvisita);
$update_stmtVisita->execute();

if ($update_stmtVisita->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Visita recusada com sucesso.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Falha ao recusar a visita.']);
}
