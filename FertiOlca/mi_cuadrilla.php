<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'jefe') {
    header("Location: index.php");
    exit;
}

$id_jefe = $_SESSION['id'];

// Buscar cuadrilla del jefe
$cuadrilla = $conn->query("SELECT * FROM cuadrillas WHERE id_jefe = $id_jefe")->fetch_assoc();

// Si no tiene cuadrilla asignada
if (!$cuadrilla) {
    echo "<h2 style='text-align:center;'>No tienes una cuadrilla asignada.</h2>";
    echo "<p style='text-align:center;'><a href='dashboard_jefe.php'>Volver al panel</a></p>";
    exit;
}

// Añadir trabajador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_trabajador'])) {
    $nombre = trim($_POST['nombre']);
    if ($nombre !== '') {
        $stmt = $conn->prepare("INSERT INTO trabajadores (nombre, id_cuadrilla) VALUES (?, ?)");
        $stmt->bind_param("si", $nombre, $cuadrilla['id']);
        $stmt->execute();
    }
    header("Location: mi_cuadrilla.php");
    exit;
}

// Eliminar trabajador
if (isset($_GET['eliminar'])) {
    $id_trabajador = (int)$_GET['eliminar'];
    $conn->query("DELETE FROM trabajadores WHERE id = $id_trabajador");
    header("Location: mi_cuadrilla.php");
    exit;
}

// Actualizar ubicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_ubicacion'])) {
    $ubicacion = trim($_POST['ubicacion']);
    $stmt = $conn->prepare("UPDATE cuadrillas SET ubicacion = ? WHERE id = ?");
    $stmt->bind_param("si", $ubicacion, $cuadrilla['id']);
    $stmt->execute();
    $cuadrilla['ubicacion'] = $ubicacion;
    header("Location: mi_cuadrilla.php");
    exit;
}

// Listado de trabajadores
$trabajadores = $conn->query("SELECT * FROM trabajadores WHERE id_cuadrilla = {$cuadrilla['id']}");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi Cuadrilla - FertiOlca</title>
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
      <span>👷‍♂️ <?= htmlspecialchars($_SESSION['nombre']) ?></span>
      <a href="dashboard_jefe.php" class="btn btn--ghost">Volver</a>
      <a href="logout.php" class="btn btn--ghost">Salir</a>
    </div>
  </div>
</header>

<!-- LAYOUT -->
<div class="layout">
  <aside class="sidebar">
    <nav>
      <ul class="menu">
        <li><a href="dashboard_jefe.php">🏠 Panel</a></li>
        <li><a href="mi_cuadrilla.php" class="is-active">👨‍🌾 Mi Cuadrilla</a></li>
        <li><a href="maquinaria.php">🚜 Maquinaria</a></li>
      </ul>
    </nav>
  </aside>

  <main class="content">
    <h1 class="page-title">Mi Cuadrilla: <?= htmlspecialchars($cuadrilla['nombre']) ?></h1>
    <p class="page-subtitle">Gestiona los trabajadores y la ubicación actual de tu cuadrilla.</p>

    <!-- FORM AÑADIR TRABAJADOR -->
    <form class="form" method="POST">
      <h3>Añadir trabajador</h3>
      <label>Nombre del trabajador</label>
      <input type="text" name="nombre" required placeholder="Ejemplo: Juan Pérez">
      <button class="btn mt-3" name="nuevo_trabajador">Añadir trabajador</button>
    </form>

    <!-- LISTA DE TRABAJADORES -->
    <h2 class="page-subtitle mt-5">Trabajadores actuales</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($trabajadores && $trabajadores->num_rows > 0): ?>
            <?php while ($t = $trabajadores->fetch_assoc()): ?>
              <tr>
                <td><?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['nombre']) ?></td>
                <td>
                  <a href="?eliminar=<?= $t['id'] ?>" class="btn btn--danger" onclick="return confirm('¿Eliminar al trabajador <?= htmlspecialchars($t['nombre']) ?>?')">Eliminar</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="3">No hay trabajadores en esta cuadrilla.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- ACTUALIZAR UBICACIÓN -->
    <form class="form mt-5" method="POST">
      <h3>Actualizar ubicación de trabajo</h3>
      <label>Ubicación actual</label>
      <input type="text" name="ubicacion" placeholder="Ej: Campo 4" value="<?= htmlspecialchars($cuadrilla['ubicacion'] ?? '') ?>">
      <button class="btn mt-3" name="actualizar_ubicacion">Actualizar ubicación</button>
    </form>
  </main>
</div>

<footer class="footer">
  © <?= date('Y') ?> FertiOlca | Gestión de Cuadrillas
</footer>
</body>
</html>
