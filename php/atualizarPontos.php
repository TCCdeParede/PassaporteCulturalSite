<?php
include "conexao.php";

// Dados passados pelo AJAX
$data = json_decode(file_get_contents('php://input'), true);
$rmalu = $data['rmalu'];
$local = $data['local'];
$rev = $data['rev'];
$idvisita = $data['idvisita'];

switch ($local) {
    case 'Show':
    case 'Teatro':
    case 'Feira':
        $pontuacao = 20;
        break;
    case 'Centro Histórico':
    case 'Museu':
    case 'Visita Técnica':
        $pontuacao = 15;
        break;
    case 'Exposição':
    case 'Cinema':
        $pontuacao = 10;
        break;
    case 'Biblioteca':
    case 'Evento Esportivo':
        $pontuacao = 5;
        break;
    default:
        $pontuacao = 0;
}

$query = "SELECT pontmes, pontano, nometur FROM alunos WHERE rmalu = ?";
$stmt = $conexao->prepare($query);
$stmt->bind_param("s", $rmalu);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();

$pontosAtualmente = $aluno['pontmes'];
$pontosFaltando = 200 - $pontosAtualmente;

if ($pontuacao > $pontosFaltando) {
    $pontuacao = $pontosFaltando;
}

$mesAtual = date("m");
$ferias = [1, 7, 12];

// Checando se é um mês de férias
if (!in_array($mesAtual, $ferias) && ($aluno['pontmes'] + $pontuacao) > 200) {
    if ($aluno['pontmes'] + $pontuacao > 200) {
        echo json_encode(['success' => false, 'message' => 'Limite de pontos mensal atingido.']);
        exit;
    }
}

$novoPontMes = $aluno['pontmes'] + $pontuacao;
$novoPontAno = $aluno['pontano'] + $pontuacao;

// UPDATE alunos
$update_query = "UPDATE alunos SET pontmes = ?, pontano = ? WHERE rmalu = ?";
$update_stmt = $conexao->prepare($update_query);
$update_stmt->bind_param("iis", $novoPontMes, $novoPontAno, $rmalu);
$update_stmt->execute();

// UPDATE visita
$update_queryVisita = "UPDATE visita SET rev = 'Aceito' WHERE idfoto = ?";
$update_stmtVisita = $conexao->prepare($update_queryVisita);
$update_stmtVisita->bind_param("s", $idvisita);
$update_stmtVisita->execute();

$nometur = $aluno['nometur'];

// Buscar a pontuação geral da sala
$querySala = "SELECT pontgeral FROM turma WHERE nometur = ?";
$stmtSala = $conexao->prepare($querySala);
$stmtSala->bind_param("s", $nometur);
$stmtSala->execute();
$resultSala = $stmtSala->get_result();
$sala = $resultSala->fetch_assoc();

if ($sala) {
    $novoPontGerais = $sala['pontgeral'] + $pontuacao;

    // UPDATE turma
    $update_queryTurma = "UPDATE turma SET pontgeral = ? WHERE nometur = ?";
    $update_stmtTurma = $conexao->prepare($update_queryTurma);
    $update_stmtTurma->bind_param("is", $novoPontGerais, $nometur);
    $update_stmtTurma->execute();
}

echo json_encode([
    'success' => true,
    'message' => 'Pontuação atualizada com sucesso.',
    'novoPontMes' => $novoPontMes,
    'novoPontAno' => $novoPontAno
]);
?>