<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passaporte Cultural - Professores</title>
    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
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
                            <a class="nav-link active" href="#">Classes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./listarVisitas.php">Visitas</a>
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
    <main class="p-4 my-4 text-center">
        <div class="col-lg-8 mx-auto">
            <form action="" method="get" class="content">
                <div class="form-floating formFloatingCustom">
                    <select class="form-select selectCustom" id="classe" name="classe"
                        aria-label="Floating label select example" onchange="updateClass(this.value)">
                        <option></option>
                        <option value="1eaa" name="1eaa">1EAA</option>
                        <option value="1eab" name="1eab">1EAB</option>
                        <option value="1dsa" name="1dsa">1DSA</option>
                        <option value="1dsb" name="1dsb">1DSB</option>
                        <option value="2eaa" name="2eaa">2EAA</option>
                        <option value="2eab" name="2eab">2EAB</option>
                        <option value="2dsa" name="2dsa">2DSA</option>
                        <option value="2dsb" name="2dsb">2DSB</option>
                        <option value="3eaa" name="3eaa">3EAA</option>
                        <option value="3eab" name="3eab">3EAB</option>
                        <option value="3dsa" name="3dsa">3DSA</option>
                        <option value="3dsb" name="3DSB">3DSB</option>
                    </select>
                    <label for="classe" class="labelSelectCustom">Selecione uma sala</label>
                </div>
            </form>
            <div class="table-responsive mt-3" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-striped table-bordered border-dark table-hover mb-0 custom-table caption-top">
                    <caption class="h3">
                        <?php
                        $options = [
                            "1eaa" => "1º ADM A",
                            "1eab" => "1° ADM B",
                            "1dsa" => "1° DS A",
                            "1dsb" => "1° DS B",
                            "2eaa" => "2º ADM A",
                            "2eab" => "2º ADM B",
                            "2dsa" => "2º DS A",
                            "2dsb" => "2º DS B",
                            "3eaa" => "3º ADM A",
                            "3eab" => "3º ADM B",
                            "3dsa" => "3º DS A",
                            "3dsb" => "3º DS B"
                        ];

                        $sala = $_GET['classe'] ?? '';
                        $nomeSala = $options[$sala] ?? '';

                        echo $nomeSala;
                        ?>
                    </caption>
                    <thead class="sticky-top align-middle">
                        <tr>
                            <th rowspan="2" scope="col">RM</th>
                            <th rowspan="2" scope="col">Nome</th>
                            <th rowspan="2" scope="col">Email</th>
                            <th rowspan="1" colspan="2" scope="colgroup">Pontuação no mês:</th>
                            <th rowspan="2" scope="col">Pontos no ano</th>
                        </tr>
                        <tr>
                            <th>Passado</th>
                            <th>Atual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "conexao.php";

                        $classe = $_GET["classe"] ?? "";
                        $mesAtual = date('m');
                        $anoAtual = date('Y');
                        $mesAnterior = $mesAtual - 1;
                        $anoAnterior = $anoAtual - 1;

                        if ($mesAnterior === 0) {
                            $mesAnterior = 12;
                            $anoAnterior -= 1;
                        }

                        if (empty($classe)) {
                            echo "
                                    <tr>
                                        <td colspan='6' class='text-center'>Selecione uma sala</td>
                                    </tr>";
                        } else {
                            $query = mysqli_query($conexao, "SELECT a.rmalu, a.nomealu, a.emailalu, a.pontmes AS pontos_atual,
                            COALESCE(SUM(
                                CASE
                                    WHEN MONTH(v.data) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                    AND YEAR(v.data) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) THEN
                                        CASE
                                            WHEN v.local IN ('Show', 'Teatro', 'Feira') THEN 20
                                            WHEN v.local IN ('Centro Histórico', 'Museu', 'Visita Técnica') THEN 15
                                            WHEN v.local IN ('Exposição', 'Cinema') THEN 10
                                            WHEN v.local IN ('Biblioteca', 'Evento Esportivo') THEN 5
                                            ELSE 0
                                        END
                                    ELSE 0
                                END
                            ), 0) AS pontos_anterior,
                            a.pontano
                        FROM
                            alunos a
                        LEFT JOIN
                            visita v
                        ON
                            a.rmalu = v.rmalu
                        WHERE
                            a.nometur = '$classe'
                        GROUP BY
                            a.rmalu, a.nomealu, a.emailalu, a.pontmes, a.pontano
                        ORDER BY
                            a.nomealu ASC;");

                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_array($query)) {
                                    $rmalu = $row['rmalu'];
                                    $nomealu = $row['nomealu'];
                                    $emailalu = $row['emailalu'];
                                    $pontosAtual = $row['pontos_atual'];
                                    $pontosAnterior = $row['pontos_anterior'];
                                    $pontano = $row['pontano'];
                                    echo "
                                        <tr>
                                            <td>$rmalu</td>
                                            <td>$nomealu</td>
                                            <td>$emailalu</td>
                                            <td>$pontosAnterior</td>
                                            <td>$pontosAtual</td>
                                            <td>$pontano</td>
                                        </tr>";
                                }
                            } else {
                                echo "
                                        <tr>
                                            <td colspan='6' class='text-center'>Nenhum aluno encontrado</td>
                                        </tr>";
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot class="sticky-bottom">
                        <?php
                        $sqlcode_turma = "SELECT pontjust FROM turma WHERE nometur = '$classe'";
                        $turma_query = $conexao->query($sqlcode_turma);
                        $turma = $turma_query->fetch_assoc();
                        $pontjust = $turma['pontjust'] ?? "";
                        echo "
                                    <tr>
                                        <th colspan=5 scope='row'>Pontuação da sala no ano: </th>
                                        <td>$pontjust</td>
                                    </tr>
                                ";
                        ?>
                    </tfoot>
                </table>
            </div>
        </div>
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

    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        function updateClass(classe) {
            if (classe) {
                window.location.href = `?classe=${classe}`;
            }
        }
    </script>
</body>

</html>