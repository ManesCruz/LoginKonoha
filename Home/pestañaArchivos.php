<?php
require_once '../config/Connection.php';
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Archivos</title>
    <link rel="stylesheet" href="../css/style.css">

    <!-- ✅ Librería SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleTipoJutsu() {
            const tipoArchivo = document.getElementById("tipo_archivo").value;
            const jutsuDiv = document.getElementById("tipoJutsuDiv");
            jutsuDiv.style.display = tipoArchivo === "Jutsu" ? "block" : "none";
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Panel de Control</h2>
        <a href="dashboard.php">Inicio</a>
        <a href="pestañaUsuarios.php">Usuarios</a>
        <a href="pestañaArchivos.php" class="active">Archivos</a>
        <a href="../InicioSesion/CerrarSesion.php">Cerrar sesión</a>
    </div>

    <div class="main-content">
        <h1>Gestión de Archivos</h1>

        <!-- 📁 Formulario de subida -->
        <form action="../archivos/listarArchivos.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <label>Seleccionar archivo PDF:</label>
            <input type="file" name="archivo" accept=".pdf" required>

            <label>Nivel de riesgo:</label>
            <select name="nivel_riesgo" required>
                <option value="">Seleccione...</option>
                <option value="D">D</option>
                <option value="C">C</option>
                <option value="B">B</option>
                <option value="A">A</option>
                <option value="S">S</option>
            </select>

            <label>Tipo de archivo:</label>
            <select name="tipo_archivo" id="tipo_archivo" onchange="toggleTipoJutsu()" required>
                <option value="">Seleccione...</option>
                <option value="Jutsu">Jutsu</option>
                <option value="Médico">Médico</option>
                <option value="Historia Shinobi">Historia Shinobi</option>
                <option value="Mapa o Estrategia">Mapa o Estrategia</option>
            </select>

            <div id="tipoJutsuDiv" style="display:none;">
                <label>Tipo de jutsu:</label>
                <select name="tipo_jutsu">
                    <option value="">Seleccione...</option>
                    <option value="Ninjutsu">Ninjutsu</option>
                    <option value="Genjutsu">Genjutsu</option>
                    <option value="Taijutsu">Taijutsu</option>
                    <option value="Kekkei Genkai">Kekkei Genkai</option>
                </select>
            </div>

            <label>Clan:</label>
            <select name="clan">
                <option value="">Seleccione...</option>
                <option value="Uchiha">Uchiha</option>
                <option value="Hyūga">Hyūga</option>
                <option value="Sin clan">Sin clan</option>
            </select>

            <label>Elemento:</label>
            <select name="elemento">
                <option value="">Seleccione...</option>
                <option value="Fuego">Fuego</option>
                <option value="Viento">Viento</option>
                <option value="Agua">Agua</option>
                <option value="Tierra">Tierra</option>
                <option value="Rayo">Rayo</option>
            </select>

            <input type="hidden" name="usuario" value="<?= htmlspecialchars($username) ?>">

            <button type="submit">Subir archivo</button>
        </form>

        <hr><hr>

        <h2>Archivos subidos</h2>

        <!-- 🔍 Buscador y filtros -->
        <div class="busqueda-filtros">
            <input type="text" id="buscar" placeholder="Buscar por nombre o usuario...">

            <div class="filtros">
                <select id="filtroTipo">
                    <option value="">Tipo de archivo</option>
                    <option value="Jutsu">Jutsu</option>
                    <option value="Médico">Médico</option>
                    <option value="Historia Shinobi">Historia Shinobi</option>
                    <option value="Mapa o Estrategia">Mapa o Estrategia</option>
                </select>

                <select id="filtroRiesgo">
                    <option value="">Nivel de riesgo</option>
                    <option value="D">D</option>
                    <option value="C">C</option>
                    <option value="B">B</option>
                    <option value="A">A</option>
                    <option value="S">S</option>
                </select>

                <select id="filtroClan">
                    <option value="">Clan</option>
                    <option value="Uchiha">Uchiha</option>
                    <option value="Hyūga">Hyūga</option>
                    <option value="Sin clan">Sin clan</option>
                </select>

                <select id="filtroElemento">
                    <option value="">Elemento</option>
                    <option value="Fuego">Fuego</option>
                    <option value="Viento">Viento</option>
                    <option value="Agua">Agua</option>
                    <option value="Tierra">Tierra</option>
                    <option value="Rayo">Rayo</option>
                </select>
            </div>
        </div>

        <!-- 📋 Tabla de archivos -->
        <table id="tablaArchivos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Riesgo</th>
                    <th>Clan</th>
                    <th>Elemento</th>
                    <th>Subido por</th>
                    <th>Fecha</th>
                    <th>Ver</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabla = document.querySelector('#tablaArchivos tbody');
            const buscar = document.getElementById('buscar');
            const tipo = document.getElementById('filtroTipo');
            const riesgo = document.getElementById('filtroRiesgo');
            const clan = document.getElementById('filtroClan');
            const elemento = document.getElementById('filtroElemento');

            // 🔄 Cargar archivos
            async function cargarArchivos() {
                const params = new URLSearchParams({
                    buscar: buscar.value,
                    tipo_archivo: tipo.value,
                    nivel_riesgo: riesgo.value,
                    clan: clan.value,
                    elemento: elemento.value
                });

                const res = await fetch(`../archivos/buscarArchivos.php?${params.toString()}`);
                const data = await res.json();

                tabla.innerHTML = '';
                if (data.length === 0) {
                    tabla.innerHTML = '<tr><td colspan="9">No se encontraron archivos.</td></tr>';
                    return;
                }

                data.forEach(a => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${a.nombre}</td>
                        <td>${a.tipo_archivo || a.tipo_jutsu || 'N/A'}</td>
                        <td>${a.nivel_riesgo}</td>
                        <td>${a.clan}</td>
                        <td>${a.elemento}</td>
                        <td>${a.usuario}</td>
                        <td>${a.fecha_subida}</td>
                        <td><a href="${a.ruta}" target="_blank">📄 Abrir</a></td>
                        <td><button class="eliminar-btn" data-id="${a.id}" title="Eliminar">🗑️</button></td>
                    `;
                    tabla.appendChild(tr);
                });
            }

            cargarArchivos();
            [buscar, tipo, riesgo, clan, elemento].forEach(el => {
                el.addEventListener('input', cargarArchivos);
                el.addEventListener('change', cargarArchivos);
            });

            // 🗑️ Eliminar archivos con SweetAlert2
            tabla.addEventListener('click', async (e) => {
                if (e.target.classList.contains('eliminar-btn')) {
                    const id = e.target.dataset.id;

                    const confirmacion = await Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'El archivo será eliminado permanentemente.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (confirmacion.isConfirmed) {
                        try {
                            const res = await fetch('../archivos/eliminarArchivos.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: new URLSearchParams({ id })
                            });

                            const data = await res.json();
                            console.log("Respuesta del servidor:", data);

                            if (data.success) {
                                e.target.closest('tr').remove();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado correctamente',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error inesperado',
                                text: 'No se pudo conectar con el servidor.'
                            });
                            console.error(error);
                        }
                    }
                }
            });
        });
        </script>
    </div>
</body>
</html>
