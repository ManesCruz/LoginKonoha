<?php
require_once '../config/Connection.php';
header('Content-Type: application/json');

$response = ["success" => false, "message" => ""];

if (!isset($_POST['id'])) {
    $response["message"] = "ID no recibido.";
    echo json_encode($response);
    exit;
}

$id = $_POST['id'];

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

    // 🗑️ Eliminar de la base de datos PRIMERO
    $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
    $stmt->execute([$id]);

    // 🧹 Eliminar archivo físico si existe
    // ✅ CORRECCIÓN: La ruta ya viene completa desde la BD (../uploads/archivo.pdf)
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

} catch (Exception $e) {
    $response["message"] = "Error al eliminar: " . $e->getMessage();
}

echo json_encode($response);
?>