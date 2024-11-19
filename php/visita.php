<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "conexao.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

ob_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (empty($input["userId"]) || empty($input["local"]) || empty($input["photos"])) {
        echo json_encode(["status" => "error", "message" => "Campos obrigatórios não preenchidos"]);
        exit;
    }

    $userId = $input["userId"];
    $local = $input["local"];
    $pontfoto = 0;
    $rev = "nao";
    $rmprof = 22513;
    $photos = $input["photos"];
    $firstPhoto = $photos[0];
    $data = date("Y-m-d", strtotime($firstPhoto["date"]));
    $hora = date("H:i:s", strtotime($firstPhoto["date"]));
    $cdx = $firstPhoto["location"]["latitude"];
    $cdy = $firstPhoto["location"]["longitude"];

    $imgData = base64_decode($firstPhoto["uri"]); // Decodificando a imagem de base64

    $stmt = $conexao->prepare("INSERT INTO visita (imgfoto, cdx, cdy, rev, data, hora, pontfoto, rmalu, rmprof) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("bddsssiii", $imgData, $cdx, $cdy, $rev, $data, $hora, $pontfoto, $userId, $rmprof);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Visita registrada com sucesso"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao registrar visita"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Método inválido"]);
}

$conexao->close();
ob_end_flush();
?>