<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $error = "نام کاربری یا رمز عبور اشتباه است.";
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
    <title>ورود به سایت</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="auth-box">
            <h2 style="text-align: center; margin-bottom: 30px;">ورود به حساب</h2>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>نام کاربری</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>رمز عبور</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">ورود</button>
            </form>

            <p style="margin-top: 20px; text-align: center; color: var(--text-secondary);">
                حساب ندارید؟ <a href="register.php" style="color: var(--accent);">ثبت نام کنید</a>
            </p>
            <p style="margin-top: 10px; text-align: center; font-size: 0.8rem; color: var(--text-secondary);">
                (ادمین پیش‌فرض: admin / 123456)
            </p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>