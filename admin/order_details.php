<?php
require_once '../db.php';
require_once 'header_admin.php';

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$orderId = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT o.*, u.username, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("سفارش یافت نشد.");
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

<div class="container admin-layout">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            سفارش <span style="font-family: monospace; color: var(--accent);"><?= $order['tracking_code'] ?></span>
            </h1>
            <a href="orders.php" class="btn btn-small" style="background: var(--text-secondary);">بازگشت</a>
        </div>

        <div class="card" style="margin-top: 20px;">
            <div class="card-body">
                <div class="grid" style="grid-template-columns: 1fr 1fr;">
                    <div>
                        <h3 style="margin-bottom: 15px; color: var(--accent);">اطلاعات مشتری</h3>
                        <p><strong>نام کاربر:</strong>
                            <?= htmlspecialchars($order['username']) ?>
                        </p>
                        <p><strong>ایمیل:</strong>
                            <?= htmlspecialchars($order['email']) ?>
                        </p>
                        <p><strong>تلفن:</strong>
                            <?= htmlspecialchars($order['phone']) ?>
                        </p>
                        <p><strong>تاریخ سفارش:</strong>
                            <span
                                style="direction: ltr; display: inline-block;"><?= shamsi($order['created_at']) ?></span>
                        </p>
                    </div>
                    <div>
                        <h3 style="margin-bottom: 15px; color: var(--accent);">اطلاعات ارسال</h3>
                        <p><strong>آدرس:</strong></p>
                        <p
                            style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 8px; margin-top: 5px;">
                            <?= nl2br(htmlspecialchars($order['address'])) ?>
                        </p>
                        <p style="margin-top: 10px;"><strong>وضعیت:</strong>
                            <span
                                style="padding: 2px 8px; border-radius: 4px; background: 
                                <?= $order['status'] == 'completed' ? 'var(--success)' : ($order['status'] == 'cancelled' ? 'var(--danger)' : '#fbbf24; color:black') ?>">
                                <?= $order['status'] ?>
                            </span>
                        </p>
                    </div>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px;">اقلام سفارش</h3>
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
                            <tr style="background: rgba(255,255,255,0.05); font-weight: bold;">
                                <td colspan="3" style="text-align: left;">مبلغ نهایی:</td>
                                <td style="color: var(--success); font-size: 1.2rem;">
                                    <?= number_format($order['total_price']) ?> تومان
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

</body>

</html>