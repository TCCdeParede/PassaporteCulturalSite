<?php
include "conexao.php";

// Verificar se os dados chegaram via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar se as variáveis estão definidas e não vazias
    if (isset($_POST['rmalu'], $_POST['nomealu'], $_POST['emailalu'], $_POST['alusenha'], $_POST['pontmes'], $_POST['pontano'], $_POST['nometur'])) {
        // Obter dados do POST
        $rmalu = $_POST['rmalu'];
        $nomealu = $_POST['nomealu'];
        $emailalu = $_POST['emailalu'];
        $alusenha = $_POST['alusenha'];
        $pontmes = $_POST['pontmes'];
        $pontano = $_POST['pontano'];
        $nometur = $_POST['nometur'];

        $pontmes = 0;
        $pontano = 0;

        // Preparar a consulta SQL
        $sql = "INSERT INTO alunos (rmalu, nomealu, emailalu, alusenha, pontmes, pontano, nometur)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Usar prepared statement para evitar SQL Injection
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("issiiis", $rmalu, $nomealu, $emailalu, $alusenha, $pontmes, $pontano, $nometur)

        // Executar e verificar se a inserção foi bem-sucedida
        if ($stmt->execute()) {
            echo json_encode(["message" => "Aluno cadastrado com sucesso!"]);
        } else {
            echo json_encode(["error" => "Erro ao cadastrar aluno: " . $stmt->error]);
        }

        // Fechar a conexão
        $stmt->close();
    } else {
        echo json_encode(["error" => "Faltam dados para o cadastro."]);
    }
} else {
    echo json_encode(["error" => "Método inválido, use POST."]);
}

$conexao->close();
?>