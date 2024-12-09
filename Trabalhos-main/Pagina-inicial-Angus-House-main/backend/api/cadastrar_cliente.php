<?php
// Ativa exibição de erros para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define o tipo de resposta como JSON
header('Content-Type: application/json');

// Conexão com o banco de dados
$conexao = require_once 'conexao.php';

// Função para registrar logs de erro
function logError($message) {
    error_log($message);
}

// Teste inicial da conexão
try {
    $teste = $conexao->query("SELECT 1");
    error_log("Conexão teste realizada com sucesso");
} catch (Exception $e) {
    error_log("Erro na conexão teste: " . $e->getMessage());
    die(json_encode(['status' => 'error', 'message' => 'Erro na conexão com o banco de dados']));
}

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Log dos dados recebidos
    error_log("POST Data: " . print_r($_POST, true));
    
    // Verifica se os campos obrigatórios estão presentes
    $camposObrigatorios = ['nome', 'email', 'senha', 'confirmarSenha', 'logradouro', 'numero', 'bairro', 'cidade', 'estado'];
    
    foreach ($camposObrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            logError("Campo obrigatório faltando: $campo");
            echo json_encode(['status' => 'error', 'message' => "O campo $campo é obrigatório."]);
            exit;
        }
    }

    // Recupera e sanitiza os dados do formulário
    $nome = htmlspecialchars(trim($_POST['nome']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmarSenha'];
    $logradouro = htmlspecialchars(trim($_POST['logradouro']));
    $numero = htmlspecialchars(trim($_POST['numero']));
    $bairro = htmlspecialchars(trim($_POST['bairro']));
    $cidade = htmlspecialchars(trim($_POST['cidade']));
    $estado = htmlspecialchars(trim($_POST['estado']));

    // Verifica se as senhas coincidem
    if ($senha !== $confirmarSenha) {
        logError("Senhas não coincidem");
        echo json_encode(['status' => 'error', 'message' => 'As senhas não coincidem.']);
        exit;
    }

    // Validação de e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logError("E-mail inválido: $email");
        echo json_encode(['status' => 'error', 'message' => 'E-mail inválido.']);
        exit;
    }

    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        // Verifica se o e-mail já está registrado
        $stmt = $conexao->prepare("SELECT COUNT(*) FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            logError("E-mail já registrado: $email");
            echo json_encode(['status' => 'error', 'message' => 'Este e-mail já está registrado.']);
            exit;
        }

        // Insere os dados no banco de dados
        $stmt = $conexao->prepare("
            INSERT INTO clientes (nome, email, senha, logradouro, numero, bairro, cidade, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Log dos dados que serão inseridos
        logError("Preparando inserção para o usuário: $nome");

        $resultado = $stmt->execute([
            $nome,
            $email,
            $senhaHash,
            $logradouro,
            $numero,
            $bairro,
            $cidade,
            $estado
        ]);

        if ($resultado) {
            logError("Cadastro realizado com sucesso para: $email");
            echo json_encode([
                'status' => 'success',
                'message' => 'Cadastro realizado com sucesso!'
            ]);
        } else {
            logError("Falha ao inserir dados. Detalhes do erro: " . print_r($stmt->errorInfo(), true));
            echo json_encode([
                'status' => 'error',
                'message' => 'Erro ao cadastrar cliente.'
            ]);
        }
    } catch (Exception $e) {
        logError("Exceção capturada: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao cadastrar cliente: ' . $e->getMessage()
        ]);
    }
} else {
    logError("Método de requisição inválido");
    echo json_encode(['status' => 'error', 'message' => 'Método inválido. Use POST.']);
}
?>