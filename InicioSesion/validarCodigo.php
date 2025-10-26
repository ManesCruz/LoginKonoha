<?php
session_start();

if (!isset($_SESSION['2fa_code'])) {
    header("Location: ../index.php");
    exit;
}

$codigoIngresado = $_POST['codigo'];
$codigoCorrecto = $_SESSION['2fa_code'];

if ($codigoIngresado == $codigoCorrecto) {

    // Convertir sesión temporal en sesión real
    $_SESSION['user_id'] = $_SESSION['2fa_user_id'];
    $_SESSION['username'] = $_SESSION['2fa_username'];
    $_SESSION['role_id'] = $_SESSION['2fa_role'];

    unset($_SESSION['2fa_code'], $_SESSION['2fa_user_id'], $_SESSION['2fa_username'], $_SESSION['2fa_role']);

    if ($_SESSION['role_id'] == 1) {
        header("Location: ../Home/dashboard.php");
    } elseif ($_SESSION['role_id'] == 2) {
        header("Location: ../Home/dashboardUsuario.php");
    }
    exit;

} else {
    echo "<script>alert('Código incorrecto'); window.location.href='verificar_codigo.php';</script>";
}
