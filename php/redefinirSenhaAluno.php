<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

include "conexao.php";

function logMessage($message)
{
    $logFile = __DIR__ . "/log_redefinirSenhaAluno.txt";
    $date = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$date] $message\n", FILE_APPEND);
}

ob_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        logMessage("Erro: Email não informado.");
        echo json_encode(["status" => "error", "message" => "O email é obrigatório"]);
        exit;
    }

    $stmt = $conexao->prepare("SELECT rmalu FROM alunos WHERE emailalu = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        logMessage("Usuário encontrado: RM={$user['rmalu']}, Gerando token.");

        $updateStmt = $conexao->prepare("UPDATE alunos SET reset_token = ?, reset_expires = ? WHERE rmalu = ?");
        $updateStmt->bind_param("ssi", $token, $expires, $user['rmalu']);
        if ($updateStmt->execute()) {
            logMessage("Token atualizado no banco para RM={$user['rmalu']}.");
        } else {
            logMessage("Erro ao atualizar token no banco: " . $conexao->error);
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tccdaparede2024@gmail.com';
            $mail->Password = 'goqu iykr kbxx fjul';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('tccdaparede2024@gmail.com', 'Passaporte Cultural');
            $mail->addAddress($email);

            $resetLink = "http://192.168.1.104/PassaporteCulturalSite/php/resetSenhaAluno.php?token=$token";
            $mail->isHTML(true);
            $mail->Subject = 'Redefinição de Senha';
            $mail->Body = "
                <h1>Redefinição de Senha</h1>
                <p>Olá, clique no link abaixo para redefinir sua senha:</p>
                <a href='$resetLink'>$resetLink</a>
                <p><strong>Nota:</strong> Este link expira em 1 hora.</p>
            ";
            $mail->AltBody = "Olá, clique no link para redefinir sua senha: $resetLink";

            $mail->send();
            logMessage("Email enviado para $email.");
            echo json_encode(["status" => "success", "message" => "Um email com o link de redefinição foi enviado para $email"]);
        } catch (Exception $e) {
            logMessage("Erro ao enviar email: {$mail->ErrorInfo}");
            echo json_encode(["status" => "error", "message" => "Erro ao enviar email: {$mail->ErrorInfo}"]);
        }
    } else {
        logMessage("Erro: Email $email não encontrado no banco.");
        echo json_encode(["status" => "error", "message" => "Email não encontrado"]);
    }

    $stmt->close();
} else {
    logMessage("Erro: Método inválido.");
    echo json_encode(["status" => "error", "message" => "Método inválido"]);
}

$conexao->close();
ob_end_flush();
