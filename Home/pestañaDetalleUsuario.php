<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['2fa_verified']) || $_SESSION['role_id'] != 5) {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Usuario no válido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Usuario</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="sidebar">
    <h2>Panel ANBU</h2>
        <a href="dashboardUsuarioAnbu.php">Inicio</a>
        <a href="pestañaArchivosAnbu.php">Archivos</a>
        <a href="pestañaUsuariosAnbu.php">Usuarios</a>
        <a href="../InicioSesion/CerrarSesion.php">Cerrar sesión</a>
</div>

<div class="main-content">
    <div class="header">
        <h1>Detalle del Usuario</h1>
        <p>ANBU: <?= htmlspecialchars($username) ?></p>
    </div>

    <div class="content">
        <h2>Información del Usuario</h2>
        <div id="info-usuario">Cargando...</div>

        <h2>Historial de Actividad</h2>
        <table id="tabla-historial">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Archivo</th>
                    <th>Acción</th>
                    <th>Fecha</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const res = await fetch(`detalleUsuario.php?id=<?= $id ?>`);
    const data = await res.json();

    if (!data.success) {
        document.getElementById('info-usuario').innerHTML = "Error cargando datos";
        return;
    }

    const u = data.usuario;

    document.getElementById('info-usuario').innerHTML = `
        <p><strong>ID:</strong> ${u.id}</p>
        <p><strong>Usuario:</strong> ${u.username}</p>
        <p><strong>Nombre:</strong> ${u.nombre}</p>
        <p><strong>Documento:</strong> ${u.documento_identidad}</p>
        <p><strong>Rol:</strong> ${u.role_id}</p>
        <p><strong>Creado:</strong> ${u.created_at}</p>
    `;

    const tabla = document.querySelector("#tabla-historial tbody");
    tabla.innerHTML = "";

    data.historial.forEach(h => {
        tabla.innerHTML += `
            <tr>
                <td>${h.id}</td>
                <td>${h.archivo ?? 'Sin nombre'}</td>
                <td>${h.accion}</td>
                <td>${h.fecha_hora}</td>
                <td>${h.ip_address}</td>
            </tr>
        `;
    });
});
</script>

</body>
</html>
