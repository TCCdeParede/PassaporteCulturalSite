<?php
include "conexao.php";

// Recebendo os dados do FormData
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se as fotos foram enviadas
    if (isset($_FILES['photos'])) {
        $photos = $_FILES['photos'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $date = $_POST['date'];
        $rmalu = $_POST['rmalu'];
        $rmprof = $_POST['rmprof'];
        $local = $_POST['local'];  // A localização (teatro, feira, etc)

        // Itera sobre as fotos enviadas
        foreach ($photos['tmp_name'] as $index => $tmpName) {
            // Obtém o conteúdo da foto
            $photoData = file_get_contents($tmpName);
            $photoMimeType = mime_content_type($tmpName);
            
            // SQL para inserir a foto na tabela
            $stmt = $conexao->prepare("INSERT INTO visita (imgfoto, cdx, cdy, rev, dia, pontfoto, rmalu, rmprof) 
                                       VALUES (?, ?, ?, ?, ?, 0, ?, ?)");
            $stmt->bind_param("ssssdii", $photoData, $latitude, $longitude, $local, $date, $rmalu, $rmprof);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Fotos enviadas com sucesso"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Erro ao salvar a foto"]);
            }
            $stmt->close();
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Nenhuma foto enviada"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método inválido"]);
}
?>
