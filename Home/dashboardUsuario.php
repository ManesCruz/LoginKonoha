<?php
session_start();

// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Bloquear acceso sin login
if (!isset($_SESSION['username']) || empty($_SESSION['2fa_verified'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../Config/Connection.php';
$connection = new Connection();
$pdo = $connection->getConnection();

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        // Bloquear volver con “Atrás”
        window.history.pushState(null, "", window.location.href);
        window.addEventListener("popstate", () => {
            window.history.pushState(null, "", window.location.href);
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Usuario</h2>
        <a href="#">Inicio</a>
        <a href="#">Mis archivos</a>
        <a href="#">Perfil</a>
        <a href="../InicioSesion/CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Bienvenido, <?= htmlspecialchars($username) ?></h1>
            <p>Rol: Usuario</p>
        </div>

        <p>Este es tu panel de usuario.</p>
    </div>
</body>
</html>
