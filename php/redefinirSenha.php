<?php
session_start();
include('conexao.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $message = '';
    $status = '';

    $query = "SELECT * FROM professor WHERE emailprof = ?";
    $stmt = $conexao->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $professor = $result->fetch_assoc();

        // Gera o token de redefinição de senha
        $token = bin2hex(random_bytes(16));
        $expires = date('U') + 1800; // Token expira em 30 minutos

        $updateQuery = "UPDATE professor SET reset_token = ?, reset_expires = ? WHERE emailprof = ?";
        $updateStmt = $conexao->prepare($updateQuery);
        $updateStmt->bind_param('sis', $token, $expires, $email);
        $updateStmt->execute();

        $resetLink = "http://192.168.1.103/PassaporteCulturalSite/php/redefinirSenhaConfirmar.php?token=$token";

        // Configuração do PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tccdaparede2024@gmail.com';
            $mail->Password = 'goqu iykr kbxx fjul';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('tccdaparede2024@gmail.com', 'Passaporte Cultural');
            $mail->addAddress($email);

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Redefinição de Senha';
            $mail->Body = "
                <h2>Redefinição de Senha</h2>
                <p>Você solicitou uma redefinição de senha para sua conta no Passaporte Cultural.</p>
                <p>Clique no link abaixo para redefinir sua senha:</p>
                <a href='$resetLink'>$resetLink</a>
                <p>Este link expira em 30 minutos.</p>
            ";

            // Envia o email
            if ($mail->send()) {
                $status = 'email_sent';
            } else {
                $message = "Erro ao enviar o e-mail. Tente novamente.";
            }
        } catch (Exception $e) {
            $message = "Erro ao enviar o e-mail. Detalhes: {$mail->ErrorInfo}";
        }
    } else {
        $message = "E-mail não encontrado. Verifique e tente novamente.";
    }

    // Se houver status de sucesso, redireciona
    if ($status === 'email_sent') {
        header("Location: login.php?status=email_sent");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
    <!-- HEADER -->
    <nav class="navbar navbar-custom navbar-expand-lg border-body" data-bs-theme="dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 titleLogin">
                <a class="navbar-brand fs-4" href="./login.php">Passaporte Cultural</a>
            </span>
        </div>
    </nav>
    <!-- FIM HEADER -->

    <!-- MAIN -->
    <main class="form-signin container-lg m-auto d-flex flex-column justify-content-center px-4"
        style="min-height: 75vh;">
        <div class="col-12 col-md-9 mx-auto">
            <form method="post" action="redefinirSenha.php" autocomplete="off">
                <h1 class="h3 mb-3 fw-normal text-center">Redefinir Senha</h1>

                <!-- Campo Email -->
                <div class="mb-3 inputGroup">
                    <label for="email">Digite seu e-mail:</label>
                    <input type="email" name="email" class="form-control" placeholder="E-mail" required />
                </div>

                <button class="btn btn-primary w-100 py-2 buttonCustom" type="submit">
                    Enviar link para redefinir
                </button>
            </form>
        </div>
    </main>
    <!-- FIM MAIN -->

    <!-- MODAL -->
    <div id="myModal" class="modal" style="display: none;">
        <div class="modal-content">
            <p id="modalMessage"></p>
            <button id="acceptBtn" class="buttonCustom">Ok</button>
        </div>
    </div>
    <!-- FIM MODAL -->

    <!-- FOOTER -->
    <div class="container">
        <footer class="d-flex flex-wrap justify-content-center align-items-center py-1 border-top border-dark">
            <div class="col-md-12 text-center">
                <span class="mb-3 mb-md-0 text-secondary-emphasis">
                    © 2024 Bunny Boys, Inc
                </span>
            </div>
        </footer>
    </div>
    <!-- FIM FOOTER -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

    <script>
        // Exibir o modal caso haja mensagem de erro
        const message = "<?= $message ?>";
        if (message) {
            const modal = document.getElementById('myModal');
            const modalMessage = document.getElementById('modalMessage');
            modalMessage.textContent = message;
            modal.style.display = 'block';

            const acceptBtn = document.getElementById('acceptBtn');
            acceptBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }
    </script>
</body>

</html>