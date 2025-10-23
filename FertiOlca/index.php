<?php
// index.php
declare(strict_types=1);
session_start();
require_once __DIR__ . "/conexion.php";

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if ($correo === '' || $clave === '') {
        $error = "⚠️ Rellena todos los campos.";
    } else {
        $stmt = $conn->prepare("SELECT id, nombre, contrasena, rol, activo FROM usuarios WHERE correo = ? LIMIT 1");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ((int)$user['activo'] !== 1) {
                $error = "⛔ Usuario inactivo.";
            } elseif (password_verify($clave, $user['contrasena'])) {
                session_regenerate_id(true);
                $_SESSION['id'] = $user['id'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['rol'] = $user['rol'];

                if ($user['rol'] === 'admin') {
                    header("Location: dashboard_admin.php");
                } else {
                    header("Location: dashboard_jefe.php");
                }
                exit;
            } else {
                $error = "❌ Contraseña incorrecta.";
            }
        } else {
            $error = "❌ Usuario no encontrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - FertiOlca</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="login">
  <div class="login__card">
    <h1 class="login__title">Acceder a FertiOlca</h1>
    <p class="login__subtitle">Sistema de gestión de cuadrillas</p>

    <?php if ($error): ?>
      <p style="color:#b91c1c; font-weight:bold; text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="form mt-4" method="POST">
      <label>Correo electrónico</label>
      <input type="email" name="correo" placeholder="ejemplo@correo.com" required>

      <label>Contraseña</label>
      <input type="password" name="clave" placeholder="••••••••" required>

      <button type="submit" class="btn mt-3">Iniciar sesión</button>
    </form>

    <p class="mt-4" style="text-align:center; color:#6b7280; font-size:14px;">
      © <?= date('Y') ?> FertiOlca — Todos los derechos reservados
    </p>
  </div>
</div>

</body>
</html>
