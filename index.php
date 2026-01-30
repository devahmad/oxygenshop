<?php
session_start();
require_once 'db.php';
require_once 'config.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= SITE_TITLE ?>
    </title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <div class="container header-content">
            <div class="logo"><?= SITE_TITLE ?></div>
            <nav>
                <a href="index.php" class="active">خانه</a>

                <a href="cart.php" class="cart-btn">
                    <span>سبد خرید</span>
                    <span class="cart-badge">
                        <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>
                    </span>
                </a>

                <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.1); margin: 0 10px;"></div>

                <?php if (isLoggedIn()): ?>
                    <a href="profile.php">
                        <span style="color: var(--text-secondary); font-size: 0.9em;">سلام</span>
                        <?= htmlspecialchars($_SESSION['username']) ?>
                    </a>
                    <?php if (isAdmin()): ?>
                        <a href="admin/dashboard.php" class="nav-btn">پنل مدیریت</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-small btn-danger" style="margin-right: 5px;">خروج</a>
                <?php else: ?>
                    <a href="login.php">ورود</a>
                    <a href="register.php" class="btn btn-small nav-btn">ثبت نام</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="hero" style="text-align: center; margin-bottom: 50px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 20px;">بهترین محصولات با بهترین قیمت</h1>
            <p style="color: var(--text-secondary); margin-bottom: 30px;">تجربه خریدی متفاوت در فروشگاه ما</p>
        </div>

        <div class="grid">
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <div class="card-img">
                        <?php if ($product['image']): ?>
                            <img src="<?= htmlspecialchars($product['image']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <span>بدون تصویر</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">
                            <?= htmlspecialchars($product['name']) ?>
                        </h3>
                        <p class="card-price">
                            <?= number_format($product['price']) ?> تومان
                        </p>
                        <a href="cart.php?action=add&id=<?= $product['id'] ?>" class="btn"
                            style="width: 100%; text-align: center; margin-top: 10px;">افزودن به سبد
                            خرید</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($products) == 0): ?>
            <p style="text-align: center; color: var(--text-secondary);">هنوز محصولی اضافه نشده است.</p>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>