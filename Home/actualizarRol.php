<?php
require_once '../Config/Connection.php';

$connection = new Connection();
$pdo = $connection->getConnection();

$roles = [
    1 => 'Hokage',
    2 => "Genin",
    3 => "Chunin",
    4 => "Jonin",
    5 => "Anbu"
];

// === PETICIÓN GET: BUSCAR USUARIOS ===
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $buscar = $_GET['buscar'] ?? '';

    $sql = "SELECT id, username, role_id FROM users WHERE username LIKE :buscar ORDER BY id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['buscar' => "%$buscar%"]);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($usuarios as &$u) {
        $u['role_name'] = $roles[$u['role_id']] ?? 'Desconocido';
    }

    header('Content-Type: application/json');
    echo json_encode($usuarios);
    exit;
}

// === PETICIÓN POST: CAMBIAR ROL ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $rol = $_POST['rol'] ?? null;

    if (!$id || !$rol) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
        exit;
    }

    $sql = "UPDATE users SET role_id = :rol WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['rol' => $rol, 'id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Rol actualizado correctamente.']);
    exit;
}
