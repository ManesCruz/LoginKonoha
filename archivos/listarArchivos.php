<?php
session_start();
require_once '../config/connection.php';
require_once 'registrarActividad.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    die("❌ Usuario no autenticado. Por favor inicia sesión.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_SESSION['user_id'];
    
    // DEBUG: Ver qué contiene la sesión
    error_log("USER_ID de sesión: " . $user_id);

    $nivel_riesgo = $_POST['nivel_riesgo'] ?? '';
    $tipo_archivo = $_POST['tipo_archivo'] ?? '';
    $tipo_jutsu = $_POST['tipo_jutsu'] ?? null;
    $clan = $_POST['clan'] ?? '';
    $elemento = $_POST['elemento'] ?? '';

    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        die('Error al subir el archivo.');
    }

    $nombreArchivo = basename($_FILES['archivo']['name']);
    $rutaCarpeta = '../uploads/';
    if (!is_dir($rutaCarpeta)) mkdir($rutaCarpeta, 0777, true);

    $rutaDestino = $rutaCarpeta . time() . '_' . $nombreArchivo;

    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino)) {

        try {
            $connection = new Connection();
            $pdo = $connection->getConnection();

            $sql = "INSERT INTO files 
                (nombre, nivel_riesgo, tipo_archivo, tipo_jutsu, clan, elemento, ruta, usuario, fecha_subida)
                VALUES 
                (:nombre, :nivel_riesgo, :tipo_archivo, :tipo_jutsu, :clan, :elemento, :ruta, :usuario, NOW())";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombreArchivo,
                ':nivel_riesgo' => $nivel_riesgo,
                ':tipo_archivo' => $tipo_archivo,
                ':tipo_jutsu' => $tipo_jutsu,
                ':clan' => $clan,
                ':elemento' => $elemento,
                ':ruta' => $rutaDestino,
                ':usuario' => $user_id
            ]);

            // Obtener ID del archivo recién insertado
            $file_id = $pdo->lastInsertId();
            
            error_log("✅ Archivo insertado con ID: $file_id");

            // REGISTRAR ACTIVIDAD (subida)
            $resultado = registrarActividad($file_id, $user_id, 'subida');
            
            if ($resultado) {
                error_log("✅ Actividad registrada correctamente");
            } else {
                error_log("❌ Falló el registro de actividad");
            }

            echo "<script>
                    alert('✅ Archivo subido correctamente.');
                    window.location.href = '../Home/pestañaArchivos.php';
                  </script>";
            exit;

        } catch (PDOException $e) {
            error_log("❌ Error en BD: " . $e->getMessage());
            echo "<script>alert('❌ Error al guardar en la base de datos: " . $e->getMessage() . "'); window.history.back();</script>";
        }

    } else {
        echo "<script>alert('❌ Error al mover el archivo.'); window.history.back();</script>";
    }
}
?>