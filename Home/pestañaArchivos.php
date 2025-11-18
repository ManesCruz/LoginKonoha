<?php
require_once '../config/Connection.php';
require_once '../archivos/registrarActividad.php'; // ⬅️ AGREGAR
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

// Verificar que exista user_id en sesión
if (!isset($_SESSION['user_id'])) {
    die("❌ Error: user_id no encontrado en sesión. Por favor cierra sesión e inicia de nuevo.");
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// 📤 PROCESAR SUBIDA DE ARCHIVO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    
    error_log("USER_ID de sesión: " . $user_id);

    $nivel_riesgo = $_POST['nivel_riesgo'] ?? '';
    $tipo_archivo = $_POST['tipo_archivo'] ?? '';
    $tipo_jutsu = $_POST['tipo_jutsu'] ?? null;
    $clan = $_POST['clan'] ?? '';
    $elemento = $_POST['elemento'] ?? '';

    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('❌ Error al subir el archivo.'); window.location.href='pestañaArchivos.php';</script>";
        exit;
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

            // 📝 REGISTRAR ACTIVIDAD DE SUBIDA
            $resultado = registrarActividad($file_id, $user_id, 'subida');
            
            if ($resultado) {
                error_log("✅ Actividad de subida registrada correctamente");
            } else {
                error_log("❌ Falló el registro de actividad de subida");
            }

            echo "<script>
                    alert('✅ Archivo subido correctamente.');
                    window.location.href = 'pestañaArchivos.php';
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
        <form action="pestañaArchivos.php" method="POST" enctype="multipart/form-data" class="upload-form">
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
                <option value="Ninguno">Ninguno</option>
            </select>

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
                        <td><a href="../archivos/abrirArchivos.php?id=${a.id}" target="_blank">📄 Abrir</a></td>
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