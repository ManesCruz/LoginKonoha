<?php
require_once '../config/Connection.php';

session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username']??'';
    $password = $_POST['password']??'';

    try{
        $connection = new Connection();
        $pdo= $connection->getConnection();

        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username'=>$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];

            if($user['role_id'] == 1){
            header("Location: ../Home/dashboard.php");
            }elseif($user['role_id'] == 2){
                header("Location: ../Home/dashboardUsuario.php");
        } else{
            echo 'acceso denegado';
        }
        exit;
    }else{
        $error_message='credenciales invalidas';
}
}

    catch(Throwable $th){
           $error_message = "conexion fallida " . $th->getMessage();
                exit;
    }
}
