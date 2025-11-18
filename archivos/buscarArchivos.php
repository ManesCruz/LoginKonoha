<?php
require_once '../Config/Connection.php';
session_start();

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

try {
    // Verificar autenticación
    if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => true, 'message' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $connection = new Connection();
    $pdo = $connection->getConnection();

    $user_id = $_SESSION['user_id'];
    
    // Obtener el rol del usuario
    $sql_user = "SELECT u.*, r.nombre as rol_nombre 
                 FROM users u 
                 INNER JOIN roles r ON u.role_id = r.id 
                 WHERE u.id = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$user_id]);
    $usuario = $stmt_user->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        http_response_code(401);
        echo json_encode(['error' => true, 'message' => 'Usuario no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $rol = $usuario['rol_nombre'];

    // Parámetros de búsqueda
    $buscar = trim($_GET['buscar'] ?? '');
    $tipo_archivo = trim($_GET['tipo_archivo'] ?? '');
    $nivel_riesgo = trim($_GET['nivel_riesgo'] ?? '');
    $clan = trim($_GET['clan'] ?? '');
    $elemento = trim($_GET['elemento'] ?? '');

    $sql = "SELECT * FROM files WHERE 1=1";
    $params = [];

    // 🔒 CONTROL DE ACCESO POR RANGO
    switch ($rol) {
        case 'Genin':
            $sql .= " AND nivel_riesgo = 'D'";
            break;
        case 'Chunin':
            $sql .= " AND nivel_riesgo IN ('D', 'C')";
            break;
        case 'Jonin':
            $sql .= " AND nivel_riesgo IN ('D', 'C', 'B')";
            break;
        case 'Anbu':
            $sql .= " AND nivel_riesgo IN ('D', 'C', 'B', 'A')";
            break;
        case 'Hokage':
            // Hokage puede ver TODOS los niveles (D, C, B, A, S)
            break;
        default:
            // Por seguridad, rol desconocido solo ve D
            $sql .= " AND nivel_riesgo = 'D'";
            break;
    }

    // Filtros adicionales
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
    echo json_encode(['error' => true, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>