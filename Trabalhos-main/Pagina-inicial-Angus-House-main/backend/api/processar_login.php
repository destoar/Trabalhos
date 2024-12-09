<?php
session_start();
include 'conexao.php'; // Arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

    if ($email && $senha) {
        // Consulta ao banco de dados
        $query = $conexao->prepare('SELECT id, nome, senha FROM clientes WHERE email = :email');
        $query->bindParam(':email', $email);
        $query->execute();

        $usuario = $query->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Sessão iniciada com sucesso
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            
            // Redireciona para a página de pedidos
            header('Location: pedidos.php');
            exit;
        } else {
            echo '<script>alert("Email ou senha inválidos."); window.location.href="login.html";</script>';
        }
    } else {
        echo '<script>alert("Preencha todos os campos!"); window.location.href="login.html";</script>';
    }
} else {
    header('Location: login.html');
    exit;
}
?>
