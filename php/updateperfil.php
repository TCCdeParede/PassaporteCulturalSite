<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
include "conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rmalu'], $_POST['nomealu'])) { // Verifica se os dados essenciais estão presentes
        $rmalu = $_POST['rmalu'];
        $nomealu = $_POST['nomealu'];
        
        // Inicializa a variável fotoPath com o valor atual da foto
        $fotoPath = null;

        // Se o usuário enviou uma nova foto, processa o upload
        if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
            $profileImage = $_FILES['profileImage'];
            
            // Processar a imagem
            $fotoNome = uniqid() . '.' . pathinfo($profileImage['name'], PATHINFO_EXTENSION);
            $uploadDir = '../uploads/alunos/';
            $uploadPath = $uploadDir . $fotoNome;

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true); // Cria o diretório se não existir
            }

            if (move_uploaded_file($profileImage['tmp_name'], $uploadPath)) {
                $fotoPath = "/uploads/alunos/$fotoNome"; // Caminho relativo
            } else {
                echo json_encode(["error" => "Erro ao fazer upload da imagem"]);
                exit;
            }
        }

        // Atualizar o banco de dados com o novo nome e, se houver, a nova foto
        if ($fotoPath) {
            // Caso a foto tenha sido alterada
            $sql = "UPDATE alunos SET nomealu = ?, fotoalu = ? WHERE rmalu = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssi", $nomealu, $fotoPath, $rmalu);
        } else {
            // Caso a foto não tenha sido alterada
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
