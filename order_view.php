<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
$title = 'Заказ';
require_once __DIR__.'/layout.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { echo "Некорректный ID"; exit; }

$stmt = db()->prepare("
SELECT 
  o.*,
  c.full_name AS client_name,
  u.login AS manager_login
FROM orders o
JOIN clients c ON c.id = o.client_id
JOIN users u ON u.id = o.manager_id
WHERE o.id = ?
");
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) { echo "Заказ не найден"; exit; }

$ts = db()->prepare("SELECT id, title, created_date FROM tech_specs WHERE order_id=?");
$ts->execute([$id]);
$tech = $ts->fetch();

$rep = db()->prepare("SELECT id, report_date, LEFT(work_done, 60) AS short_work FROM work_reports WHERE order_id=? ORDER BY id DESC");
$rep->execute([$id]);
$reports = $rep->fetchAll();

function status_ru(string $s): string {
    return match($s) {
        'new' => 'Новый',
        'in_progress' => 'В работе',
        'done' => 'Завершен',
        default => $s,
    };
}
?>
<h2>Заказ #<?= (int)$order['id'] ?></h2>

<p><b>Клиент:</b> <?= htmlspecialchars($order['client_name']) ?></p>
<p><b>Менеджер:</b> <?= htmlspecialchars($order['manager_login']) ?></p>
<p><b>Дата создания:</b> <?= htmlspecialchars($order['created_date']) ?></p>
<p><b>Плановая дата завершения:</b> <?= htmlspecialchars($order['planned_finish_date']) ?></p>
<p><b>Статус:</b> <?= htmlspecialchars(status_ru($order['status'])) ?></p>
<p><b>Сумма:</b> <?= htmlspecialchars((string)$order['amount']) ?></p>
<p><b>Описание:</b><br><?= nl2br(htmlspecialchars($order['description'])) ?></p>

<hr>
<h3>Техническое задание (ТЗ)</h3>
<?php if ($tech): ?>
  <p>ТЗ создано: <?= htmlspecialchars($tech['created_date']) ?> — <?= htmlspecialchars($tech['title']) ?></p>
  <a href="ts_edit.php?order_id=<?= (int)$order['id'] ?>">Открыть/редактировать ТЗ</a>
<?php else: ?>
  <p>ТЗ ещё не создано.</p>
  <a href="ts_edit.php?order_id=<?= (int)$order['id'] ?>">+ Создать ТЗ</a>
<?php endif; ?>

<hr>
<h3>Отчеты о выполненных работах</h3>
<p><a href="report_add.php?order_id=<?= (int)$order['id'] ?>">+ Добавить отчет</a></p>

<?php if (!$reports): ?>
  <p>Отчётов пока нет.</p>
<?php else: ?>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Дата</th>
      <th>Кратко</th>
    </tr>
    <?php foreach ($reports as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= htmlspecialchars($r['report_date']) ?></td>
        <td><?= htmlspecialchars($r['short_work']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<p style="margin-top:10px;"><a href="orders.php">← Назад к списку заказов</a></p>

</body></html>