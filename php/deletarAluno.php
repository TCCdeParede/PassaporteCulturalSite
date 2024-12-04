<?php
session_start();

include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $rmalu = $_POST["rmalu"] ?? null;

    if (!$rmalu) {
        echo json_encode(["success" => false, "message" => "RM do aluno não fornecido."]);
        exit();
    }

    $query = $conexao->prepare("SELECT nometur, pontcompmesAluno, pontcompanoAluno, pontmesGeralAluno, pontanoGeralAluno FROM alunos WHERE rmalu = ?");
    $query->bind_param("s", $rmalu);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Aluno não encontrado."]);
        exit();
    }

    $row = $result->fetch_assoc();
    $nometur = $row["nometur"];
    $pontcompmes = $row["pontcompmesAluno"];
    $pontcompano = $row["pontcompanoAluno"];
    $pontmesGeral = $row["pontmesGeralAluno"];
    $pontanoGeral = $row["pontanoGeralAluno"];

    // Deleta o aluno
    $deleteQuery = $conexao->prepare("DELETE FROM alunos WHERE rmalu = ?");
    $deleteQuery->bind_param("s", $rmalu);

    if ($deleteQuery->execute()) {
        // Subtrai os pontos do aluno da turma
        $updateQuery = $conexao->prepare("UPDATE turma SET pontcompmensalTurma = pontcompmensalTurma - ?, pontcompgeralTurma = pontcompgeralTurma - ?, pontmesGeralTurma = pontmesGeralTurma - ?, pontanualGeralTurma = pontanualGeralTurma - ? WHERE nometur = ?");
        $updateQuery->bind_param("iiiis", $pontcompmes, $pontcompano, $pontmesGeral, $pontanoGeral, $nometur);
        $updateQuery->execute();

        echo json_encode(["success" => true, "message" => "Aluno excluído com sucesso."]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao excluir o aluno."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método não suportado."]);
}
