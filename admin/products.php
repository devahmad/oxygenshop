<?php
require_once '../db.php';
require_once 'header_admin.php';

$message = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $message = "محصول با موفقیت حذف شد.";
}

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];

    $imagePath = '';

    // Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../uploads/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = 'uploads/' . $fileName;
        } else {
            $error = "خطا در آپلود تصویر.";
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $imagePath]);
        $message = "محصول جدید اضافه شد.";
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container admin-layout">
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>مدیریت محصولات</h1>

        <?php if ($message): ?>
            <div class="alert">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Add Product Form -->
        <div class="card" style="margin-bottom: 30px;">
            <div class="card-body">
                <h3>افزودن محصول جدید</h3>
                <form method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                    <div class="grid" style="grid-template-columns: 1fr 1fr;">
                        <div class="form-group">
                            <label>نام محصول</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>قیمت (تومان)</label>
                            <input type="number" name="price" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>توضیحات</label>
                        <textarea name="description" rows="3"
                            style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:white; border-radius:8px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label>تصویر محصول</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <button type="submit" name="add_product" class="btn">ثبت محصول</button>
                </form>
            </div>
        </div>

        <!-- Product List -->
        <h3>لیست محصولات</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>تصویر</th>
                        <th>نام</th>
                        <th>قیمت</th>
                        <th>تاریخ</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td style="width: 80px;">
                                <?php if ($p['image']): ?>
                                    <img src="../<?= $p['image'] ?>"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($p['name']) ?>
                            </td>
                            <td>
                                <?= number_format($p['price']) ?>
                            </td>
                            <td>
                                <?= $p['created_at'] ?>
                            </td>
                            <td>
                                <a href="?delete=<?= $p['id'] ?>" class="btn btn-small btn-danger"
                                    onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
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