<?php
session_start();
require 'conexao.php';

// Verifica se o cliente está logado
if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit;
}

// Inicializa mensagens de erro/sucesso
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produtos = $_POST['produtos'] ?? []; // Array com produtos e quantidades
    $codigo_cupom = $_POST['codigo_cupom'] ?? null;
    $total = 0;

    try {
        $pdo->beginTransaction();

        // Calcula o total do pedido
        foreach ($produtos as $produto) {
            $total += $produto['quantidade'] * $produto['preco_unitario'];
        }

        // Verifica se um cupom foi aplicado
        if (!empty($codigo_cupom)) {
            $stmt = $pdo->prepare("SELECT * FROM cupons WHERE codigo = :codigo AND validade >= NOW()");
            $stmt->execute(['codigo' => $codigo_cupom]);
            $cupom = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cupom) {
                $desconto = ($cupom['desconto'] / 100) * $total;
                $total -= $desconto;
            } else {
                $mensagem = "Cupom inválido ou expirado!";
            }
        }

        // Cria o pedido no banco
        $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, total) VALUES (:cliente_id, :total)");
        $stmt->execute([
            'cliente_id' => $_SESSION['cliente_id'],
            'total' => $total
        ]);

        $pedido_id = $pdo->lastInsertId();

        // Insere os itens do pedido
        foreach ($produtos as $produto) {
            $stmt = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_nome, quantidade, preco_unitario) 
                                   VALUES (:pedido_id, :produto_nome, :quantidade, :preco_unitario)");
            $stmt->execute([
                'pedido_id' => $pedido_id,
                'produto_nome' => $produto['nome'],
                'quantidade' => $produto['quantidade'],
                'preco_unitario' => $produto['preco_unitario']
            ]);
        }

        $pdo->commit();
        $mensagem = "Pedido realizado com sucesso! Total: R$ " . number_format($total, 2);
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensagem = "Erro ao processar o pedido: " . $e->getMessage();
    }
}
?>

