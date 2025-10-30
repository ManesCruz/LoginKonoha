<?php
require_once '../Config/Connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nivel_riesgo = $_POST['nivel_riesgo'] ?? '';
    $tipo_archivo = $_POST['tipo_archivo'] ?? '';
    $tipo_jutsu = $_POST['tipo_jutsu'] ?? null;
    $clan = $_POST['clan'] ?? '';
    $elemento = $_POST['elemento'] ?? '';
    $usuario = $_POST['usuario'] ?? 'Desconocido';

    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        die('Error al subir el archivo.');
    }

    $nombreArchivo = basename($_FILES['archivo']['name']);
    $rutaCarpeta = '../uploads/';
    if (!is_dir($rutaCarpeta)) mkdir($rutaCarpeta, 0777, true);
    $rutaDestino = $rutaCarpeta . time() . '_' . $nombreArchivo;

    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino)) {
        $connection = new Connection();
        $pdo = $connection->getConnection();

        $sql = "INSERT INTO files (nombre, nivel_riesgo, tipo_archivo, tipo_jutsu, clan, elemento, ruta, usuario, fecha_subida)
                VALUES (:nombre, :nivel_riesgo, :tipo_archivo, :tipo_jutsu, :clan, :elemento, :ruta, :usuario, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombreArchivo,
            'nivel_riesgo' => $nivel_riesgo,
            'tipo_archivo' => $tipo_archivo,
            'tipo_jutsu' => $tipo_jutsu,
            'clan' => $clan,
            'elemento' => $elemento,
            'ruta' => $rutaDestino,
            'usuario' => $usuario
        ]);

        echo "<script>
                alert('✅ Archivo subido correctamente.');
                window.location.href = '../Home/pestañaArchivos.php';
              </script>";
    } else {
        echo "<script>alert('❌ Error al mover el archivo.'); window.history.back();</script>";
    }
}
?>
