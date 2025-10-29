<?php
session_start();

// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Verificar sesión
if (!isset($_SESSION['username']) || empty($_SESSION['2fa_verified'])) {
    header("Location: ../index.php");
    exit;
}

// Conexión a base de datos
require_once '../Config/Connection.php';
$connection = new Connection();
$pdo = $connection->getConnection();

$username = $_SESSION['username'];
$rol = $_SESSION['role_id'] ?? 0;

// Cargar datos según el rol
if ($rol === 1) {
    $sql = "SELECT COUNT(*) AS total_usuarios FROM users";
    $stmt = $pdo->query($sql);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif ($rol === 2) {
    $sql = "SELECT id, username FROM users";
    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        // Bloquear botón "Atrás"
        window.history.pushState(null, "", window.location.href);
        window.addEventListener("popstate", () => {
            window.history.pushState(null, "", window.location.href);
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Panel de Control</h2>
        <a href="#">Inicio</a>
        <a href="#">Archivos</a>
        <a href="#">Perfil</a>
        <a href="../InicioSesion/CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Bienvenido, <?= htmlspecialchars($username) ?></h1>
            <p>Rol: <?= $rol === 1 ? 'Administrador' : 'Usuario' ?></p>
        </div>

        <?php if ($rol === 1): ?>
            <div class="card">
                <h3>Usuarios registrados</h3>
                <p><?= $stats['total_usuarios'] ?? 0 ?></p>
            </div>
        <?php elseif ($rol === 2): ?>
            <table>
                <tr><th>ID</th><th>Nombre de Usuario</th></tr>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['id']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
