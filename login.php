<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = db()->prepare("SELECT id, login, password_hash, role FROM users WHERE login=?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'login' => $user['login'],
            'role' => $user['role'],
        ];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!doctype html>
<html lang="ru">
<head><meta charset="utf-8"><title>Вход</title></head>
<body>
<h2>Вход</h2>
<?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
  <label>Логин: <input name="login" required></label><br><br>
  <label>Пароль: <input type="password" name="password" required></label><br><br>
  <button type="submit">Войти</button>
</form>
</body>
</html>