<?php
require_once '../db.php';
require_once 'header_admin.php';

// Handle Actions
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Prevent self-delete
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}

if (isset($_GET['promote'])) {
    $id = $_GET['promote'];
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
    $stmt->execute([$id]);
}

if (isset($_GET['demote'])) {
    $id = $_GET['demote'];
    // Prevent self-demote
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
        $stmt->execute([$id]);
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container admin-layout">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>مدیریت کاربران</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>نام کاربری</th>
                        <th>ایمیل</th>
                        <th>نقش</th>
                        <th>تاریخ عضویت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <?= $u['id'] ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($u['username']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($u['email']) ?>
                            </td>
                            <td>
                                <?php if ($u['role'] == 'admin'): ?>
                                    <span style="color: var(--accent);">مدیر</span>
                                <?php else: ?>
                                    کاربر
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $u['created_at'] ?>
                            </td>
                            <td>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="?delete=<?= $u['id'] ?>" class="btn btn-small btn-danger"
                                        onclick="return confirm('حذف شود؟')">حذف</a>
                                    <?php if ($u['role'] == 'user'): ?>
                                        <a href="?promote=<?= $u['id'] ?>" class="btn btn-small">ارتقا به مدیر</a>
                                    <?php else: ?>
                                        <a href="?demote=<?= $u['id'] ?>" class="btn btn-small"
                                            style="background: #fbbf24; color: black;">تنزید درجه</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary);">شما</span>
                                <?php endif; ?>
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