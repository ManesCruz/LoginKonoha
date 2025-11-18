<?php
session_start();
require_once '../config/Connection.php';
require_once 'registrarActividad.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    die("Usuario no autenticado.");
}

$file_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if ($file_id <= 0) {
    die("ID de archivo inválido.");
}

try {
    $connection = new Connection();
    $pdo = $connection->getConnection();

    // Obtener la ruta del archivo
    $stmt = $pdo->prepare("SELECT ruta FROM files WHERE id = ?");
    $stmt->execute([$file_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        die("Archivo no encontrado.");
    }

    // 📖 REGISTRAR ACTIVIDAD DE APERTURA
    registrarActividad($file_id, $user_id, 'apertura');
    error_log("✅ Actividad de apertura registrada para archivo ID: $file_id por usuario ID: $user_id");

    // 🔧 SOLUCIÓN: Convertir la ruta relativa a una ruta absoluta desde la raíz del proyecto
    // La ruta en BD es: ../uploads/archivo.pdf
    // Necesitamos: /LoginKonoha/uploads/archivo.pdf
    
    $nombreArchivo = basename($file['ruta']); // Extrae solo el nombre del archivo
    $rutaParaNavegador = "/LoginKonoha/uploads/" . $nombreArchivo;
    
    // Redirigir al archivo
    header("Location: " . $rutaParaNavegador);
    exit;

} catch (Exception $e) {
    error_log("❌ Error al abrir archivo: " . $e->getMessage());
    die("Error al abrir el archivo: " . $e->getMessage());
}
?>