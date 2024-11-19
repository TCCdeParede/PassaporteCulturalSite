<?php
include "conexao.php";

$idvisita = $_GET['idvisita'];
$rmalu = $_GET['rmalu'];
?>
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
              <a class="nav-link" href="./classes.php">Classes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="./listarVisitas.php">Visitas</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./php/logout.php">Sair</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
  <!-- FIM HEADER -->

  <!-- MAIN -->
  <main class="container my-1 align-content-center">
    <h1 class="text-center my-1">Verificação de Pontos</h1>
    <div class="row mt-3 gap-2 justify-content-center">
      <!-- Coluna da Foto do Aluno e Informações -->
      <div
        class="col-11 col-md-3 d-flex flex-column align-items-center justify-content-center border border-black p-2 mx-auto shadow-sm">
        <div
          class="foto-aluno d-flex align-items-center justify-content-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
          </svg>
        </div>
        <div class="info-aluno my-5">
          <?php
          $sqlcode_aluno = "SELECT * FROM alunos WHERE rmalu = '$rmalu'";
          $aluno_query = $conexao->query($sqlcode_aluno);
          $aluno = $aluno_query->fetch_assoc();

          $nomealu = $aluno['nomealu'];
          $nometur = $aluno['nometur'];
          $pontmes = $aluno['pontmes'];

          echo "
          <p>RM: $rmalu</p>
          <p>Nome: $nomealu</p>
          <p>Turma: $nometur</p>
          <p>Pontos: $pontmes</p>
          ";

          // Calculando status
          $sqlcode_totalVisitas = "SELECT COUNT(*) as total FROM visita";
          $totalVisitas_query = $conexao->query($sqlcode_totalVisitas);
          $totalVisitas = $totalVisitas_query->fetch_assoc()['total'];

          $sqlcode_posicao = "SELECT ROW_NUMBER() OVER (ORDER BY idfoto asc) AS posicao, idfoto FROM visita";
          $posicao_query = $conexao->query($sqlcode_posicao);

          $posicaoAtual = 1;
          if ($posicao_query) {
            $posicoes = $posicao_query->fetch_all(MYSQLI_ASSOC);
            foreach ($posicoes as $index => $visita) {
                if ($visita['idfoto'] == $idvisita) {
                    $posicaoAtual = $index + 1; 
                    break;
                }
            }
        }
          ?>
        </div>
      </div>

      <!-- Coluna Central com Status -->
      <div class="col-11 col-md-8 border border-black p-2 mx-auto shadow-sm">
        <h4 class="text-center my-3"><?php echo "$posicaoAtual/$totalVisitas" ?></h4>
        <div class="row">
          <!-- Foto -->
          <div
            class="col-12 col-md-6 d-flex align-items-center justify-content-center mb-3 mb-md-0">
            <img src="../img/classes.png" class="img-fluid rounded" />
          </div>
          <!-- Maps -->
          <div
            class="col-12 col-md-6 d-flex align-items-center justify-content-center mb-3 mb-md-0">
            <img src="../img/classes.png" class="img-fluid rounded" />
          </div>
        </div>

        <!-- Data, Hora e Local -->
        <div class="mt-3 text-center">
          <?php
          $sqlcode_visitas = "SELECT * FROM visita WHERE idfoto = '$idvisita'";
          $visitas_query = $conexao->query($sqlcode_visitas);
          $visita = $visitas_query->fetch_assoc();
          $data = $visita['data'];
          $dataFormatada = date("d/m/Y", strtotime($data));
          $hora = $visita['hora'];
          $horaFormatada = date("H:i", strtotime($hora));
          $local = $visita['local'];

          echo "
          <span>Data: $dataFormatada</span>
          <span class=\"mx-3\">Horário: $horaFormatada</span>
          <span>Local visitado: $local</span>
          ";
          ?>
        </div>

        <!-- Setas e Botões -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <i class="bi bi-arrow-left fs-2"></i>
          <div>
            <button
              class="btn btn-success buttonConsultarAceitar"
              id="btnAceitar">
              Aceitar
            </button>
            <button
              class="btn btn-danger ms-2 buttonConsultarRecusar"
              id="btnRecusar">
              Recusar
            </button>
          </div>
          <i class="bi bi-arrow-right fs-2"></i>
        </div>

        <!-- Campo de recusa (escondido inicialmente) -->
        <div class="mt-3" id="recusaField" style="display: none">
          <textarea
            class="form-control"
            rows="5"
            placeholder="Explique o motivo da recusa..."
            style="resize: none"></textarea>
          <button
            class="btn btn-primary my-3 w-100 buttonCustom"
            id="btnEnviarRecusa">
            Enviar
          </button>
        </div>
      </div>
    </div>
  </main>
  <!-- FIM MAIN -->

  <!-- FOOTER -->
  <div class="container">
    <footer
      class="d-flex flex-wrap justify-content-center align-items-center mt-2 py-1 border-top border-dark">
      <div class="col-md-12 text-center">
        <span class="mb-3 mb-md-0 text-secondary-emphasis">© 2024 Bunny Boys, Inc</span>
      </div>
    </footer>
  </div>
  <!-- FIM FOOTER -->

  <!-- MODAL -->
  <div id="myModal" class="modal" style="display: none">
    <div class="modal-content">
      <p id="modalMessage"></p>
      <button id="acceptBtn" class="buttonCustom">Ok</button>
    </div>
  </div>

  <!-- BOOTSTRAP JS -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

  <script>
    // Obter o modal
    var modal = document.getElementById("myModal");

    document
      .getElementById("btnRecusar")
      .addEventListener("click", function() {
        document.getElementById("recusaField").style.display = "block";
      });

    document
      .getElementById("btnAceitar")
      .addEventListener("click", function() {
        // Exibir modal com a mensagem
        document.getElementById("modalMessage").innerText = "Você aceitou.";
        modal.style.display = "block";
        document.getElementById("btnAceitar").setAttribute("disabled", "");
        document.getElementById("btnRecusar").setAttribute("disabled", "");
      });

    document
      .getElementById("btnEnviarRecusa")
      .addEventListener("click", function() {
        let motivo = document.querySelector("#recusaField textarea").value;
        if (motivo.trim() === "") {
          document.getElementById("modalMessage").innerText =
            "Por favor, explique o motivo da recusa.";
          modal.style.display = "block";
        } else {
          document.getElementById("modalMessage").innerText =
            "Recusa enviada: " + motivo;
          modal.style.display = "block";
          document
            .querySelector(".form-control")
            .setAttribute("disabled", "");
          document
            .getElementById("btnEnviarRecusa")
            .setAttribute("disabled", "");
          document.getElementById("btnAceitar").setAttribute("disabled", "");
          document.getElementById("btnRecusar").setAttribute("disabled", "");
        }
      });

    // Fechar modal ao clicar no botão "Ok"
    document.getElementById("acceptBtn").onclick = function() {
      modal.style.display = "none";
    };

    // Fechar modal ao clicar fora dele
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    };
  </script>
</body>

</html>