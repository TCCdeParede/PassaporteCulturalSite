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

        $resetLink = "http://192.168.18.5/PassaporteCulturalSite/php/redefinirSenhaConfirmar.php?token=$token";

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

            // Conteúdo do email
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
    <title>Passaporte Cultural Digital | Redefinir Senha</title>

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
                <a class="navbar-brand fs-4" href="./login.php">Passaporte Cultural Digital</a>
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
        <footer
            class="d-flex flex-column flex-md-row justify-content-between align-items-center text-center mt-2 py-1 border-top border-dark">
            <!-- Texto centralizado -->
            <span class="mb-3 mb-md-0">© 2024 Passaporte Cultural Digital</span>

            <!-- Logo do GitHub -->
            <ul class="nav justify-content-center justify-content-md-end">
                <li class="ms-3">
                    <a class="text-body-secondary" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                            class="bi bi-github" viewBox="0 0 16 16">
                            <path
                                d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8">
                            </path>
                        </svg>
                    </a>
                </li>
            </ul>
        </footer>
    </div>
    <!-- FIM FOOTER -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

    <script>
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