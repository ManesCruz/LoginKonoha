<?php
session_start();

// Borrar sesión completamente
$_SESSION = [];
session_unset();
session_destroy();

// Evitar caché del navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirigir al login usando redirección real
header("Location: ../index.php");
exit;
?>
