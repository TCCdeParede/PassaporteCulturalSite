<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "conexao.php";

ob_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if ($inputData) {
        $userId = $inputData['userId'];
        $local = $inputData['local'];
        $photos = $inputData['photos'];
        $rev = $inputData['rev'];
        $rmprof = $inputData['rmprof'] ?? null;

        $conexao->begin_transaction();

        try {
            // Converte a data/hora de "18/11/2024 22:31:38" para "Y-m-d H:i:s"
            $dataHora = DateTime::createFromFormat('d/m/Y H:i:s', $photos[0]['date']);
            $dataVisita = $dataHora->format('Y-m-d');
            $horaVisita = $dataHora->format('H:i:s');

            // Inserir a visita
            $sqlVisita = "INSERT INTO visita (cdx, cdy, rev, data, hora,  rmalu, rmprof, local) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtVisita = $conexao->prepare($sqlVisita);

            $cdx = $photos[0]['location']['latitude'];
            $cdy = $photos[0]['location']['longitude'];
            $rev = ($rev === "Pendente") ? "Pendente" : "no";
            $dataVisita = $dataVisita;
            $horaVisita = $horaVisita;
            $userId = $inputData['userId'];
            $rmprof = $inputData['rmprof'];
            $local = $inputData['local'];
            
            $stmtVisita->bind_param("dsssssss", $cdx, $cdy, $rev, $dataVisita, $horaVisita, $userId, $rmprof, $local);
            $stmtVisita->execute();
            $visitaId = $stmtVisita->insert_id;

            $uploadDir = "../uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $sqlImagens = "INSERT INTO visita_imagens (idfoto, caminho_imagem) VALUES (?, ?)";
            $stmtImagens = $conexao->prepare($sqlImagens);

            foreach ($photos as $photo) {
                $imagePath = $uploadDir . "photo_" . uniqid() . ".jpg";
                file_put_contents($imagePath, base64_decode($photo['uri']));

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
