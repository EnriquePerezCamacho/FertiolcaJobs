<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}
require_once "conexion.php";

// Datos para las estadísticas
$totalCuadrillas = $conn->query("SELECT COUNT(*) AS total FROM cuadrillas")->fetch_assoc()['total'] ?? 0;
$totalJefes = $conn->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol='jefe'")->fetch_assoc()['total'] ?? 0;
$totalTrabajadores = $conn->query("SELECT COUNT(*) AS total FROM trabajadores")->fetch_assoc()['total'] ?? 0;
$maquinasLibres = $conn->query("SELECT COUNT(*) AS total FROM maquinarias WHERE estado='disponible'")->fetch_assoc()['total'] ?? 0;
$maquinasOcupadas = $conn->query("SELECT COUNT(*) AS total FROM maquinarias WHERE estado='ocupada'")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel de Administración - FertiOlca</title>
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
      <img src="../imagenes/logo.png" alt="usuario" class="user-avatar">
      <span>👋 <?= htmlspecialchars($_SESSION['nombre']) ?></span>
      <a href="logout.php" class="btn btn--ghost">Cerrar sesión</a>
    </div>
  </div>
</header>

<!-- ESTRUCTURA PRINCIPAL -->
<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <nav>
      <ul class="menu">
        <li><a href="dashboard_admin.php" class="is-active">🏠 Dashboard</a></li>
        <li><a href="gestionar_jefes.php">👷‍♂️ Jefes</a></li>
        <li><a href="gestionar_cuadrillas.php">👨‍🌾 Cuadrillas</a></li>
        <li><a href="gestionar_maquinas.php">🚜 Maquinaria</a></li>
        <li><a href="crear_admin.php">⚙️ Administradores</a></li>
      </ul>
    </nav>
  </aside>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="content">
    <h1 class="page-title">Panel de Administración</h1>
    <p class="page-subtitle">Resumen general del sistema y accesos rápidos</p>

    <!-- CARDS ESTADÍSTICAS -->
    <section class="stats">
      <article class="card card--emphasis">
        <div class="card__label">Cuadrillas</div>
        <div class="card__value"><?= $totalCuadrillas ?></div>
      </article>
      <article class="card card--emphasis">
        <div class="card__label">Jefes activos</div>
        <div class="card__value"><?= $totalJefes ?></div>
      </article>
      <article class="card card--emphasis">
        <div class="card__label">Trabajadores</div>
        <div class="card__value"><?= $totalTrabajadores ?></div>
      </article>
      <article class="card card--emphasis">
        <div class="card__label">Máquinas libres</div>
        <div class="card__value"><?= $maquinasLibres ?></div>
      </article>
      <article class="card card--emphasis">
        <div class="card__label">Máquinas ocupadas</div>
        <div class="card__value"><?= $maquinasOcupadas ?></div>
      </article>
    </section>

    <!-- ENLACES RÁPIDOS -->
    <h2 class="page-subtitle mt-4">Gestión del sistema</h2>
    <div class="stats" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
      <article class="card">
        <h3>Gestionar Jefes</h3>
        <p>Dar de alta o baja a los jefes de cuadrilla.</p>
        <a href="gestionar_jefes.php" class="btn btn--info mt-3">Entrar</a>
      </article>
      <article class="card">
        <h3>Gestionar Cuadrillas</h3>
        <p>Crear cuadrillas y asignar trabajadores.</p>
        <a href="gestionar_cuadrillas.php" class="btn btn--info mt-3">Entrar</a>
      </article>
      <article class="card">
        <h3>Gestionar Maquinaria</h3>
        <p>Controlar máquinas, categorías y estados.</p>
        <a href="gestionar_maquinas.php" class="btn btn--info mt-3">Entrar</a>
      </article>
    </div>

  </main>
</div>

<!-- FOOTER -->
<footer class="footer">
  © <?= date('Y') ?> FertiOlca | Sistema de Gestión de Cuadrillas
</footer>

</body>
</html>
