<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $error = "نام کاربری یا ایمیل قبلا ثبت شده است.";
        } else {
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashedPass])) {
                $success = "ثبت نام با موفقیت انجام شد. اکنون وارد شوید.";
            } else {
                $error = "خطایی رخ داد.";
            }
        }
    } else {
        $error = "لطفا همه فیلدها را پر کنید.";
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت نام</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="auth-box">
            <h2 style="text-align: center; margin-bottom: 30px;">عضویت در سایت</h2>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>نام کاربری</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>ایمیل</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>رمز عبور</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">ثبت نام</button>
            </form>

            <p style="margin-top: 20px; text-align: center; color: var(--text-secondary);">
                قبلا ثبت نام کرده‌اید؟ <a href="login.php" style="color: var(--accent);">وارد شوید</a>
            </p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>