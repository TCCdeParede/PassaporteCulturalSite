<?php
session_start();

// Mensagens de erro e sucesso
$errorRm = '';
$errorSenha = '';

if (isset($_SESSION['errorRm'])) {
  $errorRm = $_SESSION['errorRm'];
  unset($_SESSION['errorRm']);
}

if (isset($_SESSION['errorSenha'])) {
  $errorSenha = $_SESSION['errorSenha'];
  unset($_SESSION['errorSenha']);
}

$statusMessage = '';
if (isset($_GET['status'])) {
  if ($_GET['status'] === 'email_sent') {
    $statusMessage = 'Um link para redefinir sua senha foi enviado para seu e-mail.';
  } elseif ($_GET['status'] === 'password_reset') {
    $statusMessage = 'Sua senha foi redefinida com sucesso!';
  }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Passaporte Cultural | Login</title>

  <!-- BOOTSTRAP -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
  <!-- HEADER -->
  <nav class="navbar navbar-custom navbar-expand-lg border-body" data-bs-theme="dark">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1 titleLogin"><a class="navbar-brand fs-4" href="#">Passaporte Cultural</a></span>
    </div>
  </nav>
  <!-- FIM HEADER -->

  <!-- MAIN -->
  <main class="form-signin container-lg m-auto d-flex flex-column justify-content-center px-4"
    style="min-height: 75vh;">
    <div class="col-12 col-md-9 mx-auto">
      <form action="valida_login.php" method="post" autocomplete="off">
        <h1 class="h3 mb-3 fw-normal text-center">Login</h1>

        <!-- Campo RadioButton -->
        <div class="d-flex justify-content-center mb-3">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="tipoLogin" id="flexRadioDefault1" value="professor"
              checked>
            <label class="form-check-label" for="flexRadioDefault1">
              Professor
            </label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="tipoLogin" id="flexRadioDefault2" value="administrador">
            <label class="form-check-label" for="flexRadioDefault2">
              Administrador
            </label>
          </div>
        </div>

        <!-- Campo RM/Usuário -->
        <div class="mb-3 inputGroup">
          <label for="inputRM" id="labelRM">RM</label>
          <input type="number" class="form-control" id="inputRM" name="rm" placeholder="RM" />
          <?php if ($errorRm): ?>
            <div class="text-danger"><?= $errorRm ?></div>
          <?php endif; ?>
        </div>

        <!-- Campo Senha -->
        <div class="mb-3 inputGroup">
          <label for="inputPassword">Senha</label>
          <input type="password" class="form-control" id="inputPassword" name="senha" placeholder="Senha" />
          <?php if ($errorSenha): ?>
            <div class="text-danger"><?= $errorSenha ?></div>
          <?php endif; ?>
        </div>

        <!-- Redefinir senha -->
        <div class="form-check text-start my-4">
          <div class="d-block m-auto text-center text-md-start">
            <p class="mb-0 mb-md-0 mt-3 mt-md-0">
              <a href="redefinirSenha.php"
                class="link-offset-2 link-offset-3-hover link-dark link-underline link-underline-opacity-0 link-underline-opacity-75-hover">
                Esqueceu a senha? Clique aqui para redefinir
              </a>
            </p>
          </div>
        </div>

        <!-- Botão Entrar -->
        <button class="btn btn-primary w-100 py-2 buttonCustom" type="submit">Entrar</button>
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

  <!-- TROCAR LOGIN -->
  <script>
    function alterarCampoLogin() {
      const tipoLogin = document.querySelector('input[name="tipoLogin"]:checked').value;
      const inputRM = document.getElementById('inputRM');
      const labelRM = document.getElementById('labelRM');

      if (tipoLogin === 'administrador') {
        // Administrador selecionado
        inputRM.setAttribute('type', 'text');
        inputRM.setAttribute('placeholder', 'Usuário');
        labelRM.textContent = 'Usuário';
      } else {
        // Professor selecionado
        inputRM.setAttribute('type', 'number');
        inputRM.setAttribute('placeholder', 'RM');
        labelRM.textContent = 'RM';
      }
    }

    // Adiciona o evento de mudança para todos os radio buttons com o name correto
    document.querySelectorAll('input[name="tipoLogin"]').forEach(radio => {
      radio.addEventListener('change', alterarCampoLogin);
    });

    // Chama a função uma vez ao carregar para configurar o estado inicial
    alterarCampoLogin();
  </script>
  <!-- FIM Trocar login -->

  <script>
    const statusMessage = "<?= $statusMessage ?>";

    if (statusMessage) {
      const modal = document.getElementById('myModal');
      const modalMessage = document.getElementById('modalMessage');
      const acceptBtn = document.getElementById('acceptBtn');

      modalMessage.textContent = statusMessage;
      modal.style.display = 'block';

      acceptBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        window.history.replaceState({}, document.title, window.location.pathname);
      });
    }
  </script>

  <!-- BOOTSTRAP JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</body>

</html>