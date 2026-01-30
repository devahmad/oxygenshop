<?php
require_once '../db.php';
require_once 'header_admin.php';

// Change Status
if (isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $orderId]);
}

$orders = $pdo->query("
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container admin-layout">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>مدیریت سفارشات</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>شماره سفارش</th>
                        <th>کد پیگیری</th>
                        <th>کاربر</th>
                        <th>مبلغ (تومان)</th>
                        <th>وضعیت</th>
                        <th>تاریخ</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>#
                                <?= $o['id'] ?>
                            </td>
                            <td style="font-family: monospace; color: var(--accent);"><?= $o['tracking_code'] ?></td>
                            <td>
                                <?= htmlspecialchars($o['username']) ?>
                            </td>
                            <td>
                                <?= number_format($o['total_price']) ?>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <select name="status" onchange="this.form.submit()"
                                        style="padding: 5px; border-radius: 5px; background: rgba(0,0,0,0.2); color:white; border:none;">
                                        <option value="pending" <?= $o['status'] == 'pending' ? 'selected' : '' ?>>در انتظار
                                        </option>
                                        <option value="completed" <?= $o['status'] == 'completed' ? 'selected' : '' ?>>تکمیل
                                            شده</option>
                                        <option value="cancelled" <?= $o['status'] == 'cancelled' ? 'selected' : '' ?>>لغو شده
                                        </option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td style="font-size: 0.85rem;">
                                <?= shamsi($o['created_at']) ?>
                            </td>
                            <td>
                                <a href="order_details.php?id=<?= $o['id'] ?>" class="btn btn-small">جزئیات</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>

</html>