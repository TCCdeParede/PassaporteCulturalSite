<?php

include("conexao.php");

$resetAluno = "UPDATE alunos SET pontano = 0";
$resetTurma = "UPDATE turma SET pontgeral = 0";

if ($conexao->query($resetAluno) === TRUE && $conexao->query($resetTurma) === TRUE) {
    echo "Pontuações anuais resetadas com sucesso!";
} else {
    echo "Erro ao resetar pontuações: " . $conexao->error;
}
