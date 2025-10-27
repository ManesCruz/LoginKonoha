<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Si no hay intento 2FA, regreso al inicio
if (!isset($_SESSION['2fa_code']) || !isset($_SESSION['2fa_user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Aseguramos que llego por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si llaman por GET u otra cosa, redirigimos al formulario
    header("Location:  /LoginKonoha/verificacionCodigo.php");
    exit;
}

// Tomar y normalizar el código ingresado
$codigoIngresado = trim((string)($_POST['codigo'] ?? ''));

// Código almacenado en sesión (string)
$codigoCorrecto = (string)($_SESSION['2fa_code'] ?? '');

if ($codigoIngresado === $codigoCorrecto) {

    // Promover sesión temporal a sesión real
    $_SESSION['user_id']  = $_SESSION['2fa_user_id'];
    $_SESSION['username'] = $_SESSION['2fa_username'] ?? '';
    $_SESSION['role_id']  = $_SESSION['2fa_role'] ?? '';

    // Limpiar las variables 2FA
    unset($_SESSION['2fa_code'], $_SESSION['2fa_user_id'], $_SESSION['2fa_username'], $_SESSION['2fa_role']);

    // Redirigir según rol (ajusta valores si tu rol es distinto)
    if ($_SESSION['role_id'] == 1) {
        header("Location: ../Home/dashboard.php");
    } elseif ($_SESSION['role_id'] == 2) {
        header("Location: ../Home/dashboardUsuario.php");
    } else {
        // Si no hay rol, volver al index o a una página por defecto
        header("Location: ../index.php");
    }
    exit;

} else {
    // Código incorrecto: volvemos al formulario con mensaje
    echo "<script>alert('Código incorrecto'); window.location.href='../verificacionCodigo.php';</script>";
    exit;
}
