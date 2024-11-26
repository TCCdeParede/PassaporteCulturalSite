<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "conexao.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

ob_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if ($inputData) {
        $userId = $inputData['userId'];
        $local = $inputData['local'];
        $photos = $inputData['photos'];
        $rev = $inputData['rev'];
        $rmprof = $inputData['rmprof'] ?? null;

        // Preparando para inserir na tabela visita
        $conexao->begin_transaction();

        try {
            // Converte a data e hora do formato "18/11/2024 22:31:38" para "Y-m-d H:i:s"
            $dataHora = DateTime::createFromFormat('d/m/Y H:i:s', $photos[0]['date']);
            $dataVisita = $dataHora->format('Y-m-d');
            $horaVisita = $dataHora->format('H:i:s');

            // Inserir a visita
            $sqlVisita = "INSERT INTO visita (cdx, cdy, rev, data, hora,  rmalu, rmprof, local) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtVisita = $conexao->prepare($sqlVisita);

            // Definindo os parâmetros para a visita
            $cdx = $photos[0]['location']['latitude']; // Latitude
            $cdy = $photos[0]['location']['longitude']; // Longitude
            $rev = ($rev === "Pendente") ? "Pendente" : "no"; // Revisado
            $dataVisita = $dataVisita; // Data da visita
            $horaVisita = $horaVisita; // Hora da visita
            $userId = $inputData['userId']; // Pegando o ID do usuário
            $rmprof = $inputData['rmprof']; // ID do professor
            $local = $inputData['local']; // Local vindo do aplicativo React Native

            // Corrigir o número de parâmetros no bind_param
            $stmtVisita->bind_param("dsssssss", $cdx, $cdy, $rev, $dataVisita, $horaVisita, $userId, $rmprof, $local);
            $stmtVisita->execute();
            $visitaId = $stmtVisita->insert_id; // Pegando o id da visita inserida

            // Criar diretório 'uploads' caso não exista
            $uploadDir = "../uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Cria o diretório com permissões adequadas
            }

            // Agora inserimos na tabela visita_imagens
            $sqlImagens = "INSERT INTO visita_imagens (idfoto, caminho_imagem) VALUES (?, ?)";
            $stmtImagens = $conexao->prepare($sqlImagens);

            // Inserir as imagens na tabela visita_imagens
            foreach ($photos as $photo) {
                // Caminho da imagem (nome do arquivo ou caminho)
                $imagePath = $uploadDir . "photo_" . uniqid() . ".jpg";
                file_put_contents($imagePath, base64_decode($photo['uri'])); // Salvando a imagem no servidor

                // Inserir o caminho da imagem na tabela visita_imagens
                $stmtImagens->bind_param("is", $visitaId, $imagePath);
                $stmtImagens->execute();
            }

            $conexao->commit();
            echo json_encode(["status" => "success"]);
        } catch (Exception $e) {
            $conexao->rollback();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Dados inválidos"]);
    }
}
