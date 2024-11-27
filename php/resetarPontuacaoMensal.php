<?php

include("conexao.php");

$resetAluno = "UPDATE alunos SET pontmesGeralAluno = 0, pontcompmesAluno = 0";
$resetTurma = "UPDATE turma SET pontmesGeralTurma = 0, pontcompmensalTurma = 0";

if ($conexao->query($resetAluno) === TRUE && $conexao->query($resetTurma) === TRUE) {
    echo "Pontuações mensais resetadas com sucesso!";
} else {
    echo "Erro ao resetar pontuações: " . $conexao->error;
}
