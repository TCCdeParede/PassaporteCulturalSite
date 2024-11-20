<?php
header("Content-Type: application/json");
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Listar visitas pendentes
    $query = "SELECT v.idfoto, v.data, v.hora, v.local, a.nomealu, v.rev 
              FROM visita v 
              JOIN alunos a ON v.rmalu = a.rmalu 
              WHERE v.rev = 'Pendente'";
    $result = $conn->query($query);

    $visitas = [];
    while ($row = $result->fetch_assoc()) {
        $visitas[] = $row;
    }

    echo json_encode(["status" => "sucesso", "data" => $visitas]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar status da visita
    $data = json_decode(file_get_contents("php://input"), true);
    $idfoto = $data['idfoto'];
    $status = $data['status'];

    $query = "UPDATE visita SET rev = ? WHERE idfoto = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $idfoto);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso", "message" => "Visita atualizada!"]);
    } else {
        echo json_encode(["status" => "erro", "message" => "Erro ao atualizar a visita."]);
    }
    exit();
}

echo json_encode(["status" => "erro", "message" => "Método inválido."]);
?>
