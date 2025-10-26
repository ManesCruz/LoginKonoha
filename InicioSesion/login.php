<?php
require_once '../config/Connection.php';
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;

session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username']??'';
    $password = $_POST['password']??'';

    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            alert('Por favor ingrese un correo electrónico válido');
            window.location.href='../index.php';
        </script>";
        exit;
    }

    try{
        $connection = new Connection();
        $pdo= $connection->getConnection();

        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username'=>$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if($user && password_verify($password, $user['password'])){
            //verificación en 2 pasos
            $codigo = rand(100000, 999999);

            $_SESSION['2fa_user_id'] = $user['id'];
            $_SESSION['2fa_username'] = $user['username'];
            $_SESSION['2fa_role'] = $user['role_id'];
            $_SESSION['2fa_code'] = $codigo;

            // Enviar correo
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'TUCORREO@gmail.com';  
            $mail->Password = 'APP_PASSWORD_AQUI';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('TUCORREO@gmail.com', 'Konoha Login');
            $mail->addAddress($username);
            $mail->Subject = 'Código de verificación 2FA';
            $mail->Body = "Tu código de acceso es: $codigo";

            $mail->send();

            header("Location: verificar_codigo.php");
            exit;
        }

        echo "<script>alert('Credenciales inválidas'); window.location.href='../index.php';</script>";
        exit;

    }
    catch(Throwable $th){
           $error_message = "conexion fallida " . $th->getMessage();
                exit;
    }
}
