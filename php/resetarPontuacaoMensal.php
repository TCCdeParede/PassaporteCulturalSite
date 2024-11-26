<?php

include("conexao.php");

$resetAluno = "UPDATE alunos SET pontmes = 0";
$resetTurma = "UPDATE turma SET pontmensal = 0";

if ($conexao->query($resetAluno) === TRUE && $conexao->query($resetTurma) === TRUE) {
    echo "Pontuações mensais resetadas com sucesso!";
} else {
    echo "Erro ao resetar pontuações: " . $conexao->error;
}
