<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Crear nuevo administrador
$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_admin'])) {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $clave = trim($_POST['clave']);

    if ($nombre && $correo && $clave) {
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol, activo) VALUES (?, ?, ?, 'admin', 1)");
        $stmt->bind_param("sss", $nombre, $correo, $hash);
        if ($stmt->execute()) {
            $msg = "Administrador creado correctamente ✅";
        } else {
            $msg = "❌ Error al crear administrador.";
        }
    } else {
        $msg = "Por favor completa todos los campos.";
    }
}

// Consultar lista de administradores
$admins = $conn->query("SELECT id, nombre, correo, activo FROM usuarios WHERE rol='admin' ORDER BY id");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear Administrador - FertiOlca</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<!-- CABECERA -->
<header class="header">
  <div class="header__inner container">
    <div class="brand">
      <span class="brand__logo"></span>
      <span class="brand__name">FertiOlca</span>
    </div>
    <div class="header__actions">
      <a href="dashboard_admin.php" class="btn btn--ghost">Volver</a>
      <a href="logout.php" class="btn btn--ghost">Cerrar sesión</a>
    </div>
  </div>
</header>

<!-- LAYOUT -->
<div class="layout">
  <aside class="sidebar">
    <nav>
      <ul class="menu">
        <li><a href="dashboard_admin.php">🏠 Dashboard</a></li>
        <li><a href="gestionar_jefes.php">👷‍♂️ Jefes</a></li>
        <li><a href="gestionar_cuadrillas.php">👨‍🌾 Cuadrillas</a></li>
        <li><a href="gestionar_maquinas.php">🚜 Maquinaria</a></li>
        <li><a href="crear_admin.php" class="is-active">⚙️ Administradores</a></li>
      </ul>
    </nav>
  </aside>

  <main class="content">
    <h1 class="page-title">Gestión de Administradores</h1>
    <p class="page-subtitle">Dar de alta nuevos administradores del sistema.</p>

    <?php if ($msg): ?>
      <p style="text-align:center; font-weight:bold; margin-bottom:20px;">
        <?= htmlspecialchars($msg) ?>
      </p>
    <?php endif; ?>

    <!-- FORMULARIO DE NUEVO ADMIN -->
    <form class="form" method="POST">
      <h3>Registrar nuevo administrador</h3>
      <div class="form__row">
        <div>
          <label>Nombre completo</label>
          <input type="text" name="nombre" required>
        </div>
        <div>
          <label>Correo electrónico</label>
          <input type="email" name="correo" required>
        </div>
      </div>
      <label>Contraseña inicial</label>
      <input type="password" name="clave" required>
      <button class="btn mt-3" type="submit" name="nuevo_admin">Crear Administrador</button>
    </form>

    <!-- LISTA DE ADMINISTRADORES -->
    <h2 class="page-subtitle mt-5">Administradores actuales</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($admins && $admins->num_rows > 0): ?>
            <?php while ($a = $admins->fetch_assoc()): ?>
              <tr>
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['nombre']) ?></td>
                <td><?= htmlspecialchars($a['correo']) ?></td>
                <td>
                  <?php if ($a['activo']): ?>
                    <span class="badge badge--ok">Activo</span>
                  <?php else: ?>
                    <span class="badge badge--bad">Inactivo</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4">No hay administradores registrados.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<footer class="footer">
  © <?= date('Y') ?> FertiOlca | Gestión de Administradores
</footer>
</body>
</html>
