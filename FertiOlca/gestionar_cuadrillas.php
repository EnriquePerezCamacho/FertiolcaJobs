<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// ===== Helpers =====
function fetch_jefes(mysqli $conn) {
    $jefes = [];
    $res = $conn->query("SELECT id, nombre FROM usuarios WHERE rol='jefe' AND activo=1 ORDER BY nombre");
    if ($res) { while ($r = $res->fetch_assoc()) { $jefes[] = $r; } }
    return $jefes;
}

function fetch_cuadrillas(mysqli $conn) {
    $sql = "SELECT c.id, c.nombre, c.ubicacion, u.nombre AS jefe
            FROM cuadrillas c
            LEFT JOIN usuarios u ON u.id = c.id_jefe
            ORDER BY c.id DESC";
    return $conn->query($sql);
}

// ===== Acciones =====
// Crear nueva cuadrilla
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_cuadrilla'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $id_jefe = (int)($_POST['id_jefe'] ?? 0);

    if ($nombre !== '') {
        $stmt = $conn->prepare("INSERT INTO cuadrillas (nombre, id_jefe, ubicacion) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $nombre, $id_jefe, $ubicacion);
        $stmt->execute();
    }
    header("Location: gestionar_cuadrillas.php");
    exit;
}

// Actualizar ubicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_ubicacion'])) {
    $id = (int)($_POST['id'] ?? 0);
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE cuadrillas SET ubicacion=? WHERE id=?");
        $stmt->bind_param("si", $ubicacion, $id);
        $stmt->execute();
    }
    header("Location: gestionar_cuadrillas.php");
    exit;
}

// Reasignar jefe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reasignar_jefe'])) {
    $id = (int)($_POST['id'] ?? 0);
    $id_jefe = (int)($_POST['id_jefe'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE cuadrillas SET id_jefe=? WHERE id=?");
        $stmt->bind_param("ii", $id_jefe, $id);
        $stmt->execute();
    }
    header("Location: gestionar_cuadrillas.php");
    exit;
}

// Eliminar cuadrilla (simple; en producción valida dependencias)
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM cuadrillas WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: gestionar_cuadrillas.php");
    exit;
}

// Datos para la vista
$jefes = fetch_jefes($conn);
$cuadrillas = fetch_cuadrillas($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestionar Cuadrillas - FertiOlca</title>
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
      <a href="dashboard_admin.php" class="btn btn--ghost">Volver al panel</a>
      <a href="logout.php" class="btn btn--ghost">Cerrar sesión</a>
    </div>
  </div>
</header>

<!-- LAYOUT -->
<div class="layout">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <nav>
      <ul class="menu">
        <li><a href="dashboard_admin.php">🏠 Dashboard</a></li>
        <li><a href="gestionar_jefes.php">👷‍♂️ Jefes</a></li>
        <li><a href="gestionar_cuadrillas.php" class="is-active">👨‍🌾 Cuadrillas</a></li>
        <li><a href="gestionar_maquinas.php">🚜 Maquinaria</a></li>
      </ul>
    </nav>
  </aside>

  <!-- CONTENIDO -->
  <main class="content">
    <h1 class="page-title">Gestión de Cuadrillas</h1>
    <p class="page-subtitle">Crea nuevas cuadrillas, actualiza su ubicación o reasigna jefes.</p>

    <!-- FORM CREAR CUADRILLA -->
    <form class="form" method="POST">
      <h3>Crear nueva cuadrilla</h3>
      <div class="form__row">
        <div>
          <label>Nombre de cuadrilla</label>
          <input type="text" name="nombre" required>
        </div>
        <div>
          <label>Ubicación actual</label>
          <input type="text" name="ubicacion" placeholder="Ej: Finca Los Olivos">
        </div>
      </div>
      <div>
        <label>Jefe asignado</label>
        <select name="id_jefe">
          <option value="0">(Sin jefe)</option>
          <?php foreach ($jefes as $j): ?>
            <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn mt-3" type="submit" name="nueva_cuadrilla">Crear cuadrilla</button>
    </form>

    <!-- LISTADO -->
    <h2 class="page-subtitle mt-5">Listado de cuadrillas</h2>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Jefe</th>
            <th style="min-width:240px">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($cuadrillas && $cuadrillas->num_rows): ?>
            <?php while ($c = $cuadrillas->fetch_assoc()): ?>
              <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['nombre']) ?></td>
                <td><?= htmlspecialchars($c['ubicacion'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['jefe'] ?? '(Sin jefe)') ?></td>
                <td class="actions">
                  <!-- Actualizar ubicación -->
                  <form method="POST" class="actions" style="gap:6px">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <input type="text" name="ubicacion" placeholder="Nueva ubicación" value="<?= htmlspecialchars($c['ubicacion'] ?? '') ?>">
                    <button class="btn btn--warn" name="actualizar_ubicacion">Actualizar</button>
                  </form>

                  <!-- Reasignar jefe -->
                  <form method="POST" class="actions" style="gap:6px">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <select name="id_jefe">
                      <option value="0">(Sin jefe)</option>
                      <?php foreach ($jefes as $j): ?>
                        <option value="<?= $j['id'] ?>" <?= (isset($c['jefe']) && $c['jefe'] === $j['nombre']) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($j['nombre']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <button class="btn btn--info" name="reasignar_jefe">Reasignar</button>
                  </form>

                  <!-- Eliminar -->
                  <a class="btn btn--danger" href="?eliminar=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar la cuadrilla <?= htmlspecialchars($c['nombre']) ?>?');">Eliminar</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">No hay cuadrillas registradas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<footer class="footer">
  © <?= date('Y') ?> FertiOlca | Gestión de Cuadrillas
</footer>
</body>
</html>
