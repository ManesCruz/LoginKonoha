<?php
session_start();

// Eliminar variables de sesión
$_SESSION = [];

// Destruir sesión
session_destroy();

// Borrar cookies de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Evitar cualquier tipo de caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
header("Clear-Site-Data: \"cache\", \"cookies\", \"storage\", \"executionContexts\"");
?>

<script>
        // Evitar volver atrás y redirigir al index del proyecto
        window.history.pushState(null, '', '/LoginKonoha/index.php');
        window.addEventListener('popstate', function() {
            window.location.replace('/LoginKonoha/index.php');
        });

        // Redirigir inmediatamente
        window.location.replace('/LoginKonoha/index.php');
    </script>


