<?php
/**
 * add.php
 * The "Create" page: shows a form, and on submit, inserts a new customer.
 */
require_once 'db.php';
require_once 'helpers.php';
require_once 'icons.php';

$activePage = 'add';
$errors = [];
$old = ['customer_name' => '', 'email' => '', 'phone' => '', 'company' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['customer_name'] = trim($_POST['customer_name'] ?? '');
    $old['email']         = trim($_POST['email'] ?? '');
    $old['phone']         = trim($_POST['phone'] ?? '');
    $old['company']       = trim($_POST['company'] ?? '');

    // ---------- Validation ----------
    if ($old['customer_name'] === '') $errors[] = 'Customer name is required.';
    if ($old['email'] === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if ($old['phone'] === '') $errors[] = 'Phone number is required.';
    if ($old['company'] === '') $errors[] = 'Company is required.';

    // Check email isn't already used (the DB also enforces this, but a friendly check first is nicer)
    if (empty($errors)) {
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE email = ?");
        mysqli_stmt_bind_param($checkStmt, 's', $old['email']);
        mysqli_stmt_execute($checkStmt);
        if (mysqli_stmt_get_result($checkStmt)->num_rows > 0) {
            $errors[] = 'A customer with that email already exists.';
        }
    }

    // ---------- Insert ----------
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO customers (customer_name, email, phone, company) VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'ssss',
            $old['customer_name'], $old['email'], $old['phone'], $old['company']
        );

        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php?status=added');
            exit;
        } else {
            $errors[] = 'Could not save the customer. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Customer · Spark CRM</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="app-shell">
    <?php include 'partials/sidebar.php'; ?>

    <main class="main">
      <div class="topbar">
        <div>
          <h1>Add customer</h1>
          <p class="subtitle">Create a new customer record.</p>
        </div>
        <a href="index.php" class="btn btn-secondary"><?= icon('arrow-left') ?> Back to customers</a>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?= icon('alert-circle') ?>
          <?= h(implode(' ', $errors)) ?>
        </div>
      <?php endif; ?>

      <form class="form-card" method="POST" action="add.php">
        <div class="form-group">
          <label for="customer_name">Customer name</label>
          <input type="text" id="customer_name" name="customer_name"
                 value="<?= h($old['customer_name']) ?>" placeholder="e.g. Amina Yusuf" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email"
                 value="<?= h($old['email']) ?>" placeholder="e.g. amina@company.com" required>
        </div>

        <div class="form-group">
          <label for="phone">Phone</label>
          <input type="text" id="phone" name="phone"
                 value="<?= h($old['phone']) ?>" placeholder="e.g. +254 712 345 678" required>
        </div>

        <div class="form-group">
          <label for="company">Company</label>
          <input type="text" id="company" name="company"
                 value="<?= h($old['company']) ?>" placeholder="e.g. Brightleaf Foods" required>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" data-loading-text="Saving…">Save customer</button>
          <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </main>
  </div>
  <script src="js/app.js"></script>
</body>
</html>