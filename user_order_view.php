<?php
session_start();
require_once 'db.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: profile.php");
    exit;
}

$orderId = $_GET['id'];
$userId = $_SESSION['user_id'];

// Get Order Info (Secure check for user_id)
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("سفارش یافت نشد یا شما دسترسی ندارید.");
}

// Get Order Items
$stmtItems = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmtItems->execute([$orderId]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سفارش <?= $order['tracking_code'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <div class="container header-content">
            <div class="logo"><?= SITE_TITLE ?></div>
            <nav>
                <a href="index.php">بازگشت به خانه</a>
                <a href="profile.php" class="nav-btn">پروفایل من</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="profile.php" class="btn btn-small" style="background: var(--text-secondary);">بازگشت به پنل</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; margin-bottom: 20px;">
                    <h2>
                        سفارش <span
                            style="font-family: monospace; color: var(--accent);"><?= $order['tracking_code'] ?></span>
                    </h2>
                    <div>
                        <span style="color: var(--text-secondary);">وضعیت:</span>
                        <span
                            style="font-weight: bold; margin-right: 5px; color: 
                        <?= $order['status'] == 'completed' ? 'var(--success)' : ($order['status'] == 'cancelled' ? 'var(--danger)' : '#fbbf24') ?>">
                            <?php
                            switch ($order['status']) {
                                case 'pending':
                                    echo 'در انتظار بررسی';
                                    break;
                                case 'completed':
                                    echo 'تکمیل شده';
                                    break;
                                case 'cancelled':
                                    echo 'لغو شده';
                                    break;
                            }
                            ?>
                        </span>
                    </div>
                </div>

                <div class="grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 30px;">
                    <div>
                        <h4 style="color: var(--accent); margin-bottom: 10px;">اطلاعات گیرنده</h4>
                        <p style="color: var(--text-secondary);">تلفن: <span style="color: white;">
                                <?= htmlspecialchars($order['phone']) ?>
                            </span></p>
                        <p style="color: var(--text-secondary);">آدرس:</p>
                        <p
                            style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 8px; margin-top: 5px;">
                            <?= nl2br(htmlspecialchars($order['address'])) ?>
                        </p>
                    </div>
                    <div style="text-align: left;">
                        <p style="color: var(--text-secondary);">تاریخ ثبت:</p>
                        <p style="direction: ltr; display: inline-block;">
                            <?= shamsi($order['created_at']) ?>
                        </p>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>محصول</th>
                                <th>تعداد</th>
                                <th>قیمت واحد</th>
                                <th>جمع کل</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($item['name']) ?>
                                    </td>
                                    <td>
                                        <?= $item['quantity'] ?>
                                    </td>
                                    <td>
                                        <?= number_format($item['price']) ?>
                                    </td>
                                    <td>
                                        <?= number_format($item['price'] * $item['quantity']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr style="background: rgba(255,255,255,0.05);">
                                <td colspan="3" style="text-align: left; font-weight: bold;">مبلغ قابل پرداخت:</td>
                                <td style="color: var(--success); font-weight: bold; font-size: 1.2rem;">
                                    <?= number_format($order['total_price']) ?> تومان
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>