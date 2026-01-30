<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-layout {
            min-height: 80vh;
        }
    </style>
</head>

<body>

    <header>
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="logo">مدیریت فروشگاه</div>
                <nav>
                    <a href="../index.php">مشاهده سایت</a>
                    <a href="../logout.php" class="btn btn-small btn-danger">خروج</a>
                </nav>
            </div>
        </div>
    </header>