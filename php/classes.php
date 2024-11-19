<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passaporte Cultural - Professores</title>
    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
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
        <div class="col-lg-6 mx-auto">
            <h1 class="display-5 fw-bold text-body-emphasis fs-3 mb-4">
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
            </h1>
            <form action="" method="get" class="content">
                <div class="form-floating formFloatingCustom">
                    <select class="form-select selectCustom" id="classe" name="classe" aria-label="Floating label select example">
                        <option></option>
                        <option value="1eaa" name="1eaa">1° ADM A</option>
                        <option value="1eab" name="1eab">1° ADM B</option>
                        <option value="1dsa" name="1dsa">1° DS A</option>
                        <option value="1dsb" name="1dsb">1° DS B</option>
                        <option value="2eaa" name="2eaa">2º ADM A</option>
                        <option value="2eab" name="2eab">2º ADM B</option>
                        <option value="2dsa" name="2dsa">2º DS A</option>
                        <option value="2dsb" name="2dsb">2º DS B</option>
                        <option value="3eaa" name="3eaa">3º ADM A</option>
                        <option value="3eab" name="3eab">3º ADM B</option>
                        <option value="3dsa" name="3dsa">3º DS A</option>
                        <option value="3dsb" name="3DSB">3º DS B</option>
                    </select>
                    <label for="classe" class="labelSelectCustom">Selecione uma sala</label>
                </div>
                <button class="btn btn-outline-info m-3 btnCustom" type="submit">Enviar</button>
            </form>
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th>RM</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Pontos</th>
                            <th colspan=" 2">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "conexao.php";
                        $classe = $_GET["classe"] ?? "";
                        $query = mysqli_query($conexao, "SELECT * FROM alunos WHERE nometur='$classe' ORDER BY nomealu ASC;");
                        while ($row = mysqli_fetch_array($query)) {
                            $rmalu = $row['rmalu'];
                            $nomealu = $row['nomealu'];
                            $emailalu = $row['emailalu'];
                            $pontano = $row['pontano'];
                            echo "
                        
                    <tr>
                        <td>$rmalu</td>
                        <td>$nomealu</td>
                        <td>$emailalu</td>
                        <td>$pontano</td>
                        <td><a href=\"editar.php?codigo=$rmalu\" class=\"link-offset-2 link-offset-3-hover link-dark link-underline link-underline-opacity-0 link-underline-opacity-75-hover\">[Editar]</a></td>
                        <td><a href=\"excluir.php?codigo=$rmalu\" class=\"link-offset-2 link-offset-3-hover link-dark link-underline link-underline-opacity-0 link-underline-opacity-75-hover\">[Excluir]</a></td>
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
    <div class="container">
        <footer
            class="d-flex flex-wrap justify-content-center align-items-center py-1 border-top border-dark">
            <div class="col-md-12 text-center">
                <span class="mb-3 mb-md-0 text-secondary-emphasis">© 2024 Bunny Boys, Inc</span>
            </div>
        </footer>
    </div>
    <!-- FIM FOOTER -->

    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>