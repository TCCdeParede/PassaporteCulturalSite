<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tela do Aluno</title>
  <!-- BOOTSTRAP CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous" />
  <!-- BOOTSTRAP ICONS -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
  <!-- HEADER -->
  <nav
    class="navbar navbar-custom navbar-expand-lg border-body"
    data-bs-theme="dark">
    <div class="container-fluid">
      <a class="navbar-brand fs-4" href="../index.php">Passaporte Cultural</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasScrolling"
        aria-controls="offcanvasScrolling">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div
        class="offcanvas offcanvas-custom offcanvas-start"
        data-bs-scroll="true"
        data-bs-backdrop="false"
        tabindex="-1"
        id="offcanvasScrolling"
        aria-labelledby="offcanvasScrollingLabel">
        <div class="offcanvas-header">
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul
            class="navbar-nav w-100 d-flex justify-content-evenly gap-3 fs-5">
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="../index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../php/classes.php">Classes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="#">Visitas</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../php/logout.php">Sair</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
  <!-- FIM HEADER -->

  <!-- MAIN -->
  <main class="p-4 my-4 text-center">
    <div class="col-lg-6 mx-auto">
      <h1 class="display-5 fw-bold text-body-emphasis fs-3 mb-4">Visitas</h1>
      <div class="table-responsive">
        <table class="table table-hover table-bordered text-center align-middle">
          <thead>
            <tr>
              <th scope="col">RM</th>
              <th scope="col">Nome</th>
              <th scope="col">Sala</th>
              <th scope="col">Data de visita</th>
              <th scope="col">Local visitado</th>
              <th scope="col" colspan="2">Ação</th>
            </tr>
          </thead>
          <tbody class="table-group-divider">
            <?php
            include "conexao.php";
            $sqlcode = "SELECT * FROM visita";
            $sqlquery = $conexao->query($sqlcode);
            $visita = $sqlquery->fetch_assoc();
            $qtdRowsAffected = $sqlquery->num_rows;
            $sqlcode = "SELECT * FROM alunos";
            $sqlquery = $conexao->query($sqlcode);
            $aluno = $sqlquery->fetch_assoc();
            for ($i = 1; $i <= $qtdRowsAffected; $i++) {
              $rmalu = $visita['rmalu'];
              $nomealu = $aluno['nomealu'];
              $nometur = $aluno['nometur'];
              $data = $visita['data'];
              $local = $visita['local'];
              echo "
            
        <tr>
            <td>$rmalu</td>
            <td>$nomealu</td>
            <td>$nometur</td>
            <td>$data</td>
            <td>$local</td>
            <td><a href=\"editar.php?codigo=$rmalu\">[Editar]</a></td>
            <td><a href=\"excluir.php?codigo=$rmalu\">[Excluir]</a></td>
        </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
  <!-- FIM MAIN -->

  <!-- FOOTER -->
  <footer class="container">
    <div
      class="d-flex flex-wrap justify-content-center align-items-center py-1 border-top border-dark">
      <div class="col-md-12 text-center">
        <span class="mb-3 mb-md-0 text-secondary-emphasis">© 2024 Bunny Boys, Inc</span>
      </div>
    </div>
  </footer>
  <!-- FIM FOOTER -->

  <!-- MODAL -->
  <div id="myModal" class="modal" style="display: none">
    <div class="modal-content">
      <p id="modalMessage">Mensagem aqui!</p>
      <button id="acceptBtn">Ok</button>
    </div>
  </div>

  <!-- BOOTSTRAP JS -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>