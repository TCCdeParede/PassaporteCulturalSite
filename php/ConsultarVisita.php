<?php
session_start();

if (!isset($_SESSION["tipoLogin"])) {
  header("Location: ./logout.php");
  exit();
}

$isAdmin = $_SESSION['tipoLogin'] === 'administrador';

$rev = $_GET['rev'];

include "conexao.php";

$idvisita = $_GET['idvisita'];
$rmalu = $_GET['rmalu'];
?>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Passaporte Cultural | Consultar visita</title>
  <!-- BOOTSTRAP CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <!-- BOOTSTRAP ICONS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css" />
  <!-- Google Maps JavaScript API -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMwJ2vSpaCn-NTpNkBp_1I06TIdt4AT8U"></script>
</head>

<body>
  <!-- HEADER -->
  <nav class="navbar navbar-custom navbar-expand-lg border-body" data-bs-theme="dark">
    <div class="container-fluid">
      <a class="navbar-brand fs-4" href="../index.php">Passaporte Cultural</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling"
        aria-controls="offcanvasScrolling">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="offcanvas offcanvas-custom offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false"
        tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
        <div class="offcanvas-header">
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav w-100 d-flex justify-content-evenly gap-3 fs-5">
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="../index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./classes.php">Classes</a>
            </li>
            <?php
            if ($isAdmin) {
              echo '
                <li class="nav-item">
                    <a class="nav-link" href="./listarProfessores.php">Professores</a>
                </li>
            ';
            }
            ?>
            <li class="nav-item">
              <a class="nav-link active" href="./listarVisitas.php">Visitas</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./logout.php">Sair</a>
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
        class="col-11 col-md-3 d-flex flex-column align-items-center justify-content-center border border-black rounded-2 p-2 mx-auto shadow-sm">
        <div class="foto-aluno d-flex align-items-center justify-content-center">
          <!-- Foto perfil aluno -->
          <?php
          $sqlcode_foto = "SELECT fotoalu FROM alunos WHERE rmalu = '$rmalu'";
          $foto_query = $conexao->query($sqlcode_foto);
          $foto = $foto_query->fetch_assoc();

          if ($foto && !empty($foto['fotoalu'])) {
            // Gera o caminho completo da imagem
            $fotoPath = $foto['fotoalu'];
            echo "<img src='../{$fotoPath}' alt='Foto do Aluno' style='height: 150px;' class='img-fluid rounded-circle'>";
          } else {
            // Exibe um ícone padrão se não houver foto
            echo "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-person-circle' viewBox='0 0 16 16'>
                  <path d='M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0'/>
                  <path fill-rule='evenodd' d='M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1'/>
                </svg>";
          }
          ?>
        </div>
        <div class="info-aluno my-5">
          <?php
          $sqlcode_aluno = "SELECT * FROM alunos WHERE rmalu = '$rmalu'";
          $aluno_query = $conexao->query($sqlcode_aluno);
          $aluno = $aluno_query->fetch_assoc();

          $nomealu = $aluno['nomealu'];
          $nometur = $aluno['nometur'];
          $pontmesGeral = $aluno['pontmesGeralAluno'];
          $pontanoGeral = $aluno['pontanoGeralAluno'];

          echo "
          <p>RM: $rmalu</p>
          <p>Nome: $nomealu</p>
          <p>Turma: $nometur</p>
          <p id='pontMes'>Pontos no mês: $pontmesGeral</p>
          <p id='pontAno'>Pontos no ano: $pontanoGeral</p>
          ";

          // Calculando status
          
          if ($rev === 'Pendente') {
            $sqlcode_totalVisitas = "SELECT COUNT(*) as total FROM visita WHERE rev = 'Pendente'";
          } elseif ($rev === "Aceito") {
            $sqlcode_totalVisitas = "SELECT COUNT(*) as total FROM visita WHERE rev = 'Aceito'";
          } elseif ($rev === "Não aceito") {
            $sqlcode_totalVisitas = "SELECT COUNT(*) as total FROM visita WHERE rev = 'Não aceito'";
          }

          $totalVisitas_query = $conexao->query($sqlcode_totalVisitas);
          $totalVisitas = $totalVisitas_query->fetch_assoc()['total'];

          if ($rev === 'Pendente') {
            $sqlcode_posicao = "SELECT ROW_NUMBER() OVER (ORDER BY idfoto asc) AS posicao, idfoto FROM visita WHERE rev = 'Pendente'";
          } elseif ($rev === "Aceito") {
            $sqlcode_posicao = "SELECT ROW_NUMBER() OVER (ORDER BY idfoto asc) AS posicao, idfoto FROM visita WHERE rev = 'Aceito'";
          } elseif ($rev === "Não aceito") {
            $sqlcode_posicao = "SELECT ROW_NUMBER() OVER (ORDER BY idfoto asc) AS posicao, idfoto FROM visita WHERE rev = 'Não aceito'";
          }

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
      <div class="col-11 col-md-8 border border-black rounded-2 p-2 mx-auto shadow-sm">
        <h4 class="text-center my-3"><?php echo "$posicaoAtual/$totalVisitas" ?></h4>
        <?php

        if ($rev === 'Aceitou' || 'Não aceito') {
          echo "<h4 class='text-center my-3'>Status: $rev</h4>";
        }

        ?>
        <div class="row">
          <!-- Foto -->
          <div class="col-12 col-md-6 d-flex align-items-center justify-content-center mb-3 mb-md-0">
            <?php

            $sqlcode_imagens = "SELECT caminho_imagem FROM visita_imagens WHERE idfoto = '$idvisita'";
            $imagens_query = $conexao->query($sqlcode_imagens);

            $caminhos = [];

            while ($imagem = $imagens_query->fetch_assoc()) {
              $caminhos[] = $imagem['caminho_imagem'];
            }
            ?>
            <!-- CARROSSEL DE IMAGENS -->
            <div id="carouselExampleIndicators" class="carousel slide h-100 w-100" data-bs-ride="carousel">
              <!-- INDICADORES -->
              <div class="carousel-indicators">
                <?php
                foreach ($caminhos as $index => $caminho) {
                  $active = $index === 0 ? 'active' : '';
                  echo "<button type='button' data-bs-target='#carouselExampleIndicators' data-bs-slide-to='$index' class='$active' aria-label='Slide " . ($index + 1) . "'></button>";
                }
                ?>
              </div>
              <!-- FIM INDICADORES -->

              <!-- SLIDES -->
              <div class="carousel-inner h-100">
                <?php
                foreach ($caminhos as $index => $caminho) {
                  $active = $index === 0 ? 'active' : '';
                  echo "
                    <div class='carousel-item $active h-100'>
                      <img src='$caminho' class='d-block w-100 h-100 object-fit-cover rounded-3' alt='Imagem da visita'>
                    </div>";
                }
                ?>
              </div>
              <!-- FIM SLIDES -->

              <!-- CONTROLES -->
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
              <!-- FIM CONTROLES -->
            </div>
            <!-- FIM CARROSSEL DE IMAGENS -->
          </div>
          <!-- Maps -->
          <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
            <div id="map" class="w-100 h-100 border rounded-3" style="min-height: 300px;"></div>
          </div>
        </div>

        <!-- Data, Hora e Local -->
        <div class="mt-3 text-center">
          <?php
          if ($rev === 'Pendente') {
            $sqlcode_visitas = "SELECT * FROM visita WHERE idfoto = '$idvisita' AND rev = 'Pendente'";
          } elseif ($rev === "Aceito") {
            $sqlcode_visitas = "SELECT * FROM visita WHERE idfoto = '$idvisita' AND rev = 'Aceito'";
          } elseif ($rev === "Não aceito") {
            $sqlcode_visitas = "SELECT * FROM visita WHERE idfoto = '$idvisita' AND rev = 'Não aceito'";
          }

          $visitas_query = $conexao->query($sqlcode_visitas);
          $visita = $visitas_query->fetch_assoc();
          $data = $visita['data'];
          $dataFormatada = date("d/m/Y", strtotime($data));
          $hora = $visita['hora'];
          $horaFormatada = date("H:i", strtotime($hora));
          $local = $visita['local'];
          $rev = $visita['rev'];

          $latitude = $visita['cdx'];
          $longitude = $visita['cdy'];

          echo "
          <span>Data: $dataFormatada</span>
          <span class=\"mx-3\">Horário: $horaFormatada</span>
          <span>Local visitado: $local</span>
          ";
          ?>
        </div>

        <!-- Setas e Botões -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <i id="arrow-left" class="bi bi-arrow-left fs-2"></i>
          <?php if ($isAdmin || $rev === 'Pendente'): ?>
            <div>
              <button class="btn btn-success buttonConsultarAceitar" id="btnAceitar" <?php
              if ($rev === 'Aceito' && $isAdmin) {
                echo 'disabled';
              } else {
                echo '';
              }
              ?>>
                Aceitar
              </button>
              <button class="btn btn-danger ms-2 buttonConsultarRecusar" id="btnRecusar" <?php
              if ($rev === 'Não aceito' && $isAdmin) {
                echo 'disabled';
              } else {
                echo '';
              }
              ?>>
                Recusar
              </button>
            </div>
          <?php endif; ?>
          <i id="arrow-right" class="bi bi-arrow-right fs-2"></i>
        </div>

        <!-- Campo de recusa (escondido inicialmente) -->
        <div class="mt-3" id="recusaField"
          style="<?php echo ($rev === 'Não aceito') ? 'display: block;' : 'display: none;' ?>">
          <?php
          $sqlMotivo = "SELECT motivo FROM visita WHERE idfoto = $idvisita";
          $motivoQuery = $conexao->query($sqlMotivo);
          $motivo = $motivoQuery ? trim($motivoQuery->fetch_assoc()['motivo'] ?? '') : '';
          ?>
          <textarea class="form-control" rows="5" placeholder="Explique o motivo da recusa..." style="resize: none"
            <?php echo ($rev === 'Não aceito') ? 'readonly' : ''; ?>>
            <?php echo htmlspecialchars($motivo); ?>
        </textarea>

          <?php if ($rev !== 'Não aceito'): ?>
            <button class="btn btn-primary my-3 w-100 buttonCustom" id="btnEnviarRecusa">
              Enviar
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>
  <!-- FIM MAIN -->

  <!-- FOOTER -->
  <div class="container">
    <footer class="d-flex flex-wrap justify-content-center align-items-center mt-2 py-1 border-top border-dark">
      <div class="col-md-12 text-center">
        <span class="mb-3 mb-md-0 text-secondary-emphasis">© 2024 Bunny Boys, Inc</span>
      </div>
    </footer>
  </div>
  <!-- FIM FOOTER -->

  <!-- MODAL -->
  <div id="myModal" class="modal" style="display: none;">
    <div class="modal-content">
      <p id="modalMessage"></p>
      <button id="acceptBtn" class="buttonCustom">Ok</button>
    </div>
  </div>
  <!-- FIM MODAL -->

  <!-- BOOTSTRAP JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

  <!-- SCRIPT MAPS -->
  <script>
    function initMap() {
      const coordenadas = {
        lat: <?php echo $latitude; ?>,
        lng: <?php echo $longitude; ?>
      };

      const mapa = new google.maps.Map(document.getElementById("map"), {
        center: coordenadas,
        zoom: 15,
      });

      new google.maps.Marker({
        position: coordenadas,
        map: mapa,
        title: "Local da visita",
      });
    }
    window.onload = initMap;
  </script>
  <!-- FIM SCRIPT MAPS -->

  <!-- SCRIPT ACEITA/RECUSA VISITA -->
  <script>
    // Função para mostrar o modal
    function mostrarModal(message) {
      document.getElementById("modalMessage").innerText = message;
      document.getElementById("myModal").style.display = "block";
    }

    // Função para aceitar a visita
    function aceitarVisita(idvisita, rmalu, local) {
      const data = {
        idvisita: idvisita,
        rmalu: rmalu,
        local: local,
        rev: 'Aceito'
      };

      fetch('atualizarPontos.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const novosPontosMes = data.dadosAluno.pontmesGeral;
            const novosPontosAno = data.dadosAluno.pontanoGeral;

            document.getElementById('pontMes').textContent = `Pontos no mês: ${novosPontosMes}`;
            document.getElementById('pontAno').textContent = `Pontos no ano: ${novosPontosAno}`;

            mostrarModal("Visita aceita e pontos atualizados com sucesso!");
          } else {
            mostrarModal(data.message);
          }
        })
        .catch(error => console.error('Erro ao aceitar visita:', error));
    }

    document.addEventListener("DOMContentLoaded", function () {
      var modal = document.getElementById("myModal");

      // Botão de visita aceita
      const btnAceitar = document.getElementById("btnAceitar");
      if (btnAceitar) {
        btnAceitar.addEventListener("click", function () {
          document.getElementById("modalMessage").innerText = "Você aceitou.";
          modal.style.display = "block";

          btnAceitar.setAttribute("disabled", "");
          document.getElementById("btnRecusar").setAttribute("disabled", "");

          // Enviando dados via AJAX
          const rmalu = <?php echo $rmalu ?>;
          const local = "<?php echo $local ?>";
          const rev = "<?php echo $rev ?>";
          const idvisita = <?php echo $idvisita ?>;

          aceitarVisita(idvisita, rmalu, local);
        });
      }

      // Botão de visita recusada
      const btnRecusar = document.getElementById("btnRecusar");
      if (btnRecusar) {
        btnRecusar.addEventListener("click", function () {
          document.getElementById("recusaField").style.display = "block";
        });
      }

      // Botão de enviar recusa
      const btnEnviarRecusa = document.getElementById("btnEnviarRecusa");
      if (btnEnviarRecusa) {
        btnEnviarRecusa.addEventListener("click", function () {
          let motivo = document.querySelector("#recusaField textarea").value;
          if (motivo.trim() === "") {
            mostrarModal("Por favor, explique o motivo da recusa.");
          } else {
            let motivoCapturado = motivo;
            mostrarModal("Recusa enviada: " + motivoCapturado);
            document.querySelector(".form-control").setAttribute("disabled", "");
            btnEnviarRecusa.setAttribute("disabled", "");
            document.getElementById("btnAceitar").setAttribute("disabled", "");
            document.getElementById("btnRecusar").setAttribute("disabled", "");

            const rev = "<?php echo $rev ?>";
            const idvisita = <?php echo $idvisita ?>;
            const rmalu = <?php echo $rmalu ?>;
            const local = "<?php echo $local ?>";

            if (rev === "Aceito") {
              fetch('subtrairPontos.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                  idvisita: idvisita,
                  rmalu: rmalu,
                  motivo: motivoCapturado,
                  rev: rev
                })
              })
                .then(response => response.json())
                .then(data => {
                  if (data.success) {
                    document.getElementById('pontMes').textContent =
                      `Pontos no mês: ${data.dadosAluno.pontmesGeral}`;
                    document.getElementById('pontAno').textContent =
                      `Pontos no ano: ${data.dadosAluno.pontanoGeral}`;

                    mostrarModal(
                      "Visita recusada com sucesso e pontos reajustados!");
                  } else {
                    mostrarModal(data.message);
                  }
                })
                .catch(error => console.error('Erro ao subtrair pontos:', error));
            } else {
              fetch('visitaRecusada.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `rev=${rev}&idvisita=${idvisita}&motivo=${encodeURIComponent(motivoCapturado)}`
              })
                .then(response => response.json())
                .then(data => {
                  if (data.success) {
                    mostrarModal("Visita recusada com sucesso!");
                  } else {
                    mostrarModal("Erro ao recusar a visita.");
                  }
                })
                .catch(error => console.error('Erro ao recusar visita:', error));
            }
          }
        });
      }

      const acceptBtn = document.getElementById("acceptBtn");
      if (acceptBtn) {
        acceptBtn.onclick = function () {
          document.getElementById("modalMessage").innerText = '';
          modal.style.display = "none";
        };
      }

      window.onclick = function (event) {
        if (event.target == modal) {
          modal.style.display = "none";
        }
      };
    });
  </script>
  <!-- FIM SCRIPT ACEITA/RECUSA VISITA -->


  <!-- SCRIPT NAVEGACAO VISITAS -->
  <script>
    document.getElementById("arrow-left").addEventListener("click", function () {
      navigateTo("prev");
    });

    document.getElementById("arrow-right").addEventListener("click", function () {
      navigateTo("next");
    });

    function navigateTo(direction) {
      const currentIdVisita = <?php echo intval($idvisita); ?>;
      const rev = "<?php echo $rev; ?>";

      fetch(`navigateVisita.php?direction=${direction}&currentId=${currentIdVisita}&rev=${encodeURIComponent(rev)}  `)
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            const {
              idvisita,
              rmalu
            } = data;
            window.location.href = `?idvisita=${idvisita}&rmalu=${rmalu}&rev=${rev}`;
          } else {
            document.getElementById("modalMessage").innerText = direction === "prev" ?
              "Não há visitas pendentes anteriores." :
              "Não há visitas pendentes posteriores.";
            modal.style.display = "block";
          }
        })
        .catch((error) => console.error("Erro ao navegar:", error));
    }
  </script>
  <!-- FIM SCRIPT NAVEGACAO VISITAS -->
</body>

</html>