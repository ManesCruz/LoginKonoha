<?php
require_once '../Config/Connection.php';

$connection = new Connection();
$pdo = $connection->getConnection();

$buscar = $_GET['buscar'] ?? '';
$tipo_archivo = $_GET['tipo_archivo'] ?? '';
$nivel_riesgo = $_GET['nivel_riesgo'] ?? '';
$clan = $_GET['clan'] ?? '';
$elemento = $_GET['elemento'] ?? '';

$sql = "SELECT * FROM files WHERE 1=1";
$params = [];

if (!empty($buscar)) {
    $sql .= " AND (nombre LIKE :buscar OR usuario LIKE :buscar)";
    $params['buscar'] = "%$buscar%";
}

if (!empty($tipo_archivo)) {
    $sql .= " AND tipo_archivo = :tipo_archivo";
    $params['tipo_archivo'] = $tipo_archivo;
}

if (!empty($nivel_riesgo)) {
    $sql .= " AND nivel_riesgo = :nivel_riesgo";
    $params['nivel_riesgo'] = $nivel_riesgo;
}

if (!empty($clan)) {
    $sql .= " AND clan = :clan";
    $params['clan'] = $clan;
}

if (!empty($elemento)) {
    $sql .= " AND elemento = :elemento";
    $params['elemento'] = $elemento;
}

$sql .= " ORDER BY fecha_subida DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$archivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($archivos);

