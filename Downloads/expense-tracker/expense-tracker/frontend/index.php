<?php
require_once 'config.php';

$expenses = api_request('GET', '/expenses');
$summary  = api_request('GET', '/summary');

$apiError = isset($expenses['error']) ? $expenses['error'] : null;
if ($apiError) {
    $expenses = [];
    $summary = ['total' => 0, 'by_category' => []];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Expense Tracker</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>💰 Expense Tracker</h1>
        <p class="subtitle">PHP frontend + Python (Flask) backend</p>
    </header>

    <?php if ($apiError): ?>
        <div class="alert alert-error"><?= htmlspecialchars($apiError) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="summary-cards">
        <div class="card total-card">
            <span class="card-label">Total Spent</span>
            <span class="card-value"><?= format_money($summary['total'] ?? 0) ?></span>
        </div>
        <div class="card">
            <span class="card-label">By Category</span>
            <ul class="category-list">
                <?php foreach (($summary['by_category'] ?? []) as $cat): ?>
                    <li><span><?= htmlspecialchars($cat['category']) ?></span> <strong><?= format_money($cat['total']) ?></strong></li>
                <?php endforeach; ?>
                <?php if (empty($summary['by_category'])): ?>
                    <li class="muted">No expenses yet</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="actions">
        <a href="add.php" class="btn btn-primary">+ Add Expense</a>
    </div>

    <table class="expense-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Category</th>
                <th>Amount</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($expenses)): ?>
                <tr><td colspan="5" class="muted">No expenses recorded yet.</td></tr>
            <?php else: ?>
                <?php foreach ($expenses as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['date']) ?></td>
                        <td><?= htmlspecialchars($e['title']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($e['category']) ?></span></td>
                        <td class="amount"><?= format_money($e['amount']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int)$e['id'] ?>" class="link-edit">Edit</a>
                            <a href="delete.php?id=<?= (int)$e['id'] ?>"
                               class="link-delete"
                               onclick="return confirm('Delete this expense?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
