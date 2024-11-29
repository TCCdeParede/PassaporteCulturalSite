<?php
header("Content-Type: application/json");
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rmalu = isset($_GET['rmalu']) ? $_GET['rmalu'] : null;

    if ($rmalu) {
        // Listar visitas pendentes do aluno
        $query = "SELECT v.*, a.nomealu, v.motivo FROM visita v JOIN alunos a ON v.rmalu = a.rmalu WHERE v.rmalu = ?";
        $stmt = $conexao->prepare($query);
        $stmt->bind_param("s", $rmalu);
        $stmt->execute();
        $result = $stmt->get_result();

        $visitas = [];
        while ($row = $result->fetch_assoc()) {
            $pontos = 0;
            switch ($row['local']) {
                case 'Show':
                case 'Teatro':
                case 'Feira':
                    $pontos = 20;
                    break;
                case 'Centro Histórico':
                case 'Museu':
                case 'Visita Técnica':
                    $pontos = 15;
                    break;
                case 'Exposição':
                case 'Cinema':
                    $pontos = 10;
                    break;
                case 'Biblioteca':
                case 'Evento Esportivo':
                    $pontos = 5;
                    break;
                default:
                    $pontos = 0;
            }

            $row['pontos'] = $pontos;
            $visitas[] = $row;
        }

        echo json_encode(["status" => "sucesso", "data" => $visitas]);
    } else {
        echo json_encode(["status" => "erro", "message" => "rmalu não fornecido."]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $idfoto = $data['idfoto'];
    $rev = $data['rev'];

    $query = "UPDATE visita SET rev = ? WHERE idfoto = ?";
    $stmt = $conexao->prepare($query);
    $stmt->bind_param("si", $rev, $idfoto);

    if ($stmt->execute()) {
        echo json_encode(["status" => "sucesso", "message" => "Visita atualizada!"]);
    } else {
        echo json_encode(["status" => "erro", "message" => "Erro ao atualizar a visita."]);
    }
    exit();
}

echo json_encode(["status" => "erro", "message" => "Método inválido."]);
