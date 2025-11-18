<?php
require_once '../config/Connection.php';

function registrarActividad($file_id, $user_id, $accion) {
    try {
        // Capturar IP del usuario
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Usar conexión PDO
        $connection = new Connection();
        $pdo = $connection->getConnection();

        // 📝 Obtener el nombre del archivo para guardarlo
        $stmtFile = $pdo->prepare("SELECT nombre FROM files WHERE id = ?");
        $stmtFile->execute([$file_id]);
        $archivo = $stmtFile->fetch(PDO::FETCH_ASSOC);
        $nombre_archivo = $archivo ? $archivo['nombre'] : 'Desconocido';

        $sql = "INSERT INTO file_activity_log (file_id, user_id, accion, ip_address, nombre_archivo, fecha_hora)
                VALUES (:file_id, :user_id, :accion, :ip, :nombre_archivo, NOW())";

        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            ':file_id' => $file_id,
            ':user_id' => $user_id,
            ':accion' => $accion,
            ':ip' => $ip,
            ':nombre_archivo' => $nombre_archivo
        ]);

        if ($resultado) {
            error_log("✅ Actividad registrada: file_id=$file_id, user_id=$user_id, accion=$accion");
            return true;
        } else {
            error_log("❌ Error al registrar actividad");
            return false;
        }

    } catch (Exception $e) {
        error_log("❌ ERROR registrando actividad: " . $e->getMessage());
        return false;
    }
}
?>