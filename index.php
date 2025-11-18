<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Inicio de Sesión - Konoha</title>
</head>
<body>
    <div class="wrapper"><!-- Contenedor Principal -->
        <div class="title"><h1>Iniciar Sesión</h1></div>
        <form action="InicioSesion/login.php" method="POST">
            <div class="field"><!-- Espacio para el Usuario -->
                <input type="email" name="username" required>
                <label for="username">Usuario</label>
            </div>
            <div class="field"><!-- Espacio para la Contraseña -->
                <input type="password" name="password" required>
                <label for="password">Contraseña</label> 
            </div>

                <div class="field">
                    <input type="submit" value="Iniciar Sesión">
                </div>

                <div class="signup-link">
                    ¿No tienes una cuenta? <a href="registro.php">Regístrate</a>
                </div>
            
        </form>
</div>
</body>
</html>