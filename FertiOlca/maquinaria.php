<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'jefe') {
    header("Location: index.php");
    exit;
}

$id_jefe = $_SESSION['id'];

// Cambiar estado (ocupar o devolver maquinaria)
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $m = $conn->query("SELECT estado FROM maquinarias WHERE id=$id")->fetch_assoc();
    if ($m) {
        if ($m['estado'] === 'disponible') {
            $conn->query("UPDATE maquinarias SET estado='ocupada', id_jefe_ocupando=$id_jefe WHERE id=$id");
        } else {
            $conn->query("UPDATE maquinarias SET estado='disponible', id_jefe_ocupando=NULL WHERE id=$id");
        }
    }
    header("Location: maquinaria.php");
    exit;
}

// Consultar maquinarias con categoría y jefe actual
$maquinarias = $conn->query("
SELECT m.id, m.nombre, m.estado, c.nombre AS categoria, u.nombre AS jefe
FROM maquinarias m
JOIN categorias_maquinas c ON m.id_categoria=c.id
LEFT JOIN usuarios u ON m.id_jefe_ocupando=u.id
ORDER BY c.nombre, m.nombre
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maquinaria - FertiOlca</title>
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
        <li><a href="mi_cuadrilla.php">👨‍🌾 Mi Cuadrilla</a></li>
        <li><a href="maquinaria.php" class="is-active">🚜 Maquinaria</a></li>
      </ul>
    </nav>
  </aside>

  <main class="content">
    <h1 class="page-title">Gestión de Maquinaria</h1>
    <p class="page-subtitle">Consulta el estado de las máquinas y ocupa o devuelve según corresponda.</p>

    <!-- TABLA DE MAQUINARIA -->
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Estado</th>
            <th>Ocupada por</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($maquinarias && $maquinarias->num_rows > 0): ?>
            <?php while ($m = $maquinarias->fetch_assoc()): ?>
              <tr>
                <td><?= $m['id'] ?></td>
                <td><?= htmlspecialchars($m['nombre']) ?></td>
                <td><?= htmlspecialchars($m['categoria']) ?></td>
                <td>
                  <?php if ($m['estado'] === 'disponible'): ?>
                    <span class="badge badge--ok">Disponible</span>
                  <?php else: ?>
                    <span class="badge badge--bad">Ocupada</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($m['jefe'] ?? '-') ?></td>
                <td>
                  <?php if ($m['estado'] === 'disponible' || $m['jefe'] === $_SESSION['nombre']): ?>
                    <a href="?toggle=<?= $m['id'] ?>" class="btn btn--info">
                      <?= ($m['estado'] === 'disponible') ? 'Ocupar' : 'Devolver' ?>
                    </a>
                  <?php else: ?>
                    <span class="help">No disponible</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6">No hay máquinas registradas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<footer class="footer">
  © <?= date('Y') ?> FertiOlca | Gestión de Maquinaria
</footer>
</body>
</html>
