<?php
header('Content-Type: application/json');

// Ativa exibição de erros para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexão com o banco de dados
$conexao = require_once 'conexao.php';

// Mensagem de erro inicial
$response = ['status' => 'error', 'message' => 'Login falhou.'];

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Requisição POST recebida.");

    // Verifica se os dados foram enviados
    if (isset($_POST['email']) && isset($_POST['senha'])) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Valida o formato do email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Formato de email inválido.';
            echo json_encode($response);
            exit;
        }

        try {
            // Mensagem de sucesso ao conectar ao banco de dados
            error_log("Conexão ao banco de dados bem-sucedida.");

            // Busca o usuário no banco de dados
            $stmt = $conexao->prepare("SELECT * FROM clientes WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            // Verifica se o usuário existe e a senha está correta
            if ($user && password_verify($senha, $user['senha'])) {
                $response = [
                    'status' => 'success',
                    'message' => 'Login bem-sucedido.',
                    'user' => [
                        'id' => $user['id'],
                        'nome' => $user['nome'],
                        'email' => $user['email'],
                        'telefone' => $user['telefone'] ?? null
                    ]
                ];
            } else {
                $response['message'] = 'Credenciais inválidas.';
            }
        } catch (PDOException $e) {
            // Captura qualquer erro de conexão ou consulta
            error_log("Erro ao consultar banco de dados: " . $e->getMessage());
            $response['message'] = 'Erro ao conectar ao banco de dados. Tente novamente mais tarde.';
        }
    } else {
        $response['message'] = 'Por favor, forneça o email e a senha.';
    }
}

// Retorna a resposta em formato JSON
echo json_encode($response);
?>