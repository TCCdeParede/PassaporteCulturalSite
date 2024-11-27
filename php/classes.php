<?php

session_start();

if (!isset($_SESSION["tipoLogin"])) {
    header("Location: ./logout.php");
    exit();
}

$isAdmin = $_SESSION['tipoLogin'] === 'administrador';

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passaporte Cultural | Classes</title>
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
            <h3 class="mt-3">
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
            </h3>
            <div class="table-responsive mt-3" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-striped table-bordered border-dark table-hover mb-0 custom-table caption-top">
                    <thead class="sticky-top align-middle">
                        <tr>
                            <th rowspan="2" scope="col">RM</th>
                            <th rowspan="2" scope="col">Nome</th>
                            <th rowspan="2" scope="col">Email</th>
                            <?php if ($isAdmin) {
                                echo "<th rowspan='2' colspan='2' scope='colgroup'>Ações</th>";
                            } ?>
                            <th rowspan="1" colspan="2" scope="colgroup">Pontuação Geral:</th>
                            <th rowspan="1" colspan="2" scope="colgroup">Pontuação a Computar:</th>
                        </tr>
                        <tr>
                            <th>Anual</th>
                            <th>Mês</th>
                            <th>Anual</th>
                            <th>Mês</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "conexao.php";

                        $classe = $_GET["classe"] ?? "";
                        if (empty($classe)) {
                            if ($isAdmin) {
                                echo "
                    <tr>
                        <td colspan='11' class='text-center'>Selecione uma sala</td>
                    </tr>";
                            } else {
                                echo "
                    <tr>
                        <td colspan='7' class='text-center'>Selecione uma sala</td>
                    </tr>";
                            }
                        } else {
                            $query = mysqli_query($conexao, "
                SELECT 
                    a.rmalu, 
                    a.nomealu, 
                    a.emailalu, 
                    a.pontanoGeralAluno, 
                    a.pontmesGeralAluno, 
                    a.pontcompanoAluno, 
                    a.pontcompmesAluno
                FROM alunos a
                WHERE a.nometur = '$classe'
                ORDER BY a.nomealu ASC
            ");

                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_array($query)) {
                                    $rmalu = $row['rmalu'];
                                    $nomealu = $row['nomealu'];
                                    $emailalu = $row['emailalu'];
                                    $pontGeralAno = $row['pontanoGeralAluno'];
                                    $pontGeralMes = $row['pontmesGeralAluno'];
                                    $pontCompAno = $row['pontcompanoAluno'];
                                    $pontCompMes = $row['pontcompmesAluno'];

                                    echo "
                    <tr>
                        <td>$rmalu</td>
                        <td>$nomealu</td>
                        <td>$emailalu</td>";
                                    if ($isAdmin) {
                                        echo "
                        <td>
                            <a class='link-offset-2 link-offset-3-hover link-dark link-underline link-underline-opacity-0 link-underline-opacity-75-hover' onclick=\"openModal('edit', { rmalu: '$rmalu', nomealu: '$nomealu', emailalu: '$emailalu', nometur: '$classe'})\" style='cursor: pointer;'>Editar</a>
                        </td>
                        <td>
                            <a class='link-offset-2 link-offset-3-hover link-dark link-underline link-underline-opacity-0 link-underline-opacity-75-hover' onclick=\"openModal('delete', { rmalu: '$rmalu' })\" style='cursor: pointer;'>Excluir</a>
                        </td>";
                                    }
                                    echo "
                        <td>$pontGeralAno</td>
                        <td>$pontGeralMes</td>
                        <td>$pontCompAno</td>
                        <td>$pontCompMes</td>
                    </tr>";
                                }
                            } else {
                                $colspan = $isAdmin ? 11 : 7;
                                echo "
                <tr>
                    <td colspan='$colspan' class='text-center'>Nenhum aluno encontrado</td>
                </tr>";
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot class="sticky-bottom">
                        <?php
                        $sqlcode_turma = "
            SELECT pontcompmensalTurma, pontcompgeralTurma, pontmesGeralTurma, pontanualGeralTurma FROM turma WHERE nometur = '$classe'";
                        $turma_query = $conexao->query($sqlcode_turma);
                        $turma = $turma_query->fetch_assoc();
                        $pontGeralMensalTurma = $turma["pontmesGeralTurma"] ?? "0";
                        $pontGeralAnualTurma = $turma["pontanualGeralTurma"] ?? "0";
                        $pontCompMensal = $turma['pontcompmensalTurma'] ?? "0";
                        $pontCompGeral = $turma['pontcompgeralTurma'] ?? "0";

                        $colspan = $isAdmin ? 5 : 3;
                        echo "
        <tr>
            <th colspan='$colspan' scope='row'>Pontuação da Sala: </th>
            <td>$pontGeralAnualTurma</td>
            <td>$pontGeralMensalTurma</td>
            <td>$pontCompGeral</td>
            <td>$pontCompMensal</td>
        </tr>";
                        ?>
                    </tfoot>
                </table>

            </div>

            <?php if ($isAdmin): ?>
                <button class="btn btn-primary w-50 py-2 buttonCustom mt-3" onclick="openModal('create')">Adicionar Aluno</button>
            <?php endif; ?>
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

    <!-- MODAL -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle"></h3>
            <div id="modalBody">
                <!-- O conteúdo do modal será dinamicamente preenchido -->
            </div>
            <div id="modalFooter" class="mt-2 d-flex justify-content-evenly">
                <button id="modalConfirmBtn" class="btn btn-primary buttonCustom">Confirmar</button>
                <button id="modalCancelBtn" class="btn btn-secondary buttonCustom" onclick="closeModal()">Cancelar</button>
            </div>
        </div>
    </div>
    <!-- FIM MODAL -->

    <!-- ABRIR/FECHAR MODAL -->
    <script>
        let currentAction = null;
        let currentStudentId = null;

        function openModal(action, studentData = null) {
            currentAction = action;
            document.body.classList.add('modal-active');
            const modal = document.getElementById("myModal");
            const modalTitle = document.getElementById("modalTitle");
            const modalBody = document.getElementById("modalBody");
            const modalConfirmBtn = document.getElementById("modalConfirmBtn");

            modalBody.innerHTML = "";
            modalConfirmBtn.onclick = handleModalConfirm;

            if (action === "edit") {
                modalTitle.textContent = "Editar Aluno";
                modalBody.innerHTML = `
                <label for="rmalu">RM:</label>
                <input class='form-control' type="text" id="rmalu" value="${studentData.rmalu}" disabled>
                <label for="nomealu">Nome:</label>
                <input class='form-control' type="text" id="nomealu" value="${studentData.nomealu}">
                <label for="emailalu">Email:</label>
                <input class='form-control' type="email" id="emailalu" value="${studentData.emailalu}">
                <label for="nometur">Turma:</label>
                <input class='form-control' type="text" id="nometur" value="${studentData.nometur}">
        `;
            } else if (action === "create") {
                modalTitle.textContent = "Adicionar Novo Aluno";
                modalBody.innerHTML = `
                <label for="rmalu">RM:</label>
                <input class='form-control' type="text" id="rmalu">
                <label for="nomealu">Nome:</label>
                <input class='form-control' type="text" id="nomealu">
                <label for="emailalu">Email:</label>
                <input class='form-control' type="email" id="emailalu">
                <label for="nometur">Turma:</label>
                <input class='form-control' type="text" id="nometur">
        `;
            } else if (action === "delete") {
                modalTitle.textContent = "Excluir Aluno";
                modalBody.innerHTML = `<p>Tem certeza de que deseja excluir o registro do aluno de RM ${studentData.rmalu}?</p>`;
                currentStudentId = studentData.rmalu;
            }

            modal.style.display = "block";
        }

        function closeModal() {
            document.body.classList.remove('modal-active');
            const modal = document.getElementById("myModal");
            modal.style.display = "none";
            currentAction = null;
            currentStudentId = null;
        }

        function handleModalConfirm() {
            if (currentAction === "edit" || currentAction === "create") {
                const rmalu = document.getElementById("rmalu").value;
                const nomealu = document.getElementById("nomealu").value;
                const emailalu = document.getElementById("emailalu").value;
                const nometur = document.getElementById("nometur").value;

                const formData = new FormData();
                formData.append("rmalu", rmalu);
                formData.append("nomealu", nomealu);
                formData.append("emailalu", emailalu);
                formData.append("nometur", nometur);

                const url = currentAction === "edit" ? "editarAluno.php" : "adicionarAluno.php";
                fetch(url, {
                        method: "POST",
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) {
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error("Erro:", error);
                        alert("Ocorreu um erro ao tentar processar a requisição.");
                    });
            } else if (currentAction === "delete") {
                fetch("deletarAluno.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `rmalu=${encodeURIComponent(currentStudentId)}`,
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) {
                            window.location.reload();
                        } else {
                            console.error('Erro: ', data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Erro:", error);
                        alert("Erro ao tentar excluir o aluno.");
                    });
            }

            closeModal();
        }
    </script>
    <!-- FIM ABRIR/FECHAR MODAL -->

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