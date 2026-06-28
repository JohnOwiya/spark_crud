<?php
/**
 * edit.php
 * The "Update" page: loads one customer's existing data into the form,
 * then saves changes back to the database on submit.
 */
require_once 'db.php';
require_once 'helpers.php';
require_once 'icons.php';

$activePage = 'dashboard';
$errors = [];

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?status=error');
    exit;
}

// ---------- Load the existing record ----------
$stmt = mysqli_prepare($conn, "SELECT * FROM customers WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$customer = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$customer) {
    header('Location: index.php?status=error');
    exit;
}

$old = $customer; // pre-fill the form with current values

// ---------- Handle the update ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['customer_name'] = trim($_POST['customer_name'] ?? '');
    $old['email']         = trim($_POST['email'] ?? '');
    $old['phone']         = trim($_POST['phone'] ?? '');
    $old['company']       = trim($_POST['company'] ?? '');

    if ($old['customer_name'] === '') $errors[] = 'Customer name is required.';
    if ($old['email'] === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if ($old['phone'] === '') $errors[] = 'Phone number is required.';
    if ($old['company'] === '') $errors[] = 'Company is required.';

    // Make sure the email isn't already used by a *different* customer
    if (empty($errors)) {
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($checkStmt, 'si', $old['email'], $id);
        mysqli_stmt_execute($checkStmt);
        if (mysqli_stmt_get_result($checkStmt)->num_rows > 0) {
            $errors[] = 'Another customer is already using that email.';
        }
    }

    if (empty($errors)) {
        $updateStmt = mysqli_prepare($conn,
            "UPDATE customers SET customer_name = ?, email = ?, phone = ?, company = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($updateStmt, 'ssssi',
            $old['customer_name'], $old['email'], $old['phone'], $old['company'], $id
        );

        if (mysqli_stmt_execute($updateStmt)) {
            header('Location: index.php?status=updated');
            exit;
        } else {
            $errors[] = 'Could not save changes. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Customer · Spark CRM</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="app-shell">
    <?php include 'partials/sidebar.php'; ?>

    <main class="main">
      <div class="topbar">
        <div>
          <h1>Edit customer</h1>
          <p class="subtitle">Update details for <?= h($customer['customer_name']) ?>.</p>
        </div>
        <a href="index.php" class="btn btn-secondary"><?= icon('arrow-left') ?> Back to customers</a>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?= icon('alert-circle') ?>
          <?= h(implode(' ', $errors)) ?>
        </div>
      <?php endif; ?>

      <form class="form-card" method="POST" action="edit.php?id=<?= $id ?>">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="form-group">
          <label for="customer_name">Customer name</label>
          <input type="text" id="customer_name" name="customer_name"
                 value="<?= h($old['customer_name']) ?>" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email"
                 value="<?= h($old['email']) ?>" required>
        </div>

        <div class="form-group">
          <label for="phone">Phone</label>
          <input type="text" id="phone" name="phone"
                 value="<?= h($old['phone']) ?>" required>
        </div>

        <div class="form-group">
          <label for="company">Company</label>
          <input type="text" id="company" name="company"
                 value="<?= h($old['company']) ?>" required>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" data-loading-text="Saving…">Save changes</button>
          <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </main>
  </div>
  <script src="js/app.js"></script>
</body>
</html>