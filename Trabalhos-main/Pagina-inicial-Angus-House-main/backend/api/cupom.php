<?php
require_once __DIR__ . '/../database/Connection.php';
require_once __DIR__ . '/../config/Utils.php';
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

try {
    $db = Connection::getInstance();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            Auth::require();
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['codigo'])) {
                throw new Exception('Código do cupom não fornecido');
            }

            $stmt = $db->prepare("
                SELECT * FROM cupons 
                WHERE codigo = ? 
                AND ativo = TRUE 
                AND data_inicio <= CURRENT_DATE 
                AND data_fim >= CURRENT_DATE
                AND (quantidade_maxima IS NULL OR quantidade_usada < quantidade_maxima)
            ");
            $stmt->execute([$data['codigo']]);
            $cupom = $stmt->fetch();

            if (!$cupom) {
                throw new Exception('Cupom inválido ou expirado');
            }

            if ($cupom['valor_minimo'] && $data['valor_total'] < $cupom['valor_minimo']) {
                throw new Exception("Valor mínimo para este cupom é R$ " . number_format($cupom['valor_minimo'], 2));
            }

            // Calcula o desconto
            $desconto = $cupom['tipo'] === 'percentual' 
                ? ($data['valor_total'] * ($cupom['desconto'] / 100))
                : $cupom['desconto'];

            Utils::jsonResponse([
                'status' => 'success',
                'data' => [
                    'desconto' => $desconto,
                    'tipo' => $cupom['tipo'],
                    'valor_final' => $data['valor_total'] - $desconto
                ]
            ]);
            break;

        default:
            throw new Exception('Método não permitido', 405);
    }
} catch (Exception $e) {
    Utils::logError("Erro na API de cupons: " . $e->getMessage());
    Utils::jsonResponse([
        'status' => 'error',
        'message' => $e->getMessage()
    ], ($e->getCode() ?: 400));
}