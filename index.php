<?php
/**
 * index.php
 * The "Read" page: shows every customer in a table, with search and pagination.
 * Also displays success/error banners passed via ?status= in the URL
 * (set by add.php, edit.php, delete.php after they finish their work).
 */
require_once 'db.php';
require_once 'helpers.php';
require_once 'icons.php';

$activePage = 'dashboard';

// ---------- Search ----------
$search = trim($_GET['q'] ?? '');

// ---------- Pagination ----------
$perPage = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

// Build the WHERE clause once, reused for both the count query and the page query
$whereSql = '';
$params = [];
$types = '';
if ($search !== '') {
    $whereSql = "WHERE customer_name LIKE ? OR email LIKE ? OR company LIKE ?";
    $likeTerm = "%$search%";
    $params = [$likeTerm, $likeTerm, $likeTerm];
    $types = 'sss';
}

// Total count (for pagination + stats)
$countSql = "SELECT COUNT(*) AS total FROM customers $whereSql";
$countStmt = mysqli_prepare($conn, $countSql);
if ($params) mysqli_stmt_bind_param($countStmt, $types, ...$params);
mysqli_stmt_execute($countStmt);
$totalCustomers = mysqli_fetch_assoc(mysqli_stmt_get_result($countStmt))['total'];
$totalPages = max(1, ceil($totalCustomers / $perPage));

// Customers added in the last 7 days (just a nice stat for the dashboard)
$recentSql = "SELECT COUNT(*) AS recent FROM customers WHERE created_at >= (NOW() - INTERVAL 7 DAY)";
$recentResult = mysqli_query($conn, $recentSql);
$recentCount = mysqli_fetch_assoc($recentResult)['recent'];

// Page of customers
$sql = "SELECT * FROM customers $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
$pageParams = array_merge($params, [$perPage, $offset]);
$pageTypes = $types . 'ii';
mysqli_stmt_bind_param($stmt, $pageTypes, ...$pageParams);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$customers = mysqli_fetch_all($result, MYSQLI_ASSOC);

// ---------- Status banner (after redirect from add/edit/delete) ----------
$status = $_GET['status'] ?? '';
$bannerMap = [
    'added'   => ['type' => 'success', 'text' => 'Customer added successfully.'],
    'updated' => ['type' => 'success', 'text' => 'Customer details updated.'],
    'deleted' => ['type' => 'success', 'text' => 'Customer deleted.'],
    'error'   => ['type' => 'error',   'text' => 'Something went wrong. Please try again.'],
];
$banner = $bannerMap[$status] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customers · Spark CRM</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="app-shell">
    <?php include 'partials/sidebar.php'; ?>

    <main class="main">
      <div class="topbar">
        <div>
          <h1>Customers</h1>
          <p class="subtitle">View, search, and manage every customer record.</p>
        </div>
        <a href="add.php" class="btn btn-primary"><?= icon('add') ?> Add customer</a>
      </div>

      <?php if ($banner): ?>
        <div class="alert alert-<?= $banner['type'] ?>">
          <?= icon($banner['type'] === 'success' ? 'check-circle' : 'alert-circle') ?>
          <?= h($banner['text']) ?>
        </div>
      <?php endif; ?>

      <div class="stat-strip">
        <div class="stat-card">
          <div>
            <div class="stat-label">Total customers</div>
            <div class="stat-value"><?= $totalCustomers ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div>
            <div class="stat-label">Added this week</div>
            <div class="stat-value"><?= $recentCount ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div>
            <div class="stat-label">Companies on record</div>
            <div class="stat-value">
              <?php
                $companyCountResult = mysqli_query($conn, "SELECT COUNT(DISTINCT company) AS c FROM customers");
                echo mysqli_fetch_assoc($companyCountResult)['c'];
              ?>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-head">
          <h2>All customers</h2>
          <form class="search-box" method="GET" action="index.php">
            <?= icon('search') ?>
            <input type="text" name="q" placeholder="Search by name, email, or company"
                   value="<?= h($search) ?>">
          </form>
        </div>

        <?php if (empty($customers)): ?>
          <div class="empty-state">
            <div class="empty-icon"><?= icon($search !== '' ? 'empty-search' : 'empty-box') ?></div>
            <strong><?= $search !== '' ? 'No matches found' : 'No customers yet' ?></strong>
            <p><?= $search !== ''
                  ? 'Try a different name, email, or company.'
                  : 'Add your first customer to get started.' ?></p>
            <?php if ($search === ''): ?>
              <a href="add.php" class="btn btn-primary"><?= icon('add') ?> Add customer</a>
            <?php else: ?>
              <a href="index.php" class="btn btn-secondary">Clear search</a>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>Customer</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Added</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($customers as $c): ?>
                <tr>
                  <td>
                    <div class="customer-cell">
                      <div class="avatar" style="background: <?= avatarColor($c['customer_name']) ?>">
                        <?= h(initials($c['customer_name'])) ?>
                      </div>
                      <div>
                        <div class="customer-name"><?= h($c['customer_name']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= h($c['email']) ?></td>
                  <td><?= h($c['phone']) ?></td>
                  <td><span class="company-badge"><?= h($c['company']) ?></span></td>
                  <td><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
                  <td>
                    <div class="actions-cell">
                      <a href="edit.php?id=<?= (int)$c['id'] ?>" class="icon-link" title="Edit"><?= icon('edit') ?></a>
                      <a href="delete.php?id=<?= (int)$c['id'] ?>" class="icon-link danger" title="Delete"><?= icon('trash') ?></a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a class="page-link <?= $p === $page ? 'active' : '' ?>"
               href="index.php?page=<?= $p ?>&q=<?= urlencode($search) ?>"><?= $p ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
  <script src="js/app.js"></script>
</body>
</html>