<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
$title = 'Клиенты';
require_once __DIR__.'/layout.php';

$q = trim($_GET['q'] ?? '');

$sql = "SELECT id, full_name, phone, address, comment FROM clients";
$params = [];

if ($q !== '') {
    $sql .= " WHERE full_name LIKE ? OR phone LIKE ? OR address LIKE ?";
    $like = "%$q%";
    $params = [$like, $like, $like];
}
$sql .= " ORDER BY id DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$clients = $stmt->fetchAll();
?>
<h2>Клиенты</h2>

<form method="get" style="margin-bottom:10px;">
  <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Поиск: имя/телефон/адрес">
  <button type="submit">Найти</button>
  <a href="client_add.php" style="margin-left:10px;">+ Добавить клиента</a>
</form>

<table border="1" cellpadding="6" cellspacing="0">
  <tr>
    <th>ID</th>
    <th>ФИО / Название</th>
    <th>Телефон</th>
    <th>Адрес</th>
    <th>Комментарий</th>
  </tr>
  <?php foreach ($clients as $c): ?>
    <tr>
      <td><?= (int)$c['id'] ?></td>
      <td><?= htmlspecialchars($c['full_name']) ?></td>
      <td><?= htmlspecialchars($c['phone']) ?></td>
      <td><?= htmlspecialchars($c['address']) ?></td>
      <td><?= htmlspecialchars((string)$c['comment']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>

</body></html>