<?php
include("conexao.php");

$sql = "SELECT nometur FROM turma";
$result = $conexao->query($sql);

$turmas = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $turmas[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($turmas);
?>