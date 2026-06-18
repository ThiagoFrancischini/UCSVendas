<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ob_start();

if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../config.php');
}

function respondeJson($data) {
    if (ob_get_length() > 0) ob_clean();
    if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'FORNECEDOR') {
    respondeJson(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
}

include_once(__DIR__ . '/../controllers/PedidoController.php');

$controller = new PedidoController();
$pagina    = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;
$busca     = trim($_GET['busca'] ?? '');
$numero    = intval($_GET['numero'] ?? 0);

if ($numero > 0) {
    $pedido = $controller->buscarPedidoPorId($numero);
    if ($pedido) {
        respondeJson(['sucesso' => true, 'pedidos' => [pedidoParaArray($pedido)], 'total' => 1, 'paginas' => 1]);
    } else {
        respondeJson(['sucesso' => true, 'pedidos' => [], 'total' => 0, 'paginas' => 0]);
    }
}

if ($busca !== '') {
    $pedidos = $controller->buscarPorNomeCliente($busca, $pagina, $porPagina);
    $total   = $controller->contarPorNomeCliente($busca);
} else {
    $pedidos = $controller->listarTodos($pagina, $porPagina);
    $total   = $controller->contarTodos();
}

$paginas = ceil($total / $porPagina);

respondeJson([
    'sucesso' => true,
    'pedidos' => array_map('pedidoParaArray', $pedidos),
    'total'   => $total,
    'paginas' => $paginas,
    'pagina'  => $pagina,
]);

function pedidoParaArray($pedido) {
    return [
        'id'                 => $pedido->getId(),
        'cliente_id'         => $pedido->getClienteId(),
        'cliente_nome'       => $pedido->cliente_nome ?? '',
        'data_pedido'        => $pedido->getDataPedido(),
        'status'             => $pedido->getStatus(),
        'data_envio'         => $pedido->getDataEnvio(),
        'data_cancelamento'  => $pedido->getDataCancelamento(),
        'valor_total'        => number_format($pedido->getValorTotal(), 2, ',', '.'),
    ];
}
