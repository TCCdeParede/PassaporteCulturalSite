<?php
include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rmprof = $_POST['rmprof'];
    $nomeprof = $_POST['nomeprof'];
    $emailprof = $_POST['emailprof'];

    $query = "SELECT COUNT(*) AS total FROM professor WHERE emailprof = ?";
    if ($stmt = $conexao->prepare($query)) {
        $stmt->bind_param("s", $emailprof);
        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        if ($total > 0) {
            echo json_encode(['success' => false, 'message' => 'Este e-mail já está cadastrado']);
            exit;
        }
    }

    $apiKey = '6ab3ac10951c42b810386bebabe64b2ba17d2ce8';
    $url = "https://api.hunter.io/v2/email-verifier?email=" . urlencode($emailprof) . "&api_key=" . $apiKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['success' => false, 'message' => 'Erro ao verificar o e-mail']);
        exit;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!$data || !isset($data['data']['result'])) {
        echo json_encode(['success' => false, 'message' => 'Erro ao verificar o e-mail']);
        exit;
    }

    $result = $data['data']['result'];
    if ($result !== 'deliverable') {
        echo json_encode(['success' => false, 'message' => "O e-mail fornecido não é válido ou não existe"]);
        exit;
    }

    $nomeSemEspaco = str_replace(' ', '', $_POST['nomeprof']);
    $profsenha = strtolower($nomeSemEspaco) . $rmprof;

    $senha_hashed = password_hash($profsenha, PASSWORD_BCRYPT);

    $nvauto = 0;

    $query = "INSERT INTO professor (rmprof, nomeprof, emailprof, profsenha, nvauto) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conexao->prepare($query)) {
        $stmt->bind_param("isssi", $rmprof, $nomeprof, $emailprof, $senha_hashed, $nvauto);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Professor cadastrado com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar professor']);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ocorreu um erro. Tente novamente mais tarde.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método inválido!'
    ]);
}
?>