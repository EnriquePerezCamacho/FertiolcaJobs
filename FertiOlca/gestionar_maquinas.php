<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Crear nueva categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_categoria'])) {
    $nombre = trim($_POST['nombre_categoria']);
    if ($nombre !== '') {
        $stmt = $conn->prepare("INSERT INTO categorias_maquinas (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
    }
    header("Location: gestionar_maquinas.php");
    exit;
}

// Crear nueva máquina
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_maquina'])) {
    $nombre = trim($_POST['nombre_maquina']);
    $id_categoria = (int)$_POST['id_categoria'];
    if ($nombre !== '' && $id_categoria > 0) {
        $stmt = $conn->prepare("INSERT INTO maquinarias (nombre, id_categoria, estado) VALUES (?, ?, 'disponible')");
        $stmt->bind_param("si", $nombre, $id_categoria);
        $stmt->execute();
    }
    header("Location: gestionar_maquinas.php");
    exit;
}

// Cambiar estado de máquina
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $maq = $conn->query("SELECT estado FROM maquinarias WHERE id=$id")->fetch_assoc();
    if ($maq) {
        $nuevo = ($maq['estado'] === 'disponible') ? 'ocupada' : 'disponible';
        $conn->query("UPDATE maquinarias SET estado='$nuevo', id_jefe_ocupando=NULL WHERE id=$id");
    }
    header("Location: gestionar_maquinas.php");
    exit;
}

// Consultas principales
$categorias = $conn->query("SELECT * FROM categorias_maquinas ORDER BY nombre");
$maquinas = $conn->query("SELECT m.id, m.nombre, m.estado, c.nombre AS categoria 
                          FROM maquinarias m 
                          JOIN categorias_maquinas c ON m.id_categoria=c.id 
                          ORDER BY c.nombre, m.nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Maquinaria - FertiOlca</title>
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

<!-- LAYOUT PRINCIPAL -->
<div class="layout">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <nav>
      <ul class="menu">
        <li><a href="dashboard_admin.php">🏠 Dashboard</a></li>
        <li><a href="gestionar_jefes.php">👷‍♂️ Jefes</a></li>
        <li><a href="gestionar_cuadrillas.php">👨‍🌾 Cuadrillas</a></li>
        <li><a href="gestionar_maquinas.php" class="is-active">🚜 Maquinaria</a></li>
      </ul>
    </nav>
  </aside>

  <!-- CONTENIDO -->
  <main class="content">
    <h1 class="page-title">Gestión de Maquinaria</h1>
    <p class="page-subtitle">Administra las categorías, crea nuevas máquinas y controla su estado.</p>

    <!-- FORM CREAR CATEGORÍA -->
    <form class="form" method="POST">
      <h3>Crear nueva categoría</h3>
      <label>Nombre de categoría</label>
      <input type="text" name="nombre_categoria" required>
      <button class="btn mt-3" name="nueva_categoria">Crear Categoría</button>
    </form>

    <!-- FORM CREAR MÁQUINA -->
    <form class="form mt-5" method="POST">
      <h3>Añadir nueva máquina</h3>
      <div class="form__row">
        <div>
          <label>Nombre de máquina</label>
          <input type="text" name="nombre_maquina" required>
        </div>
        <div>
          <label>Categoría</label>
          <select name="id_categoria" required>
            <option value="">Seleccionar...</option>
            <?php while ($c = $categorias->fetch_assoc()): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <button class="btn mt-3" name="nueva_maquina">Crear Máquina</button>
    </form>

    <!-- LISTADO DE MÁQUINAS -->
    <h2 class="page-subtitle mt-5">Lista de Maquinarias</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Estado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($maquinas && $maquinas->num_rows > 0): ?>
            <?php while ($m = $maquinas->fetch_assoc()): ?>
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
                <td>
                  <a href="?toggle=<?= $m['id'] ?>" class="btn btn--info">
                    Cambiar estado
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">No hay máquinas registradas.</td></tr>
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
