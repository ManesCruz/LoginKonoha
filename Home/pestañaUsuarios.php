<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar sesión y rol admin
if (!isset($_SESSION['username']) || empty($_SESSION['2fa_verified']) || $_SESSION['role_id'] !== 1) {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Panel de Control</h2>
        <a href="dashboard.php">Inicio</a>
        <a href="pestañaArchivos.php">Archivos</a>
        <a href="pestañaUsuarios.php" class="active">Usuarios</a>
        <a href="../InicioSesion/CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Gestión de Usuarios</h1>
            <p>Administrador: <?= htmlspecialchars($username) ?></p>
        </div>

        <div class="content">
            <div class="search-bar">
                <input type="text" id="buscar" placeholder="Buscar usuario...">
            </div>

            <table id="tabla-usuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Rol Actual</th>
                        <th>Cambiar Rol</th>
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
                        <select onchange="cambiarRol(${u.id}, this.value)">
                            <option value="">Seleccionar...</option>
                            <option value="1" ${u.role_id == 1 ? 'selected' : ''}>Hokage</option>
                            <option value="2" ${u.role_id == 2 ? 'selected' : ''}>Genin</option>
                            <option value="3" ${u.role_id == 3 ? 'selected' : ''}>Chunin</option>
                            <option value="4" ${u.role_id == 4 ? 'selected' : ''}>Jonin</option>
                            <option value="5" ${u.role_id == 5 ? 'selected' : ''}>Anbu</option>
                        </select>
                    </td>
                `;
                tabla.appendChild(tr);
            });
        }

        window.cambiarRol = async function(id, nuevoRol) {
            if (!nuevoRol) return;
            const formData = new FormData();
            formData.append('id', id);
            formData.append('rol', nuevoRol);

            const res = await fetch(`actualizarRol.php`, {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            alert(data.message);
            cargarUsuarios(buscar.value);
        };
    });
    </script>
</body>
</html>
