<?php
declare(strict_types=1);
require_once __DIR__.'/auth.php';
require_login();
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="ru">
<head><meta charset="utf-8"><title>ИС</title></head>
<body>
<p>Вы вошли как: <b><?= htmlspecialchars($user['login']) ?></b> (<?= htmlspecialchars($user['role']) ?>) |
<a href="logout.php">Выйти</a></p>

<h2>Меню</h2>
<ul>
  <li><a href="clients.php">Клиенты</a></li>
  <li><a href="orders.php">Заказы</a></li>
</ul>
</body>
</html>