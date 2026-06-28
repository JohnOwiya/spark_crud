<?php
/**
 * partials/sidebar.php
 * Shared sidebar markup, included at the top of every page's <body>.
 * Expects $activePage to be set by the including page ("dashboard", "add").
 */
require_once __DIR__ . '/../icons.php';
$activePage = $activePage ?? '';
?>
<div class="sidebar">
  <div class="brand">
    <span class="mark">S</span> Spark CRM
  </div>
  <p class="brand-sub">Customer Management</p>

  <p class="nav-section-label">Workspace</p>
  <a href="index.php" class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
    <?= icon('customers') ?> Customers
  </a>
  <a href="add.php" class="nav-link <?= $activePage === 'add' ? 'active' : '' ?>">
    <?= icon('add') ?> Add customer
  </a>

  <div class="sidebar-foot">
    Spark Demo Build &middot; v1.0
  </div>
</div>