<?php
// reset_password.php  (BORRAR al terminar)
declare(strict_types=1);
require_once __DIR__ . "/conexion.php";

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $correo = trim($_POST['correo'] ?? '');
  $clave  = (string)($_POST['clave'] ?? '');
  if ($correo === '' || $clave === '') {
    $msg = "Rellena los dos campos.";
  } else {
    $hash = password_hash($clave, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET contrasena=? WHERE correo=?");
    $stmt->bind_param("ss", $hash, $correo);
    if ($stmt->execute() && $stmt->affected_rows >= 0) {
      $msg = "Contraseña actualizada para {$correo}. Prueba a iniciar sesión.";
    } else {
      $msg = "No se pudo actualizar (¿correo no existe?).";
    }
  }
}
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><title>Reset password</title></head>
<body>
<h2>Resetear contraseña de un usuario</h2>
<?php if ($msg): ?><p><?=htmlspecialchars($msg, ENT_QUOTES,'UTF-8')?></p><?php endif; ?>
<form method="post" style="max-width:420px;margin:auto;">
  <label>Correo:</label><br>
  <input type="email" name="correo" required value="<?=htmlspecialchars($_POST['correo']??'',ENT_QUOTES,'UTF-8')?>"><br><br>
  <label>Nueva contraseña:</label><br>
  <input type="text" name="clave" required placeholder="Ej: Admin1234"><br><br>
  <button>Actualizar</button>
</form>
</body></html>
