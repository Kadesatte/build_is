<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
$title = 'Техническое задание';
require_once __DIR__.'/layout.php';

$order_id = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
if ($order_id <= 0) { echo "Некорректный order_id"; exit; }

// проверим что заказ существует
$stmt = db()->prepare("SELECT o.id, c.full_name AS client_name FROM orders o JOIN clients c ON c.id=o.client_id WHERE o.id=?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();
if (!$order) { echo "Заказ не найден"; exit; }

// загрузим текущее ТЗ (если есть)
$stmt = db()->prepare("SELECT * FROM tech_specs WHERE order_id=?");
$stmt->execute([$order_id]);
$ts = $stmt->fetch();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_ts = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');

    if ($title_ts === '' || $content === '') {
        $error = 'Заполни название и текст ТЗ';
    } else {
        if ($ts) {
            // update
            $upd = db()->prepare("UPDATE tech_specs SET title=?, content=? WHERE order_id=?");
            $upd->execute([$title_ts, $content, $order_id]);
        } else {
            // insert
            $ins = db()->prepare("INSERT INTO tech_specs(order_id, title, content, created_date) VALUES (?,?,?,?)");
            $ins->execute([$order_id, $title_ts, $content, date('Y-m-d')]);
        }
        header("Location: order_view.php?id=".$order_id);
        exit;
    }
}

// значения в форму
$form_title = $ts['title'] ?? '';
$form_content = $ts['content'] ?? '';
?>
<h2>Техническое задание (ТЗ)</h2>
<p><b>Заказ:</b> #<?= (int)$order['id'] ?> | <b>Клиент:</b> <?= htmlspecialchars($order['client_name']) ?></p>

<?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>

<form method="post">
  <input type="hidden" name="order_id" value="<?= (int)$order_id ?>">

  <p>
    <label>Название ТЗ*<br>
      <input name="title" value="<?= htmlspecialchars($form_title) ?>" required style="width:420px;">
    </label>
  </p>

  <p>
    <label>Текст ТЗ*<br>
      <textarea name="content" rows="10" cols="70" required><?= htmlspecialchars($form_content) ?></textarea>
    </label>
  </p>

  <button type="submit"><?= $ts ? 'Сохранить изменения' : 'Создать ТЗ' ?></button>
  <a href="order_view.php?id=<?= (int)$order_id ?>" style="margin-left:10px;">Отмена</a>
</form>

</body></html>