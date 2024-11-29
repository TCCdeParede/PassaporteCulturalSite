<?php
include "conexao.php";

// Dados passados pelo AJAX
$data = json_decode(file_get_contents('php://input'), true);
$rmalu = $data['rmalu'];
$local = $data['local'];
$idvisita = $data['idvisita'];

// Determinar a pontuação baseada no local
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

$queryAluno = "SELECT pontmesGeralAluno, pontanoGeralAluno, pontcompmesAluno, pontcompanoAluno, nometur FROM alunos WHERE rmalu = ?";
$stmtAluno = $conexao->prepare($queryAluno);
$stmtAluno->bind_param("i", $rmalu);
$stmtAluno->execute();
$resultAluno = $stmtAluno->get_result();
$aluno = $resultAluno->fetch_assoc();

if (!$aluno) {
    echo json_encode(['success' => false, 'message' => 'Aluno não encontrado.']);
    exit;
}

$pontosAtualmente = $aluno['pontcompmesAluno'];
$pontosFaltando = 200 - $pontosAtualmente;

$mesAtual = date("m");
$ferias = [1, 7, 12];

// Dividir a pontuação entre computada e excedente
$pontuacaoComputada = min($pontuacao, $pontosFaltando);
$pontuacaoExcedente = $pontuacao - $pontuacaoComputada;

// Ajustar se for mês de férias
if (in_array($mesAtual, $ferias)) {
    $pontuacaoComputada += $pontuacaoExcedente;
    $pontuacaoExcedente = 0;
}

$novoPontMesGeral = $aluno['pontmesGeralAluno'] + $pontuacao;
$novoPontAnoGeral = $aluno['pontanoGeralAluno'] + $pontuacao;
$novoPontCompMes = $aluno['pontcompmesAluno'] + $pontuacaoComputada;
$novoPontCompAno = $aluno['pontcompanoAluno'] + $pontuacaoComputada;

$updateAluno = "UPDATE alunos SET pontmesGeralAluno = ?, pontanoGeralAluno = ?, pontcompmesAluno = ?, pontcompanoAluno = ? WHERE rmalu = ?";
$stmtUpdateAluno = $conexao->prepare($updateAluno);
$stmtUpdateAluno->bind_param("iiiis", $novoPontMesGeral, $novoPontAnoGeral, $novoPontCompMes, $novoPontCompAno, $rmalu);
$stmtUpdateAluno->execute();

// Atualizar o status da visita
$updateVisita = "UPDATE visita SET rev = 'Aceito', motivo = null WHERE idfoto = ?";
$stmtUpdateVisita = $conexao->prepare($updateVisita);
$stmtUpdateVisita->bind_param("i", $idvisita);
$stmtUpdateVisita->execute();

// Buscar dados da turma
$nometur = $aluno['nometur'];
$queryTurma = "SELECT pontmesGeralTurma, pontcompmensalTurma, pontcompgeralTurma, pontanualGeralTurma FROM turma WHERE nometur = ?";
$stmtTurma = $conexao->prepare($queryTurma);
$stmtTurma->bind_param("s", $nometur);
$stmtTurma->execute();
$resultTurma = $stmtTurma->get_result();
$turma = $resultTurma->fetch_assoc();

if ($turma) {
    $novoPontGeralTurma = $turma['pontmesGeralTurma'] + $pontuacao;
    $novoPontAnualGeralTurma = $turma['pontanualGeralTurma'] + $pontuacao;
    $novoPontCompMensalTurma = $turma['pontcompmensalTurma'] + $pontuacaoComputada;
    $novoPontCompGeralTurma = $turma['pontcompgeralTurma'] + $pontuacaoComputada;

    $updateTurma = "UPDATE turma SET pontmesGeralTurma = ?, pontcompmensalTurma = ?, pontcompgeralTurma = ?, pontanualGeralTurma = ? WHERE nometur = ?";
    $stmtUpdateTurma = $conexao->prepare($updateTurma);
    $stmtUpdateTurma->bind_param("iiiis", $novoPontGeralTurma, $novoPontCompMensalTurma, $novoPontCompGeralTurma, $novoPontAnualGeralTurma, $nometur);
    $stmtUpdateTurma->execute();
}

// Retornar resposta
echo json_encode([
    'success' => true,
    'message' => 'Pontuação atualizada com sucesso.',
    'dadosAluno' => [
        'pontmesGeral' => $novoPontMesGeral,
        'pontanoGeral' => $novoPontAnoGeral,
        'pontcompmes' => $novoPontCompMes,
        'pontcompano' => $novoPontCompAno
    ],
    'dadosTurma' => [
        'pontgeral' => $novoPontGeralTurma ?? null,
        'pontcompmensal' => $novoPontCompMensalTurma ?? null,
        'pontcompgeral' => $novoPontCompGeralTurma ?? null,
        'pontanualGeral' => $novoPontAnualGeralTurma ?? null
    ]
]);
