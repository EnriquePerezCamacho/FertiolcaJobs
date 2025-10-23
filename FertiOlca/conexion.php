<?php
// conexion.php
$host = "localhost";
$user = "root";
$pass = ""; // Cambia si tu MySQL tiene contraseña
$db   = "fertiolca_db";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (Throwable $e) {
    die("Error de conexión a la base de datos. Verifica tus credenciales.");
}
?>
