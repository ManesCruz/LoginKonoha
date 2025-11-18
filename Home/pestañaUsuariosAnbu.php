<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar sesión y rol ANBU (5)
if (!isset($_SESSION['username']) || empty($_SESSION['2fa_verified']) || $_SESSION['role_id'] != 5) {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - ANBU</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .btn-detalle {
            padding: 6px 12px;
            background: #3d8bfd;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-detalle:hover {
            background: #1a6ae6;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Panel ANBU</h2>
    <a href="dashboardUsuarioAnbu.php">Inicio</a>
    <a href="pestañaArchivosAnbu.php">Archivos</a>
    <a href="pestañaUsuariosAnbu.php" class="active">Usuarios</a>
    <a href="../InicioSesion/CerrarSesion.php">Cerrar sesión</a>
</div>

<div class="main-content">
    <div class="header">
        <h1>Usuarios del Sistema</h1>
        <p>ANBU: <?= htmlspecialchars($username) ?></p>
    </div>

    <div class="content">

        <div class="search-bar">
            <input type="text" id="buscar" placeholder="Buscar usuario...">
        </div>

        <table id="tabla-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const buscar = document.getElementById('buscar');
    const tabla = document.querySelector('#tabla-usuarios tbody');

    cargarUsuarios();

    buscar.addEventListener('input', () => cargarUsuarios(buscar.value));

    async function cargarUsuarios(filtro = '') {
        const response = await fetch(`actualizarRol.php?buscar=${encodeURIComponent(filtro)}`);
        const data = await response.json();

        tabla.innerHTML = '';

        data.forEach(u => {
            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>${u.id}</td>
                <td>${u.username}</td>
                <td>${u.role_name}</td>
                <td>
                    <a class="btn-detalle" href="pestañaDetalleUsuario.php?id=${u.id}">
                        Ver detalle
                    </a>
                </td>
            `;

            tabla.appendChild(tr);
        });
    }
});
</script>

</body>
</html>
