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

            // Exibe a mensagem de erro no modal
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
        function showModal(message) {
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('myModal').style.display = 'block';
        }

        // Fechar o modal ao clicar no botão "Ok"
        document.getElementById('acceptBtn').addEventListener('click', function () {
            document.getElementById('myModal').style.display = 'none';
        });
    </script>
</body>

</html>