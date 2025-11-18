<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Cache OFF + control de back/forward cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

echo "<script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || performance.getEntriesByType('navigation')[0].type === 'back_forward') {
            window.location.reload(true);
        }
    });
</script>";

// Si no hay intento 2FA → volver al login
if (!isset($_SESSION['2fa_code']) || !isset($_SESSION['2fa_user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Requiere método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../verificacionCodigo.php");
    exit;
}

$codigoIngresado = trim((string) ($_POST['codigo'] ?? ''));
$codigoCorrecto  = (string) ($_SESSION['2fa_code'] ?? '');

// ======== CONTADOR DE INTENTOS =========
if (!isset($_SESSION['codigo_intentos'])) {
    $_SESSION['codigo_intentos'] = 0;
}

// ======== IMPORTAR PHPMailer y conexión =========
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../config/Connection.php';
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';


// =========================================
//          SI EL CÓDIGO ES CORRECTO
// =========================================
if ($codigoIngresado === $codigoCorrecto) {

    // Resetear intentos al acertar
    $_SESSION['codigo_intentos'] = 0;

    // Promover sesión temporal
    $_SESSION['user_id']       = $_SESSION['2fa_user_id'];
    $_SESSION['username']      = $_SESSION['2fa_username'] ?? '';
    $_SESSION['role_id']       = $_SESSION['2fa_role'] ?? '';
    $_SESSION['2fa_verified']  = true;

    unset($_SESSION['2fa_code'], $_SESSION['2fa_user_id'], $_SESSION['2fa_username'], $_SESSION['2fa_role']);

    // Redirigir según rol
    if ($_SESSION['role_id'] == 1) {
        header("Location: ../Home/dashboard.php");
    } elseif ($_SESSION['role_id'] == 5) {
        header("Location: ../Home/dashboardUsuarioAnbu.php");
    } else {
        header("Location: ../Home/dashboardUsuario.php");
    }
    exit;
}


// =========================================
//         SI EL CÓDIGO ES INCORRECTO
// =========================================

$_SESSION['codigo_intentos']++;

if ($_SESSION['codigo_intentos'] >= 2) {

    try {

        // Obtener los ANBU (role_id = 5)
        $connection = new Connection();
        $pdo = $connection->getConnection();

        $sql = "SELECT username FROM users WHERE role_id = 5";
        $stmt = $pdo->query($sql);
        $anbus = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if ($anbus) {

            // Preparar datos del intento fallido
            $usuarioIntento = $_SESSION['2fa_username'] ?? 'Desconocido';
            $ip = $_SERVER['REMOTE_ADDR'];
            $fecha = date('Y-m-d H:i:s');

            // Enviar correo
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'manescruz@gmail.com';
            $mail->Password = 'wqwb qfky xkme dorb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('manescruz@gmail.com', 'Konoha - Sistema de Seguridad');

            // Agregar TODOS los ANBU encontrados
            foreach ($anbus as $correoAnbu) {
                $mail->addAddress($correoAnbu);
            }

            $mail->Subject = '⚠ ALERTA | Intentos fallidos de acceso';
            $mail->Body = "
                Se han detectado 2 intentos fallidos de verificación del código 2FA.

                Usuario: $usuarioIntento
                IP: $ip
                Fecha y hora: $fecha

                Sistema de Seguridad de Konoha.
            ";

            $mail->send();
        }

    } catch (Exception $e) {
        // Puedes registrar el error si quieres
        // file_put_contents("errores_mail.txt", $e->getMessage(), FILE_APPEND);
    }

    // Resetear contador para evitar spam
    $_SESSION['codigo_intentos'] = 0;
}

// Mostrar mensaje de error normal
echo "<script>alert('Código incorrecto'); window.location.href='../verificacionCodigo.php';</script>";
exit;
