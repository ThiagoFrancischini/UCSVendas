<?php
/**
 * REST API - Consulta de Pedidos
 *
 * GET /api/rest/pedidos.php?numero=123
 * GET /api/rest/pedidos.php?cliente=Nome%20do%20Cliente
 *
 * Retorna JSON com pedido(s) e seus itens.
 * Acessível sem autenticação (somente leitura).
 */
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}

function saida($data) {
    if (ob_get_length() > 0) ob_clean();
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    saida(['erro' => 'Método não permitido. Use GET.']);
}

include_once(__DIR__ . '/../../controllers/PedidoController.php');
$controller = new PedidoController();

$numero  = intval($_GET['numero'] ?? 0);
$cliente = trim($_GET['cliente'] ?? '');

if ($numero <= 0 && $cliente === '') {
    http_response_code(400);
    saida(['erro' => 'Informe o parâmetro "numero" (id do pedido) ou "cliente" (nome do cliente).']);
}

if ($numero > 0) {
    $pedido = $controller->buscarPedidoPorId($numero);
    if (!$pedido) {
        http_response_code(404);
        saida(['erro' => "Pedido #$numero não encontrado."]);
    }
    $itens = $controller->buscarItensPedido($pedido->getId(), 1, 100);
    saida(['pedido' => pedidoParaArray($pedido, $itens)]);
}

// busca por nome do cliente
$pedidos = $controller->buscarPorNomeCliente($cliente, 1, 50);
$resultado = [];
foreach ($pedidos as $pedido) {
    $itens = $controller->buscarItensPedido($pedido->getId(), 1, 100);
    $resultado[] = pedidoParaArray($pedido, $itens);
}
saida(['pedidos' => $resultado, 'total' => count($resultado)]);

function pedidoParaArray($pedido, $itens) {
    $itensFormatados = array_map(function($item) {
        return [
            'produto'        => $item['produto_nome'],
            'descricao'      => $item['produto_descricao'],
            'quantidade'     => $item['quantidade'],
            'valor_unitario' => number_format($item['valor_unitario'], 2, ',', '.'),
            'valor_total'    => number_format($item['valor_total_item'], 2, ',', '.'),
        ];
    }, $itens);

    return [
        'numero'            => $pedido->getId(),
        'cliente'           => $pedido->cliente_nome ?? '',
        'data_pedido'       => $pedido->getDataPedido(),
        'status'            => $pedido->getStatus(),
        'data_envio'        => $pedido->getDataEnvio(),
        'data_cancelamento' => $pedido->getDataCancelamento(),
        'valor_total'       => number_format($pedido->getValorTotal(), 2, ',', '.'),
        'itens'             => $itensFormatados,
    ];
}
