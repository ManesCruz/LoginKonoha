<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../config/Connection.php';
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Por favor ingrese un correo electrónico válido');
                window.location.href='../index.php';
             </script>";
        exit;
    }

    try {
        $connection = new Connection();
        $pdo = $connection->getConnection();

        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            // Generar código 2FA
            $codigo = rand(100000, 999999);

            $_SESSION['2fa_user_id'] = $user['id'];
            $_SESSION['2fa_username'] = $user['username'];
            $_SESSION['2fa_role'] = $user['role_id'];
            $_SESSION['2fa_code'] = $codigo;

            // Envío del código por correo
            $mail = new PHPMailer(true);

            try {
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'manescruz@gmail.com'; 
                $mail->Password = 'wqwb qfky xkme dorb'; // Contraseña de aplicación
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Opciones para localhost con SSL relajado
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                $mail->setFrom('manescruz@gmail.com', 'Konoha');
                $mail->addAddress($username);
                $mail->Subject = 'Código de verificación 2FA';
                $mail->Body = "Tu código de acceso es: $codigo";

                $mail->send();

                header("Location:  /LoginKonoha/verificacionCodigo.php");
                exit;

            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
                exit;
            }
        }

        echo "<script>alert('Credenciales inválidas'); window.location.href='../index.php';</script>";
        exit;

    } catch (Throwable $th) {
        echo "Error: " . $th->getMessage();
        exit;
    }
}
?>
