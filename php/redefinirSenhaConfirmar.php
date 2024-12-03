<?php
session_start();
include('conexao.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $currentTime = time();

    // Verifica se o token é válido e não expirou
    $query = "SELECT * FROM professor WHERE reset_token = ? AND reset_expires > ?";
    $stmt = $conexao->prepare($query);
    $stmt->bind_param('si', $token, $currentTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $senha1 = $_POST['senha1'];
            $senha2 = $_POST['senha2'];

            if (strlen($senha1) < 8) {
                $message = "A senha deve ter pelo menos 8 caracteres.";
            } elseif ($senha1 !== $senha2) {
                $message = "As senhas não coincidem.";
            } else {
                $senhaHash = password_hash($senha1, PASSWORD_DEFAULT);

                $updateQuery = "UPDATE professor SET profsenha = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?";
                $updateStmt = $conexao->prepare($updateQuery);
                $updateStmt->bind_param('ss', $senhaHash, $token);
                $updateStmt->execute();

                header("Location: login.php?status=password_reset");
                exit;
            }

            echo "<script>window.onload = function() { showModal('$message'); }</script>";
        }
    } else {
        $message = "Token inválido ou expirado.";
        echo "<script>window.onload = function() { showModal('$message'); }</script>";
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
            <span class="navbar-brand mb-0 h1 titleLogin"><a class="navbar-brand fs-4" href="./login.php">Passaporte
                    Cultural</a></span>
        </div>
    </nav>
    <!-- FIM HEADER -->

    <!-- MAIN -->
    <main class="form-signin container-lg m-auto d-flex flex-column justify-content-center px-4"
        style="min-height: 75vh;">
        <div class="col-12 col-md-9 mx-auto">
            <form method="post" action="redefinirSenhaConfirmar.php?token=<?php echo $_GET['token']; ?>"
                autocomplete="off">
                <h1 class="h3 mb-3 fw-normal text-center">Nova Senha</h1>

                <!-- Campo Nova Senha -->
                <div class="mb-3 inputGroup">
                    <label for="senha1">Nova Senha:</label>
                    <input type="password" name="senha1" class="form-control" minlength="8" required />
                </div>

                <!-- Campo Confirmar Nova Senha -->
                <div class="mb-3 inputGroup">
                    <label for="senha2">Confirmar Nova Senha:</label>
                    <input type="password" name="senha2" class="form-control" minlength="8" required />
                </div>

                <button class="btn btn-primary w-100 py-2 buttonCustom" type="submit">Redefinir Senha</button>
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
                    <a class="text-body-secondary" href="https://github.com/TCCdeParede" target="_blank">
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
        function showModal(message) {
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('myModal').style.display = 'block';
        }

        document.getElementById('acceptBtn').addEventListener('click', function () {
            document.getElementById('myModal').style.display = 'none';
        });
    </script>
</body>

</html>