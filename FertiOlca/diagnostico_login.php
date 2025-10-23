<?php
// diagnostico_login.php  (BORRAR al terminar)
declare(strict_types=1);
session_start();
require_once __DIR__ . "/conexion.php";

$salida = [];

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave  = $_POST['clave'] ?? '';

    $salida[] = "Comprobando usuario: {$correo}";
    $stmt = $conn->prepare("SELECT id, nombre, correo, contrasena, rol, activo FROM usuarios WHERE correo=? LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $salida[] = "No existe ese correo en la BD actual.";
    } else {
        $u = $res->fetch_assoc();
        $salida[] = "Fila encontrada: id={$u['id']}, rol={$u['rol']}, activo={$u['activo']}";
        $salida[] = "Hash guardado (primeros 20 chars): ".substr((string)$u['contrasena'],0,20);
        $salida[] = "Longitud del hash: ".strlen((string)$u['contrasena']);
        $ok = password_verify($clave, (string)$u['contrasena']);
        $salida[] = "password_verify() => ".($ok ? "TRUE (coincide)" : "FALSE (no coincide)");
    }
}

// info rápida de BD
$info = [
  'server_info' => $conn->server_info ?? '',
  'client_info' => mysqli_get_client_info(),
  'database'    => 'fertiolca_db (según conexion.php)',
];
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><title>Diagnóstico login</title></head>
<body>
<h2>Diagnóstico de login (temporal)</h2>
<p>BD actual: <?=h($info['database'])?> | Server: <?=h($info['server_info'])?> | Cliente: <?=h($info['client_info'])?></p>

<form method="post" style="max-width:420px;margin:auto;">
  <label>Correo:</label><br>
  <input name="correo" type="email" required value="<?=h($_POST['correo'] ?? '')?>"><br><br>
  <label>Contraseña:</label><br>
  <input name="clave" type="password" required><br><br>
  <button>Probar password_verify()</button>
</form>

<?php if (!empty($salida)): ?>
  <pre style="background:#111;color:#0f0;padding:12px;max-width:900px;margin:20px auto;border-radius:8px;white-space:pre-wrap;"><?=h(implode("\n",$salida))?></pre>
<?php endif; ?>

<hr>
<h3>Usuarios existentes</h3>
<pre style="background:#eee;padding:12px;max-width:900px;margin:10px auto;border-radius:8px;">
<?php
$res = $conn->query("SELECT id, nombre, correo, rol, activo, LENGTH(contrasena) AS len FROM usuarios ORDER BY id");
while ($r = $res->fetch_assoc()) {
  echo "id={$r['id']} | {$r['correo']} | rol={$r['rol']} | activo={$r['activo']} | len(contrasena)={$r['len']}\n";
}
?>
</pre>
</body></html>
