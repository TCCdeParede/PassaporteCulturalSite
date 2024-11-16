<?php
// Conectar ao banco de dados
$host = "localhost";
$dbname = "passaporte_cultural"; // Substitua pelo nome do seu banco de dados
$username = "root"; // Seu usuário do banco de dados (geralmente é root no WAMP)
$password = ""; // Sua senha do banco de dados (geralmente está vazia no WAMP)
$conn = new mysqli($host, $username, $password, $dbname);

// Checar a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Pegar dados do POST
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Preparar a consulta para buscar o usuário no banco de dados
    $stmt = $conn->prepare("SELECT * FROM alunos WHERE emailalu = ? AND alusenha = ?");
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Se encontrar o usuário, retorna sucesso
        echo json_encode(["status" => "success", "message" => "Login bem-sucedido"]);
    } else {
        // Caso contrário, retorna erro
        echo json_encode(["status" => "error", "message" => "Credenciais inválidas"]);
    }

    $stmt->close();
    $conn->close();
}
?>
