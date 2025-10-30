<?php
session_start();

// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar sesión
if (!isset($_SESSION['username']) || empty($_SESSION['2fa_verified'])) {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];
$rol = $_SESSION['role_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control</title>
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
        <a href="dashboard.php">Inicio</a>
        <a href="pestañaArchivos.php">Archivos</a>
        <a href="pestañaUsuarios.php">Usuarios</a>
        <a href="../InicioSesion/CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Bienvenido, <?= htmlspecialchars($username) ?></h1>
            <p>Rol: <?= $rol === 1 ? 'Hokage' : 'Usuario' ?></p>
        </div>

        <div class="content">
            <h2>Inicio del Panel</h2>
            <p>Desde aquí puedes acceder a las secciones del sistema.  
               Si eres administrador, podrás gestionar usuarios en la pestaña "Usuarios".</p>
        </div>
    </div>
</body>
</html>
