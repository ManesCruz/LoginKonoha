<?php
require_once '../config/Connection.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
     $username = $_POST['username'];
     $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
     $role_id = $_POST['role_id'];
     $documento_identidad =$_POST['documento_identidad'];
     $nombre = $_POST['nombre'];

if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
         echo "<script>
             alert('El usuario debe ser un correo electrónico válido');
             window.location.href='../registro.php';
         </script>";
         exit;
     }

     try{
        $connection = new Connection();
        $pdo= $connection->getConnection();


        $sql = "INSERT INTO users (username, password, role_id ,documento_identidad,nombre) VALUES (:username, :password, :role_id, :documento_identidad, :nombre)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username'=>$username, 
            'password'=>$password, 
            'role_id'=>$role_id,
            'documento_identidad'=>$documento_identidad,
            'nombre'=> $nombre
        ]);

        echo "<script>
        alert('usuario registrado correctamente');
        window.location.href='../index.php'; // Redirige a la página de inicio de sesión después del registro
        </script>";
    }
    catch(PDOException $th)
    {
            echo "<script>
        alert('error al registrar usuario: " . addslashes($th->getMessage()) . "');
        window.location.href='../registro.php'; // Redirige a la página de registro después del registro fallido.
        </script>";
        }
}