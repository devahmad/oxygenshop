<?php
session_start();
require_once 'db.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Actions
if (isset($_GET['action'])) {

    // Add to Cart
    if ($_GET['action'] == 'add' && isset($_GET['id'])) {
        $id = $_GET['id'];
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        } else {
            $_SESSION['cart'][$id] = 1;
        }
        header("Location: cart.php");
        exit;
    }

    // Remove from Cart
    if ($_GET['action'] == 'remove' && isset($_GET['id'])) {
        $id = $_GET['id'];
        unset($_SESSION['cart'][$id]);
        header("Location: cart.php");
        exit;
    }

    // Clear Cart
    if ($_GET['action'] == 'clear') {
        $_SESSION['cart'] = [];
        header("Location: cart.php");
        exit;
    }
}

// Update Quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

// Fetch Cart Products
$cartItems = [];
$totalPrice = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $qty;
        $totalPrice += $subtotal;

        $product['qty'] = $qty;
        $product['subtotal'] = $subtotal;
        $cartItems[] = $product;
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سبد خرید</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <div class="container header-content">
            <div class="logo"><?= SITE_TITLE ?></div>
            <nav>
                <a href="index.php">بازگشت به فروشگاه</a>
                <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.1); margin: 0 10px;"></div>
                <?php if (isLoggedIn()): ?>
                    <a href="profile.php" class="active"><?= htmlspecialchars($_SESSION['username']) ?></a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-small nav-btn">ورود / ثبت نام</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1>سبد خرید</h1>

        <?php if (empty($cartItems)): ?>
            <div class="card" style="padding: 40px; text-align: center;">
                <p>سبد خرید شما خالی است.</p>
                <a href="index.php" class="btn" style="margin-top: 20px;">مشاهده محصولات</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>محصول</th>
                                <th>قیمت واحد</th>
                                <th>تعداد</th>
                                <th>مجموع</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td style="display: flex; align-items: center; gap: 10px;">
                                        <?php if ($item['image']): ?>
                                            <img src="<?= htmlspecialchars($item['image']) ?>"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        <?php endif; ?>
                                        <?= htmlspecialchars($item['name']) ?>
                                    </td>
                                    <td><?= number_format($item['price']) ?></td>
                                    <td>
                                        <input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" min="1"
                                            style="width: 60px; padding: 5px; border-radius: 4px; border: 1px solid #475569; background: #1e293b; color: white;">
                                    </td>
                                    <td><?= number_format($item['subtotal']) ?></td>
                                    <td>
                                        <a href="?action=remove&id=<?= $item['id'] ?>" class="btn btn-small btn-danger">حذف</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; background: var(--card-bg); padding: 20px; border-radius: var(--border-radius);">
                    <div style="font-size: 1.5rem;">
                        مبلغ قابل پرداخت: <span
                            style="color: var(--success); font-weight: bold;"><?= number_format($totalPrice) ?> تومان</span>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="?action=clear" class="btn btn-danger" onclick="return confirm('سبد خرید خالی شود؟')">خالی
                            کردن سبد</a>
                        <button type="submit" name="update_cart" class="btn"
                            style="background: var(--text-secondary);">بروزرسانی</button>
                        <a href="checkout.php" class="btn" style="background: var(--success);">تسویه حساب</a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>