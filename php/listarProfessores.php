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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passaporte Cultural Digital | Listagem de professores</title>
    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <!-- BOOTSTRAP ICONS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
    <!-- HEADER -->
    <nav class="navbar navbar-custom navbar-expand-lg border-body" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand fs-4" href="../index.php">Passaporte Cultural Digital</a>
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
                                    <a class="nav-link active" href="#">Professores</a>
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
        <div class="col-lg-6 mx-auto">
            <h1 class="display-5 fw-bold text-body-emphasis fs-3 mb-4">Professores</h1>
            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered border-dark table-hover mb-0 custom-table align-middle">
                    <thead class="sticky-top text-center">
                        <tr>
                            <th scope="col">RM</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Email</th>
                            <th colspan="2" scope="colgroup">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php
                        include "conexao.php";

                        $sqlcode = "SELECT * FROM professor";
                        $sqlquery = $conexao->query($sqlcode);

                        if ($sqlquery->num_rows > 0) {
                            while ($professor = $sqlquery->fetch_assoc()) {
                                $rmprof = $professor['rmprof'];

                                $nomeprof = $professor['nomeprof'];
                                $emailprof = $professor['emailprof'];

                                echo "
                                <tr>
                                    <td>$rmprof</td>
                                    <td>$nomeprof</td>
                                    <td>$emailprof</td>
                                    <td>
                                        <a class='link-offset-2 link-offset-3-hover link-dark link-underline link-underline-opacity-0 link-underline-opacity-75-hover' onclick=\" openModal('edit', { rmprof: '$rmprof', nomeprof: '$nomeprof' ,emailprof: '$emailprof' }) \" style='cursor: pointer;'>Editar</a>
                                    </td>
                                    <td>
                                        <a class='link-offset-2 link-offset-3-hover link-dark link-underline link-underline-opacity-0 link-underline-opacity-75-hover' onclick=\" openModal('delete', { rmprof: '$rmprof' }) \" style='cursor: pointer;'>Excluir</a>
                                    </td>
                                </tr>
                                ";
                            }
                        } else {
                            echo "
                                <tr>
                                    <td colspan='5' class='text-center'>Nenhum professor cadastrado</td>
                                </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-primary w-50 py-2 buttonCustom mt-3" onclick="openModal('create')">Adicionar
                Professor</button>
        </div>
    </main>
    <!-- FIM MAIN -->

    <!-- FOOTER -->
    <div class="container">
        <footer
            class="d-flex flex-column flex-md-row justify-content-between align-items-center text-center mt-2 py-1 border-top border-dark">
            <!-- Texto centralizado -->
            <span class="mb-3 mb-md-0">© 2024 Passaporte Cultural Digital</span>

            <!-- Logo do GitHub -->
            <ul class="nav justify-content-center justify-content-md-end">
                <li class="ms-3">
                    <a class="text-body-secondary" href="https://github.com/TCCdeParede" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                            class="bi bi-github" viewBox="0 0 16 16">
                            <path
                                d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8">
                            </path>
                        </svg>
                    </a>
                </li>
            </ul>
        </footer>
    </div>
    <!-- FIM FOOTER -->

    <!-- MODAL -->
    <div id="myModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3 id="modalTitle"></h3>
            <div id="modalMessage" class="alert d-none"></div>
            <div id="modalBody"></div>
            <div id="modalFooter" class="mt-2 d-flex justify-content-evenly">
                <button id="modalConfirmBtn" class="btn btn-primary buttonCustom">Confirmar</button>
                <button id="modalCancelBtn" class="btn btn-secondary buttonCustom"
                    onclick="closeModal()">Cancelar</button>
            </div>
        </div>
    </div>
    <!-- FIM MODAL -->

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
            clearModalMessage();
            modalConfirmBtn.onclick = handleModalConfirm;

            if (action === "edit") {
                modalTitle.textContent = "Editar Professor";
                modalBody.innerHTML = `
                <label for="rmprof">RM:</label>
                <input class='form-control' type="text" id="rmprof" value="${studentData.rmprof}" disabled>
                <label for="nomeprof">Nome:</label>
                <input class='form-control' type="text" id="nomeprof" value="${studentData.nomeprof}">
                <label for="emailprof">Email:</label>
                <input class='form-control' type="email" id="emailprof" value="${studentData.emailprof}">
            `;
            } else if (action === "create") {
                modalTitle.textContent = "Adicionar Novo Professor";
                modalBody.innerHTML = `
                <label for="rmprof">RM:</label>
                <input class='form-control' type="text" id="rmprof">
                <label for="nomeprof">Nome:</label>
                <input class='form-control' type="text" id="nomeprof">
                <label for="emailprof">Email:</label>
                <input class='form-control' type="email" id="emailprof">
            `;
            } else if (action === "delete") {
                modalTitle.textContent = "Excluir Professor";
                modalBody.innerHTML = `<p>Tem certeza de que deseja excluir o registro do professor de RM ${studentData.rmprof}?</p>`;
                currentStudentId = studentData.rmprof;
            }

            modal.style.display = "block";
        }

        function closeModal() {
            const modal = document.getElementById("myModal");
            modal.style.display = "none";
            document.body.classList.remove('modal-active');
            currentAction = null;
            currentStudentId = null;
            clearModalMessage();
        }

        function showModalMessage(message, type = "success") {
            const modalMessage = document.getElementById("modalMessage");
            modalMessage.textContent = message;
            modalMessage.className = `alert alert-${type} mt-2`;
            modalMessage.classList.remove("d-none");
        }

        function clearModalMessage() {
            const modalMessage = document.getElementById("modalMessage");
            modalMessage.className = "alert d-none";
            modalMessage.textContent = "";
        }

        function handleModalConfirm() {
            clearModalMessage();
            const modalConfirmBtn = document.getElementById("modalConfirmBtn");
            modalConfirmBtn.disabled = true;

            if (currentAction === "edit" || currentAction === "create") {
                const rmprof = document.getElementById("rmprof").value;
                const nomeprof = document.getElementById("nomeprof").value;
                const emailprof = document.getElementById("emailprof").value;

                const formData = new FormData();
                formData.append("rmprof", rmprof);
                formData.append("nomeprof", nomeprof);
                formData.append("emailprof", emailprof);

                const url = currentAction === "edit" ? "editarProfessor.php" : "adicionarProfessor.php";
                fetch(url, {
                    method: "POST",
                    body: formData,
                })
                    .then(response => response.json())
                    .then(data => {
                        showModalMessage(data.message, data.success ? "success" : "danger");
                        if (data.success) {
                            setTimeout(() => {
                                closeModal();
                                window.location.reload();
                            }, 1500);
                        } else {
                            modalConfirmBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error("Erro:", error);
                        showModalMessage("Ocorreu um erro ao tentar processar a requisição.", "danger");
                        modalConfirmBtn.disabled = false;
                    });
            } else if (currentAction === "delete") {
                fetch("deletarProfessor.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `rmprof=${encodeURIComponent(currentStudentId)}`,
                })
                    .then(response => response.json())
                    .then(data => {
                        showModalMessage(data.message, data.success ? "success" : "danger");
                        if (data.success) {
                            setTimeout(() => {
                                closeModal();
                                window.location.reload();
                            }, 1500);
                        } else {
                            modalConfirmBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error("Erro:", error);
                        showModalMessage("Erro ao tentar excluir o professor.", "danger");
                        modalConfirmBtn.disabled = false;
                    });
            }
        }
    </script>


    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
</body>

</html>