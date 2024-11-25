<?php

include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rmprof = $_POST['rmprof'];

    if (!$rmprof) {
        echo json_encode(['success' => false, 'message' => 'RM do professor não encontrado']);
        exit;
    }

    $sql = $conexao->prepare("DELETE FROM professor WHERE rmprof = ?");
    $sql->bind_param("i", $rmprof);
    if ($sql->execute()) {
        echo json_encode(["success" => true, "message" => "Professor excluído com sucesso"]);
    } else {
        echo json_encode(["success"=> false, "message"=> "Erro ao excluir professor"]);
    }
} else {
    echo json_encode(["success"=> false, "message"=> "Método não suportado"]);
}
