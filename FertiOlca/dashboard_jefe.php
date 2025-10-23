<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'jefe') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Jefe de Cuadrilla - FertiOlca</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<header class="header">
  <div class="header__inner container">
    <div class="brand">
      <span class="brand__logo"></span>
      <span class="brand__name">FertiOlca</span>
    </div>
    <div class="header__actions">
      <span>👷‍♂️ <?= htmlspecialchars($_SESSION['nombre']) ?></span>
      <a href="logout.php" class="btn btn--ghost">Cerrar sesión</a>
    </div>
  </div>
</header>

<div class="layout">
  <aside class="sidebar">
    <nav>
      <ul class="menu">
        <li><a href="dashboard_jefe.php" class="is-active">🏠 Panel</a></li>
        <li><a href="mi_cuadrilla.php">👨‍🌾 Mi Cuadrilla</a></li>
        <li><a href="maquinaria.php">🚜 Maquinaria</a></li>
      </ul>
    </nav>
  </aside>

  <main class="content">
    <h1 class="page-title">Panel del Jefe de Cuadrilla</h1>
    <p class="page-subtitle">Gestiona tus trabajadores, cuadrilla y maquinaria asignada</p>

    <div class="stats" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
      <article class="card">
        <h3>Mi Cuadrilla</h3>
        <p>Gestiona tus trabajadores y ubicación actual.</p>
        <a href="mi_cuadrilla.php" class="btn btn--info mt-3">Entrar</a>
      </article>

      <article class="card">
        <h3>Maquinaria</h3>
        <p>Ocupar o devolver las máquinas que estás utilizando.</p>
        <a href="maquinaria.php" class="btn btn--info mt-3">Entrar</a>
      </article>
    </div>
  </main>
</div>

<footer class="footer">
  © <?= date('Y') ?> FertiOlca | Sistema de Gestión de Cuadrillas
</footer>

</body>
</html>


