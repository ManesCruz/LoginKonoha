<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <!--CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Incluir SweetAlert desde un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="wrapper">
        <div class="title">Registro</div>
        <form action="InicioSesion/registrarse.php" method="POST">
            <div class="field">
                <input type="email" id="username" name="username" required>
                <label for="username">Correo</label>
            </div>
            <div class="field">
                <input type="text" id="nombre" name="nombre" required>
                <label for="nombre">Nombre y Apellido</label>
            </div>
            <div class="field">
                <input type="text" id="documento_identidad" name="documento_identidad" required>
                <label for="documento_identidad"># de documento</label>
            </div>
            <div class="field">
            <input 
                type="password" 
                id="password" 
                name="password" 
                required
                minlength="8"
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.,;:#^+=_-]).{8,}$"
                title="La contraseña debe tener mínimo 8 caracteres, incluir mayúscula, minúscula, número y un caracter especial."
            >
            <label for="password">Contraseña</label>
          </div>
            <div class="field">
                <select id="role_id" name="role_id" required>
                    <option value="2">Genin</option>
                </select>
                <label for="role_id">Rol</label>
            </div>
            <div class="field">
                <input type="submit" value="Registrar">
            </div>
            <div class="signup-link">
                ¿Ya tienes cuenta? <a href="index.php">Iniciar sesión</a>
            </div>
        </form>
    </div>
</body>

</html>
