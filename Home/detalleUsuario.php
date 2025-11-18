<?php
require_once '../Config/Connection.php';

$connection = new Connection();
$pdo = $connection->getConnection();

header("Content-Type: application/json");

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID no recibido"]);
    exit;
}

// 1️⃣ Obtener datos del usuario
$sqlUser = "SELECT id, username, documento_identidad, nombre, role_id, created_at 
            FROM users WHERE id = :id";
$stmt = $pdo->prepare($sqlUser);
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// 2️⃣ Historial del usuario
// ✅ Ahora usa COALESCE para mostrar el nombre guardado si el archivo fue eliminado
$sqlLog = "SELECT 
            l.id,
            COALESCE(f.nombre, l.nombre_archivo, 'Archivo eliminado') AS archivo,
            l.accion,
            l.fecha_hora,
            l.ip_address,
            CASE 
                WHEN f.id IS NULL THEN 1
                ELSE 0
            END AS fue_eliminado
          FROM file_activity_log l
          LEFT JOIN files f ON f.id = l.file_id
          WHERE l.user_id = :id
          ORDER BY l.fecha_hora DESC";

$stmt2 = $pdo->prepare($sqlLog);
$stmt2->execute(['id' => $id]);
$historial = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "usuario" => $usuario,
    "historial" => $historial
]);
?>