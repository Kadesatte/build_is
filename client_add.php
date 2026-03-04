<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
require_once __DIR__.'/auth.php';
require_login();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $comment   = trim($_POST['comment'] ?? '');

    if ($full_name === '' || $phone === '' || $address === '') {
        $error = 'Заполни обязательные поля: имя, телефон, адрес';
    } else {
        $stmt = db()->prepare("INSERT INTO clients(full_name, phone, address, comment) VALUES(?,?,?,?)");
        $stmt->execute([$full_name, $phone, $address, $comment !== '' ? $comment : null]);
        header('Location: clients.php');
        exit;
    }
}

$title = 'Добавить клиента';
require_once __DIR__.'/layout.php';
?>
<h2>Добавить клиента</h2>

<?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>

<form method="post">
  <p>
    <label>ФИО / Название*<br>
      <input name="full_name" required>
    </label>
  </p>
  <p>
    <label>Телефон*<br>
      <input name="phone" required>
    </label>
  </p>
  <p>
    <label>Адрес*<br>
      <input name="address" required>
    </label>
  </p>
  <p>
    <label>Комментарий<br>
      <textarea name="comment" rows="3" cols="40"></textarea>
    </label>
  </p>
  <button type="submit">Сохранить</button>
  <a href="clients.php" style="margin-left:10px;">Отмена</a>
</form>

</body></html>