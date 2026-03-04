<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
$title = 'Добавить отчёт';
require_once __DIR__.'/layout.php';

$order_id = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
if ($order_id <= 0) { echo "Некорректный order_id"; exit; }

$stmt = db()->prepare("SELECT o.id, c.full_name AS client_name FROM orders o JOIN clients c ON c.id=o.client_id WHERE o.id=?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();
if (!$order) { echo "Заказ не найден"; exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_date = $_POST['report_date'] ?? date('Y-m-d');
    $work_done   = trim($_POST['work_done'] ?? '');
    $comment     = trim($_POST['comment'] ?? '');

    if ($work_done === '') {
        $error = 'Заполни поле "Выполненные работы"';
    } else {
        $ins = db()->prepare("INSERT INTO work_reports(order_id, report_date, work_done, comment) VALUES (?,?,?,?)");
        $ins->execute([$order_id, $report_date, $work_done, $comment !== '' ? $comment : null]);
        header("Location: order_view.php?id=".$order_id);
        exit;
    }
}
?>
<h2>Добавить отчет о работах</h2>
<p><b>Заказ:</b> #<?= (int)$order['id'] ?> | <b>Клиент:</b> <?= htmlspecialchars($order['client_name']) ?></p>

<?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>

<form method="post">
  <input type="hidden" name="order_id" value="<?= (int)$order_id ?>">

  <p>
    <label>Дата отчета<br>
      <input type="date" name="report_date" value="<?= htmlspecialchars(date('Y-m-d')) ?>">
    </label>
  </p>

  <p>
    <label>Выполненные работы*<br>
      <textarea name="work_done" rows="8" cols="70" required></textarea>
    </label>
  </p>

  <p>
    <label>Комментарий<br>
      <textarea name="comment" rows="3" cols="70"></textarea>
    </label>
  </p>

  <button type="submit">Сохранить отчет</button>
  <a href="order_view.php?id=<?= (int)$order_id ?>" style="margin-left:10px;">Отмена</a>
</form>

</body></html>