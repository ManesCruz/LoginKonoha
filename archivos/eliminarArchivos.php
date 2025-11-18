<?php
session_start();
require_once '../config/Connection.php';
require_once 'registrarActividad.php'; // ⬅️ IMPORTAR LA FUNCIÓN

header('Content-Type: application/json');

$response = ["success" => false, "message" => ""];

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    $response["message"] = "Usuario no autenticado.";
    echo json_encode($response);
    exit;
}

if (!isset($_POST['id'])) {
    $response["message"] = "ID no recibido.";
    echo json_encode($response);
    exit;
}

$id = $_POST['id'];
$user_id = $_SESSION['user_id'];

try {
    // ✅ Crear la conexión con la clase Connection
    $connection = new Connection();
    $pdo = $connection->getConnection();

    // 🔍 Buscar la ruta del archivo antes de borrarlo
    $stmt = $pdo->prepare("SELECT ruta FROM files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        $response["message"] = "Archivo no encontrado en la base de datos.";
        echo json_encode($response);
        exit;
    }

    // 🗑️ REGISTRAR ACTIVIDAD ANTES DE ELIMINAR
    registrarActividad($id, $user_id, 'eliminacion');
    error_log("✅ Actividad de eliminación registrada para archivo ID: $id");

    // 🗑️ Eliminar de la base de datos
    $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
    $stmt->execute([$id]);

    // 🧹 Eliminar archivo físico si existe
    $rutaArchivo = $file['ruta'];
    
    if (file_exists($rutaArchivo)) {
        if (unlink($rutaArchivo)) {
            $response["success"] = true;
            $response["message"] = "Archivo eliminado correctamente de la base de datos y del servidor.";
        } else {
            $response["success"] = true;
            $response["message"] = "Registro eliminado, pero no se pudo borrar el archivo físico.";
        }
    } else {
        $response["success"] = true;
        $response["message"] = "Registro eliminado. El archivo físico no existía en el servidor.";
    }

    error_log("✅ Archivo ID $id eliminado por usuario ID $user_id");

} catch (Exception $e) {
    error_log("❌ Error al eliminar archivo: " . $e->getMessage());
    $response["message"] = "Error al eliminar: " . $e->getMessage();
}

echo json_encode($response);
?>