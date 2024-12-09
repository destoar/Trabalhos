<?php
$host = 'localhost';  // O host do banco de dados
$dbname = 'angus_house';  // Nome do banco de dados
$username = 'root';  // Seu nome de usuário do banco
$password = '123456';  // Sua senha do banco

try {
    // Criando a conexão com o banco de dados
    $conexao = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configura o PDO para lançar exceções em caso de erro
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Log de sucesso da conexão
    error_log("Conexão com o banco de dados estabelecida com sucesso");
} catch (PDOException $e) {
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    die("Falha na conexão: " . $e->getMessage());
}

return $conexao;
?>
