<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastro Professores</title>
  <!-- BOOTSTRAP -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
  <!-- HEADER -->
  <nav class="navbar navbar-custom navbar-expand-lg border-body" data-bs-theme="dark">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1 titleLogin">Passaporte Cultural</span>
    </div>
  </nav>
  <!-- FIM HEADER -->

  <!-- MAIN -->
  <main class="form-signin container-lg m-auto mt-4 align-content-center px-4">
    <form id="cadastroForm" action="cadprofessor.php" method="post">
      <h1 class="h3 mb-3 fw-normal">Cadastro</h1>

      <div class="mb-3 inputGroup">
        <label for="inputRM">RM</label>
        <input type="number" class="form-control" id="inputRM" name="inputRM" placeholder="Your RM" />
      </div>

      <div class="mb-3 inputGroup">
        <label for="inputNome">Nome</label>
        <input type="text" class="form-control" id="inputNome" name="inputNome" placeholder="Your Name" />
      </div>

      <div class="mb-3 inputGroup">
        <label for="inputEmail">E-mail</label>
        <input type="text" class="form-control" id="inputEmail" name="inputEmail" placeholder="Your E-mail" />
      </div>

      <div class="mb-3 inputGroup">
        <label for="inputPassword">Password</label>
        <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Password" />
      </div>

      <div class="form-check text-start my-4">
        <p class="text-center">
          <a href="login.php"
            class="link-offset-2 link-offset-3-hover link-dark link-underline link-underline-opacity-0 link-underline-opacity-75-hover link-custom">
            Clique aqui para realizar o Login
          </a>
        </p>
      </div>
      <button class="btn btn-primary w-100 py-2 mb-4 buttonCustom" type="submit">Cadastrar</button>
    </form>
  </main>
  <!-- FIM MAIN -->

  <!-- FOOTER -->
  <div class="container">
    <footer class="d-flex flex-wrap justify-content-center align-items-center py-1 border-top border-dark">
      <div class="col-md-12 text-center">
        <span class="mb-3 mb-md-0 text-secondary-emphasis">© 2024 Bunny Boys, Inc</span>
      </div>
    </footer>
  </div>
  <!-- FIM FOOTER -->

  <!-- MODAL -->
  <div id="myModal" class="modal">
    <div class="modal-content">
      <p id="modalMessage">Cadastro realizado com sucesso!</p>
      <button id="acceptBtn" class="btn btn-primary buttonCustom">Ok</button>
    </div>
  </div>

  <!-- BOOTSTRAP JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const status = urlParams.get('status');
      const message = urlParams.get('message');

      if (status) {
        const modal = document.getElementById('myModal');
        const modalMessage = document.getElementById('modalMessage');
        const acceptBtn = document.getElementById('acceptBtn');

        // Define a mensagem no modal
        modalMessage.textContent = decodeURIComponent(message);

        // Exibe o modal
        modal.style.display = 'block';

        // Fechar modal ao clicar no botão "Ok"
        acceptBtn.onclick = () => {
          modal.style.display = 'none';

          // Redirecionar em caso de sucesso
          if (status === 'success') {
            window.location.href = 'login.php';
          }
        };

        // Fechar modal ao clicar fora dele
        window.onclick = (event) => {
          if (event.target === modal) {
            modal.style.display = 'none';

            // Redirecionar em caso de sucesso
            if (status === 'success') {
              window.location.href = 'login.php';
            }
          }
        };
      }
    });
  </script>

</body>

</html>