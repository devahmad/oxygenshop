<?php
require_once '../db.php';
require_once 'header_admin.php';

$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
?>

<div class="container admin-layout">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>داشبورد مدیریتی</h1>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">به پنل مدیریت خوش آمدید.</p>

        <div class="grid">
            <div class="card">
                <div class="card-body">
                    <h2>تعداد کاربران</h2>
                    <p style="font-size: 2rem; color: var(--accent);">
                        <?= $userCount ?>
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h2>تعداد محصولات</h2>
                    <p style="font-size: 2rem; color: var(--success);">
                        <?= $productCount ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>