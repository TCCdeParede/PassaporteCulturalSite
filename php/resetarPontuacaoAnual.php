<?php

include("conexao.php");

$resetAluno = "UPDATE alunos SET pontanoGeralAluno = 0, pontcompanoAluno = 0";
$resetTurma = "UPDATE turma SET pontanualGeralTurma = 0, pontcompgeralTurma = 0";

if ($conexao->query($resetAluno) === TRUE && $conexao->query($resetTurma) === TRUE) {
    echo "Pontuações anuais resetadas com sucesso!";
} else {
    echo "Erro ao resetar pontuações: " . $conexao->error;
}
