<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
$title = 'Заказы';
require_once __DIR__.'/layout.php';

$status = $_GET['status'] ?? 'all';
$allowed = ['all','new','in_progress','done'];
if (!in_array($status, $allowed, true)) $status = 'all';

$sql = "
SELECT 
  o.id,
  o.created_date,
  o.planned_finish_date,
  o.status,
  o.amount,
  LEFT(o.description, 60) AS short_desc,
  c.full_name AS client_name,
  u.login AS manager_login
FROM orders o
JOIN clients c ON c.id = o.client_id
JOIN users u ON u.id = o.manager_id
";
$params = [];
if ($status !== 'all') {
    $sql .= " WHERE o.status = ? ";
    $params[] = $status;
}
$sql .= " ORDER BY o.id DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

function status_ru(string $s): string {
    return match($s) {
        'new' => 'Новый',
        'in_progress' => 'В работе',
        'done' => 'Завершен',
        default => $s,
    };
}
?>
<h2>Заказы</h2>

<form method="get" style="margin-bottom:10px;">
  <label>Статус:
    <select name="status">
      <option value="all" <?= $status==='all'?'selected':'' ?>>Все</option>
      <option value="new" <?= $status==='new'?'selected':'' ?>>Новый</option>
      <option value="in_progress" <?= $status==='in_progress'?'selected':'' ?>>В работе</option>
      <option value="done" <?= $status==='done'?'selected':'' ?>>Завершен</option>
    </select>
  </label>
  <button type="submit">Фильтр</button>
  <a href="order_add.php" style="margin-left:10px;">+ Создать заказ</a>
</form>

<table border="1" cellpadding="6" cellspacing="0">
  <tr>
    <th>ID</th>
    <th>Клиент</th>
    <th>Менеджер</th>
    <th>Создан</th>
    <th>Срок</th>
    <th>Статус</th>
    <th>Сумма</th>
    <th>Описание</th>
    <th></th>
  </tr>
  <?php foreach ($orders as $o): ?>
    <tr>
      <td><?= (int)$o['id'] ?></td>
      <td><?= htmlspecialchars($o['client_name']) ?></td>
      <td><?= htmlspecialchars($o['manager_login']) ?></td>
      <td><?= htmlspecialchars($o['created_date']) ?></td>
      <td><?= htmlspecialchars($o['planned_finish_date']) ?></td>
      <td><?= htmlspecialchars(status_ru($o['status'])) ?></td>
      <td><?= htmlspecialchars((string)$o['amount']) ?></td>
      <td><?= htmlspecialchars($o['short_desc']) ?></td>
      <td><a href="order_view.php?id=<?= (int)$o['id'] ?>">Открыть</a></td>
    </tr>
  <?php endforeach; ?>
</table>

</body></html>