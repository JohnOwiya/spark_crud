<?php
/**
 * delete.php
 * The "Delete" page: shows a confirmation screen first (GET request),
 * and only deletes once the user confirms (POST request).
 * This avoids accidental deletes from a single misclick.
 */
require_once 'db.php';
require_once 'helpers.php';
require_once 'icons.php';

$activePage = 'dashboard';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?status=error');
    exit;
}

// ---------- Handle confirmed deletion ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = mysqli_prepare($conn, "DELETE FROM customers WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: index.php?status=deleted');
    } else {
        header('Location: index.php?status=error');
    }
    exit;
}

// ---------- Otherwise, load the record and show the confirmation screen ----------
$stmt = mysqli_prepare($conn, "SELECT * FROM customers WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$customer = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$customer) {
    header('Location: index.php?status=error');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Customer · Spark CRM</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="app-shell">
    <?php include 'partials/sidebar.php'; ?>

    <main class="main">
      <div class="card confirm-card">
        <div class="confirm-icon"><?= icon('trash') ?></div>
        <h2>Delete <?= h($customer['customer_name']) ?>?</h2>
        <p>
          This removes their record (<?= h($customer['email']) ?>) permanently.
          This can't be undone.
        </p>
        <div class="confirm-actions">
          <form method="POST" action="delete.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger" data-loading-text="Deleting…">Delete customer</button>
          </form>
          <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
      </div>
    </main>
  </div>
  <script src="js/app.js"></script>
</body>
</html>