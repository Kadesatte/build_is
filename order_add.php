<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
require_once __DIR__.'/auth.php';
require_login();

$user = $_SESSION['user'];
$manager_id = (int)$user['id']; // менеджер = кто вошел

// список клиентов для выпадающего списка
$clients = db()->query("SELECT id, full_name FROM clients ORDER BY full_name")->fetchAll();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = (int)($_POST['client_id'] ?? 0);
    $created_date = $_POST['created_date'] ?? date('Y-m-d');
    $planned_finish_date = $_POST['planned_finish_date'] ?? date('Y-m-d');
    $description = trim($_POST['description'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);
    $status = $_POST['status'] ?? 'new';

    $allowed_status = ['new','in_progress','done'];
    if (!in_array($status, $allowed_status, true)) $status = 'new';

    if ($client_id <= 0 || $description === '') {
        $error = 'Выбери клиента и заполни описание работ';
    } else {
        $stmt = db()->prepare("
          INSERT INTO orders (client_id, manager_id, created_date, description, amount, planned_finish_date, status)
          VALUES (?,?,?,?,?,?,?)
        ");
        $stmt->execute([$client_id, $manager_id, $created_date, $description, $amount, $planned_finish_date, $status]);

        $newId = (int)db()->lastInsertId();
        header("Location: order_view.php?id=".$newId);
        exit;
    }
}

$title = 'Создать заказ';
require_once __DIR__.'/layout.php';

function opt(string $value, string $label, string $current): string {
    $sel = $value === $current ? 'selected' : '';
    return "<option value=\"{$value}\" {$sel}>{$label}</option>";
}
?>
<h2>Создать заказ</h2>

<?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>

<form method="post">
  <p>
    <label>Клиент*<br>
      <select name="client_id" required>
        <option value="">-- выбери --</option>
        <?php foreach ($clients as $c): ?>
          <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
  </p>

  <p>
    <label>Дата создания<br>
      <input type="date" name="created_date" value="<?= htmlspecialchars(date('Y-m-d')) ?>">
    </label>
  </p>

  <p>
    <label>Плановая дата завершения<br>
      <input type="date" name="planned_finish_date" value="<?= htmlspecialchars(date('Y-m-d', strtotime('+7 days'))) ?>">
    </label>
  </p>

  <p>
    <label>Статус<br>
      <select name="status">
        <?= opt('new','Новый','new') ?>
        <?= opt('in_progress','В работе','new') ?>
        <?= opt('done','Завершен','new') ?>
      </select>
    </label>
  </p>

  <p>
    <label>Сумма<br>
      <input type="number" step="0.01" name="amount" value="0">
    </label>
  </p>

  <p>
    <label>Описание работ*<br>
      <textarea name="description" rows="4" cols="50" required></textarea>
    </label>
  </p>

  <button type="submit">Создать</button>
  <a href="orders.php" style="margin-left:10px;">Отмена</a>
</form>

</body></html>