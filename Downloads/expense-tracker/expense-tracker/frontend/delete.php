<?php
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    api_request('DELETE', "/expenses/$id");
}

header('Location: index.php?msg=' . urlencode('Expense deleted.'));
exit;
