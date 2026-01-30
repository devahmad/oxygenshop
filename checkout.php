<?php
session_start();
require_once 'db.php';

if (!isLoggedIn()) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

if (empty($_SESSION['cart']) && !isset($_GET['success'])) {
    header("Location: index.php");
    exit;
}

// Initialize variables
$products = [];
$totalPrice = 0;
$orderItems = [];

// Calculate Total ONLY if we are in checkout mode (not success) and cart has items
if (!isset($_GET['success']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['id']];
        $totalPrice += $product['price'] * $qty; // Update total price

        $orderItems[] = [
            'id' => $product['id'],
            'price' => $product['price'],
            'qty' => $qty
        ];
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    if (!empty($address) && !empty($phone)) {
        try {
            $pdo->beginTransaction();

            // Generate Random Tracking Code
            $trackingCode = 'MN-' . strtoupper(substr(uniqid(), -8)) . rand(10, 99);

            $stmt = $pdo->prepare("INSERT INTO orders (tracking_code, user_id, total_price, address, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$trackingCode, $_SESSION['user_id'], $totalPrice, $address, $phone]);
            $orderId = $pdo->lastInsertId();

            $stmtHooks = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($orderItems as $item) {
                $stmtHooks->execute([$orderId, $item['id'], $item['qty'], $item['price']]);
            }

            $pdo->commit();

            $_SESSION['cart'] = [];

            // Redirect to success
            header("Location: checkout.php?success=1&tracking=$trackingCode");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "خطا در ثبت سفارش: " . $e->getMessage();
        }
    } else {
        $error = "لطفا آدرس و شماره تماس را وارد کنید.";
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسویه حساب</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <div class="container header-content">
            <div class="logo"><?= SITE_TITLE ?></div>
            <nav>
                <a href="index.php">بازگشت به خانه</a>
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="profile.php" class="nav-btn"><?= htmlspecialchars($_SESSION['username']) ?></a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="card" style="text-align: center; padding: 50px;">
                <div style="font-size: 3rem; margin-bottom: 20px;">🎉</div>
                <h1 style="color: var(--success); margin-bottom: 20px;">سفارش با موفقیت ثبت شد</h1>

                <div class="alert"
                    style="display: inline-block; background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.4); font-size: 1.1rem; padding: 15px 30px;">
                    جهت پیگیری وضعیت، لطفا <strong>وارد پروفایل کاربری</strong> شوید.
                </div>

                <p style="margin-top: 20px; font-size: 0.9rem; color: var(--text-secondary);">
                    شماره پیگیری شما: <strong
                        style="font-size: 1.4rem; color: white;"><?= htmlspecialchars($_GET['tracking']) ?></strong>
                </p>

                <div style="margin-top: 40px; display: flex; justify-content: center; gap: 20px;">
                    <a href="profile.php" class="btn"
                        style="background: var(--accent); padding: 12px 30px; font-size: 1.1rem;">ورود به پروفایل</a>
                    <a href="index.php" class="btn"
                        style="background: var(--card-bg); border: 1px solid rgba(255,255,255,0.1);">بازگشت به خانه</a>
                </div>
            </div>
        <?php else: ?>
            <h1>نهایی کردن خرید</h1>

            <div class="grid" style="grid-template-columns: 2fr 1fr;">

                <!-- Form -->
                <div class="card">
                    <div class="card-body">
                        <h3>اطلاعات ارسال</h3>
                        <?php if ($error): ?>
                            <div class="alert alert-error"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <label>شماره تماس</label>
                                <input type="text" name="phone" placeholder="0912..." required>
                            </div>
                            <div class="form-group">
                                <label>آدرس کامل پستی</label>
                                <textarea name="address" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn" style="width: 100%; background: var(--success);">ثبت سفارش و
                                پرداخت</button>
                        </form>
                    </div>
                </div>

                <!-- Summary -->
                <?php if (!empty($products)): ?>
                    <div class="card" style="height: fit-content;">
                        <div class="card-body">
                            <h3>خلاصه فاکتور</h3>
                            <div style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px;">
                                <?php foreach ($products as $p): ?>
                                    <div
                                        style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem;">
                                        <span><?= htmlspecialchars($p['name']) ?> × <?= $_SESSION['cart'][$p['id']] ?></span>
                                        <span><?= number_format($p['price'] * $_SESSION['cart'][$p['id']]) ?></span>
                                    </div>
                                <?php endforeach; ?>

                                <div
                                    style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 10px; padding-top: 10px; display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1rem;">
                                    <span>مبلغ کل:</span>
                                    <span style="color: var(--success);"><?= number_format($totalPrice) ?> تومان</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>