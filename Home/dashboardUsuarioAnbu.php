<?php
session_start();

// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar sesión y rol
if (!isset($_SESSION['username']) || empty($_SESSION['2fa_verified'])) {
    header("Location: ../index.php");
    exit;
}

$rol = $_SESSION['role_id'] ?? 0;

// Si el rol es 1 (Hokage), redirigir al index
if ($rol == 1) {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página restringida para Usuarios</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        // Bloquear botón atrás
        window.history.pushState(null, "", window.location.href);
        window.addEventListener("popstate", () => {
            window.history.pushState(null, "", window.location.href);
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Panel de Control</h2>
        <a href="dashboardUsuarioAnbu.php">Inicio</a>
        <a href="pestañaArchivosAnbu.php">Archivos</a>
        <a href="pestañaUsuariosAnbu.php">Usuarios</a>
        <a href="../InicioSesion/CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Bienvenido, <?= htmlspecialchars($username) ?></h1>
            <p>Rol: <?= match($rol) {
                2 => 'Genin',
                3 => 'Chunin',
                4 => 'Jonin',
                5 => 'Anbu',
                default => 'Desconocido'
            } ?></p>
        </div>

        <div class="content">
            <h2>Sección exclusiva para usuarios (no Hokage)</h2>
            <p>Aquí pueden acceder todos los roles excepto el Hokage.</p>
        </div>
    </div>
</body>
</html>

