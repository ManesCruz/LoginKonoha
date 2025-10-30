<?php
require_once '../Config/Connection.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

try {
    $connection = new Connection();
    $pdo = $connection->getConnection();

    $buscar = trim($_GET['buscar'] ?? '');
    $tipo_archivo = trim($_GET['tipo_archivo'] ?? '');
    $nivel_riesgo = trim($_GET['nivel_riesgo'] ?? '');
    $clan = trim($_GET['clan'] ?? '');
    $elemento = trim($_GET['elemento'] ?? '');

    $sql = "SELECT * FROM files WHERE 1=1";
    $params = [];

    if ($buscar !== '') {
        $sql .= " AND (nombre LIKE ? OR usuario LIKE ?)";
        $params[] = "%$buscar%";
        $params[] = "%$buscar%";
    }

    if ($tipo_archivo !== '') {
        $sql .= " AND tipo_archivo = ?";
        $params[] = $tipo_archivo;
    }

    if ($nivel_riesgo !== '') {
        $sql .= " AND nivel_riesgo = ?";
        $params[] = $nivel_riesgo;
    }

    if ($clan !== '') {
        $sql .= " AND clan = ?";
        $params[] = $clan;
    }

    if ($elemento !== '') {
        $sql .= " AND elemento = ?";
        $params[] = $elemento;
    }

    $sql .= " ORDER BY fecha_subida DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $archivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($archivos, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>