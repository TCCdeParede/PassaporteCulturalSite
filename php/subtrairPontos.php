<?php
session_start();

if (!isset($_SESSION["tipoLogin"]) || $_SESSION["tipoLogin"] !== 'administrador') {
    die("Acesso negado");
}

include "conexao.php";

// Recupera os dados da requisição
$data = json_decode(file_get_contents('php://input'), true);
$rev = $data['rev'];
$idvisita = $data['idvisita'];
$motivo = $data['motivo'];
$motivo = trim($motivo);
if (empty($motivo)) {
    $motivo = null; 
}
$rmalu = $data['rmalu'];

$sql = "SELECT local FROM visita WHERE idfoto = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $idvisita);
$stmt->execute();
$result = $stmt->get_result();
$local = $result->fetch_assoc()['local'];

$pontuacao = 0;
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

$sqlUpdate = "UPDATE visita SET rev = 'Não aceito', motivo = ? WHERE idfoto = ?";
$stmtUpdate = $conexao->prepare($sqlUpdate);
$stmtUpdate->bind_param("si", $motivo, $idvisita);
$stmtUpdate->execute();

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

$novoPontMesGeral = max(0, $aluno['pontmesGeralAluno'] - $pontuacao);
$novoPontAnoGeral = max(0, $aluno['pontanoGeralAluno'] - $pontuacao);
$novoPontCompMes = max(0, $aluno['pontcompmesAluno'] - $pontuacao);
$novoPontCompAno = max(0, $aluno['pontcompanoAluno'] - $pontuacao);

$updateAluno = "UPDATE alunos SET 
    pontmesGeralAluno = ?, 
    pontanoGeralAluno = ?, 
    pontcompmesAluno = ?, 
    pontcompanoAluno = ? 
    WHERE rmalu = ?";
$stmtUpdateAluno = $conexao->prepare($updateAluno);
$stmtUpdateAluno->bind_param("iiiis", $novoPontMesGeral, $novoPontAnoGeral, $novoPontCompMes, $novoPontCompAno, $rmalu);
$stmtUpdateAluno->execute();

// Atualizar pontuação da turma
$nometur = $aluno['nometur'];
$queryTurma = "SELECT pontmesGeralTurma, pontanualGeralTurma, pontcompmensalTurma, pontcompgeralTurma FROM turma WHERE nometur = ?";
$stmtTurma = $conexao->prepare($queryTurma);
$stmtTurma->bind_param("s", $nometur);
$stmtTurma->execute();
$resultTurma = $stmtTurma->get_result();
$turma = $resultTurma->fetch_assoc();

if ($turma) {
    $novoPontMesGeralTurma = max(0, $turma['pontmesGeralTurma'] - $pontuacao);
    $novoPontAnualTurma = max(0, $turma['pontanualGeralTurma'] - $pontuacao);
    $novoPontCompMensalTurma = max(0, $turma['pontcompmensalTurma'] - $pontuacao);
    $novoPontCompGeralTurma = max(0, $turma['pontcompgeralTurma'] - $pontuacao);

    $updateTurma = "UPDATE turma SET 
        pontmesGeralTurma = ?, 
        pontanualGeralTurma = ?, 
        pontcompmensalTurma = ?, 
        pontcompgeralTurma = ? 
        WHERE nometur = ?";
    $stmtUpdateTurma = $conexao->prepare($updateTurma);
    $stmtUpdateTurma->bind_param("iiiss", $novoPontMesGeralTurma, $novoPontAnualTurma, $novoPontCompMensalTurma, $novoPontCompGeralTurma, $nometur);
    $stmtUpdateTurma->execute();
}

echo json_encode([
    'success' => true,
    'message' => 'Visita recusada e pontuações atualizadas.',
    'dadosAluno' => [
        'pontmesGeral' => $novoPontMesGeral,
        'pontanoGeral' => $novoPontAnoGeral,
        'pontcompmes' => $novoPontCompMes,
        'pontcompano' => $novoPontCompAno
    ]
]);
?>