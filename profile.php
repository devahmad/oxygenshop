<?php
session_start();
require_once 'db.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get User Orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل کاربری</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <div class="container header-content">
            <div class="logo"><?= SITE_TITLE ?></div>
            <nav>
                <a href="index.php">بازگشت به فروشگاه</a>
                <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.1); margin: 0 10px;"></div>
                <span style="color: var(--text-secondary);"><?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php" class="btn btn-small btn-danger">خروج</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="grid" style="grid-template-columns: 1fr 3fr;">
            <!-- Sidebar Info -->
            <div class="card" style="height: fit-content;">
                <div class="card-body">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <div
                            style="width: 80px; height: 80px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 2rem; color: white;">
                            <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                        </div>
                    </div>
                    <h3 style="text-align: center;"><?= htmlspecialchars($_SESSION['username']) ?></h3>
                    <p style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">کاربر عادی</p>

                    <div style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                        <a href="profile.php" class="btn"
                            style="width: 100%; text-align: center; margin-bottom: 10px;">سفارشات من</a>
                        <?php if (isAdmin()): ?>
                            <a href="admin/dashboard.php" class="btn"
                                style="width: 100%; text-align: center; background: var(--text-secondary);">پنل مدیریت</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="card">
                <div class="card-body">
                    <h2>سفارشات من</h2>

                    <?php if (empty($orders)): ?>
                        <p style="margin-top: 20px; color: var(--text-secondary);">شما هنوز سفارشی ثبت نکرده‌اید.</p>
                    <?php else: ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>کد پیگیری</th>
                                        <th>تاریخ</th>
                                        <th>مبلغ (تومان)</th>
                                        <th>وضعیت</th>
                                        <th>جزئیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $o): ?>
                                        <tr>
                                            <td style="font-family: monospace; font-size: 1.1rem; color: var(--accent);">
                                                <?= $o['tracking_code'] ?>
                                            </td>
                                            <td><?= shamsi($o['created_at']) ?></td>
                                            <td><?= number_format($o['total_price']) ?></td>
                                            <td>
                                                <span style="padding: 2px 8px; border-radius: 4px; font-size: 0.9rem; background: 
                                            <?= $o['status'] == 'completed' ? 'rgba(16, 185, 129, 0.2); color: #34d399' :
                                                ($o['status'] == 'cancelled' ? 'rgba(239, 68, 68, 0.2); color: #f87171' :
                                                    'rgba(251, 191, 36, 0.2); color: #fbbf24') ?>">
                                                    <?php
                                                    switch ($o['status']) {
                                                        case 'pending':
                                                            echo 'در انتظار';
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
                                            </td>
                                            <td>
                                                <a href="user_order_view.php?id=<?= $o['id'] ?>" class="btn btn-small">نمایش</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>