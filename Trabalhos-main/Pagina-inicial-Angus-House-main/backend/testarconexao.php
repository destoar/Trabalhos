<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'angus_house';
$username = 'root';
$password = '123456';

try {
    echo "Tentando conectar ao MySQL...<br>";
    
    // Primeiro tenta conectar sem selecionar o banco
    $pdo = new PDO("mysql:host=$host", $username, $password);
    echo "Conexão ao MySQL bem-sucedida!<br>";
    
    // Verifica se o banco existe
    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    if(!$stmt->fetch()) {
        echo "Criando banco de dados $dbname...<br>";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        echo "Banco de dados criado com sucesso!<br>";
    }
    
    // Conecta ao banco específico
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão ao banco $dbname bem-sucedida!<br>";
    
} catch (PDOException $e) {
    echo 'Detalhes do erro:<br>';
    echo 'Mensagem: ' . $e->getMessage() . '<br>';
    echo 'Código: ' . $e->getCode() . '<br>';
    echo 'Arquivo: ' . $e->getFile() . '<br>';
    echo 'Linha: ' . $e->getLine() . '<br>';
}
?>
