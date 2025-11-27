<?php
header('Content-Type: application/json'); // Retorna JSON

// Conexão com o banco de dados (substitua com seus dados)
$host = "localhost";
$db   = "senac5";
$user = "seu_cliente";
$pass = "senac";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}

// Função para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Captura os dados enviados pelo fetch
$action = $_POST['action'] ?? '';

switch($action) {

    case 'list':
        $stmt = $pdo->query("SELECT id, nome, cpfcnpj, email FROM cliente"); // Não retorna senha
        $cliente = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($cliente);
        break;

    case 'create':
        $nome = trim($_POST['nome'] ?? '');
        $cpfcnpj = trim($_POST['cpfcnpj'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        // Validação simples
        if (!$nome || !$cpfcnpj || !$email || !$senha) {
            echo json_encode(["error" => "Todos os campos são obrigatórios"]);
            exit;
        }
        if (!validarEmail($email)) {
            echo json_encode(["error" => "Email inválido"]);
            exit;
        }

        // Hash da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO cliente (nome, cpfcnpj, email, senha) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $cpfcnpj, $email, $senhaHash]);
        echo json_encode(["success" => true]);
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $nome = trim($_POST['nome'] ?? '');
        $cpfcnpj = trim($_POST['cpfcnpj'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (!$id || !$nome || !$cpfcnpj || !$email) {
            echo json_encode(["error" => "ID, nome, CPF/CNPJ e email são obrigatórios"]);
            exit;
        }
        if (!validarEmail($email)) {
            echo json_encode(["error" => "Email inválido"]);
            exit;
        }

        if ($senha) {
            // Atualiza a senha somente se foi preenchida
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE cliente SET nome=?, cpfcnpj=?, email=?, senha=? WHERE id=?");
            $stmt->execute([$nome, $cpfcnpj, $email, $senhaHash, $id]);
        } else {
            // Não altera a senha
            $stmt = $pdo->prepare("UPDATE cliente SET nome=?, cpfcnpj=?, email=? WHERE id=?");
            $stmt->execute([$nome, $cpfcnpj, $email, $id]);
        }

        echo json_encode(["success" => true]);
        break;

    case 'delete':
        $id = $_POST['id'] ?? '';
        if (!$id) {
            echo json_encode(["error" => "ID é obrigatório"]);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM cliente WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(["success" => true]);
        break;

    default:
        echo json_encode(["error" => "Ação inválida"]);
        break;
}
