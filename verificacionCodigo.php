<?php
session_start();

// Si no hay sesión 2FA iniciada, regresa al index
if (!isset($_SESSION['2fa_user_id'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Código</title>
</head>
<body>
    <h2>Autenticación en dos pasos</h2>
    <p>Hemos enviado un código a tu correo (revisa spam).</p>

    <form action="/LoginKonoha/InicioSesion/validarCodigo.php" method="POST">
        <input type="text" name="codigo" placeholder="Ingrese el código" required maxlength="6" pattern="\d{6}">
        <button type="submit">Verificar</button>
    </form>
</body>
</html>
