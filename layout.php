<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title ?? 'ИС') ?></title>
</head>
<body>
<p>
  Вы вошли как: <b><?= htmlspecialchars($user['login']) ?></b>
  (<?= htmlspecialchars($user['role']) ?>) |
  <a href="index.php">Главная</a> |
  <a href="clients.php">Клиенты</a> |
  <a href="orders.php">Заказы</a> |
  <a href="logout.php">Выйти</a>
</p>
<hr>
</body></html>