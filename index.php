<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['rmprof'])) {
  header("Location: php/login.php");
  exit();
}

$nomeprof = isset($_SESSION['nomeprof']) ? $_SESSION['nomeprof'] : 'Professor(a)';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Passaporte Cultural - Professores</title>
  <!-- BOOTSTRAP -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous" />
  <!-- CSS -->
  <link rel="stylesheet" href="css/style.css" />
</head>

<body>
  <!-- HEADER -->
  <nav
    class="navbar navbar-custom navbar-expand-lg border-body"
    data-bs-theme="dark">
    <div class="container-fluid">
      <a class="navbar-brand fs-4" href="#">Passaporte Cultural</a>
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
              <a class="nav-link active" aria-current="page" href="#">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./php/classes.php">Classes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./php/listarVisitas.php">Visitas</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="php/logout.php">Sair</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
  <!-- FIM HEADER -->

  <!-- MAIN -->
  <main class="px-4 py-5 my-5 text-center d-flex flex-column justify-content-center">
    <h1 class="display-5 fw-bold text-body-emphasis mb-5">Bem-vindo(a) <?php echo $nomeprof; ?>!</h1>
    <div class="col-lg-6 mx-auto">
      <div class="card text-white shadow-sm" style="background-color: #001f3f">
        <div class="card-body">
          <h5 class="card-title">Revisões em pendência:</h5>
          <p class="card-text fs-4">0</p>
          <a href="./php/listarVisitas.php" class="btn btn-outline-light">VER MAIS</a>
        </div>
      </div>
    </div>
  </main>
  <!-- FIM MAIN -->

  <!-- FOOTER -->
  <div class="container">
    <footer
      class="d-flex flex-wrap justify-content-center align-items-center py-1 border-top border-dark">
      <div class="col-md-12 text-center">
        <span class="mb-3 mb-md-0 text-secondary-emphasis">© 2024 Bunny Boys, Inc</span>
      </div>
    </footer>
  </div>
  <!-- FIM FOOTER -->

  <!--BOOTSTRAP-->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>