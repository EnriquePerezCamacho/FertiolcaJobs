<?php
session_start();
require_once "conexion.php";
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_jefe'])) {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol, activo) VALUES (?, ?, ?, 'jefe', 1)");
    $stmt->bind_param("sss", $nombre, $correo, $clave);
    $stmt->execute();
}

if (isset($_GET['baja'])) {
    $id = (int)$_GET['baja'];
    $conn->query("UPDATE usuarios SET activo = 0 WHERE id = $id AND rol='jefe'");
}

if (isset($_GET['alta'])) {
    $id = (int)$_GET['alta'];
    $conn->query("UPDATE usuarios SET activo = 1 WHERE id = $id AND rol='jefe'");
}

$jefes = $conn->query("SELECT id, nombre, correo, activo FROM usuarios WHERE rol='jefe'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestionar Jefes - FertiOlca</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<header class="header">
  <div class="header__inner container">
    <div class="brand"><span class="brand__logo"></span><span class="brand__name">FertiOlca</span></div>
    <div class="header__actions"><a href="dashboard_admin.php" class="btn btn--ghost">Volver</a></div>
  </div>
</header>

<div class="layout">
  <aside class="sidebar">
    <ul class="menu">
      <li><a href="dashboard_admin.php">🏠 Panel</a></li>
      <li><a href="gestionar_jefes.php" class="is-active">👷‍♂️ Jefes</a></li>
      <li><a href="gestionar_cuadrillas.php">👨‍🌾 Cuadrillas</a></li>
      <li><a href="gestionar_maquinas.php">🚜 Maquinaria</a></li>
    </ul>
  </aside>

  <main class="content">
    <h1 class="page-title">Gestión de Jefes</h1>

    <form class="form" method="POST">
      <h3>Crear nuevo jefe</h3>
      <div class="form__row">
        <div>
          <label>Nombre</label>
          <input type="text" name="nombre" required>
        </div>
        <div>
          <label>Correo</label>
          <input type="email" name="correo" required>
        </div>
      </div>
      <label>Contraseña</label>
      <input type="password" name="clave" required>
      <button class="btn" type="submit" name="nuevo_jefe">Crear Jefe</button>
    </form>

    <h2 class="page-subtitle mt-5">Lista de jefes registrados</h2>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Nombre</th><th>Correo</th><th>Estado</th><th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($j = $jefes->fetch_assoc()): ?>
          <tr>
            <td><?= $j['id'] ?></td>
            <td><?= htmlspecialchars($j['nombre']) ?></td>
            <td><?= htmlspecialchars($j['correo']) ?></td>
            <td>
              <?php if ($j['activo']): ?>
                <span class="badge badge--ok">Activo</span>
              <?php else: ?>
                <span class="badge badge--bad">Inactivo</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($j['activo']): ?>
                <a href="?baja=<?= $j['id'] ?>" class="btn btn--danger">Dar de baja</a>
              <?php else: ?>
                <a href="?alta=<?= $j['id'] ?>" class="btn btn--info">Reactivar</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<footer class="footer">© <?= date('Y') ?> FertiOlca</footer>
</body>
</html>
