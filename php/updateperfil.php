<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
include "conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rmalu'], $_POST['nomealu'])) {
        $rmalu = $_POST['rmalu'];
        $nomealu = $_POST['nomealu'];

        $fotoPath = null;

        $sqlSelect = "SELECT fotoalu FROM alunos WHERE rmalu = ?";
        $stmtSelect = $conexao->prepare($sqlSelect);
        $stmtSelect->bind_param("i", $rmalu);
        $stmtSelect->execute();
        $result = $stmtSelect->get_result();
        $currentPhoto = $result->fetch_assoc()['fotoalu'];

        if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
            $profileImage = $_FILES['profileImage'];
            $fotoNome = uniqid() . '.' . pathinfo($profileImage['name'], PATHINFO_EXTENSION);
            $uploadDir = '../uploads/alunos/';
            $uploadPath = $uploadDir . $fotoNome;

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($profileImage['tmp_name'], $uploadPath)) {
                $fotoPath = "/uploads/alunos/$fotoNome";

                if ($currentPhoto && file_exists("..$currentPhoto")) {
                    unlink("..$currentPhoto");
                }
            } else {
                echo json_encode(["error" => "Erro ao fazer upload da imagem"]);
                exit;
            }
        }

        if ($fotoPath) {
            $sql = "UPDATE alunos SET nomealu = ?, fotoalu = ? WHERE rmalu = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssi", $nomealu, $fotoPath, $rmalu);
        } else {
            $sql = "UPDATE alunos SET nomealu = ? WHERE rmalu = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("si", $nomealu, $rmalu);
        }

        if ($stmt->execute()) {
            echo json_encode(["message" => "Perfil atualizado com sucesso!", "fotoPath" => $fotoPath]);
        } else {
            echo json_encode(["error" => "Erro ao atualizar perfil: " . $stmt->error]);
        }
    } else {
        echo json_encode(["error" => "Faltam dados para atualizar o perfil."]);
    }
} else {
    echo json_encode(["error" => "Método inválido, use POST."]);
}

$conexao->close();
?>