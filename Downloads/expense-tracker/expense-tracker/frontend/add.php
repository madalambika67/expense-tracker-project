<?php
require_once 'config.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'title'    => trim($_POST['title'] ?? ''),
        'amount'   => $_POST['amount'] ?? '',
        'category' => trim($_POST['category'] ?? 'Other'),
        'date'     => $_POST['date'] ?? date('Y-m-d'),
    ];

    $result = api_request('POST', '/expenses', $payload);

    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        header('Location: index.php?msg=' . urlencode('Expense added successfully!'));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Expense - Expense Tracker</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container narrow">
    <header>
        <h1>➕ Add Expense</h1>
    </header>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="expense-form">
        <label>
            Title
            <input type="text" name="title" required placeholder="e.g. Groceries" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </label>

        <label>
            Amount
            <input type="number" step="0.01" min="0" name="amount" required placeholder="0.00" value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>">
        </label>

        <label>
            Category
            <select name="category">
                <?php
                $categories = ['Food', 'Transport', 'Utilities', 'Entertainment', 'Health', 'Shopping', 'Other'];
                $selected = $_POST['category'] ?? '';
                foreach ($categories as $c) {
                    $sel = $selected === $c ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($c) . "\" $sel>" . htmlspecialchars($c) . "</option>";
                }
                ?>
            </select>
        </label>

        <label>
            Date
            <input type="date" name="date" value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>">
        </label>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Expense</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
