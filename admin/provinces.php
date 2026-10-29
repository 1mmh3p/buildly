<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// التحقق من صلاحيات الأدمن
requireAdmin();

// تعريف متغير action بشكل آمن
$action = isset($_GET['action']) ? $_GET['action'] : '';
$postAction = isset($_POST['action']) ? $_POST['action'] : '';

// ✅ معالجة إضافة محافظة جديدة
if ($postAction === 'add_province') {
    if (!empty($_POST['name'])) {
        $name = trim($_POST['name']);

        $stmt = $pdo->prepare("INSERT INTO provinces (name) VALUES (?)");
        $stmt->execute([$name]);

        header("Location: provinces.php?success=1");
        exit;
    } else {
        header("Location: provinces.php?error=empty_name");
        exit;
    }
}

// ✅ معالجة حذف محافظة
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM provinces WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: provinces.php?success=2");
    exit;
}

// ✅ جلب جميع المحافظات من قاعدة البيانات
$provinces = $pdo->query("SELECT * FROM provinces ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المحافظات - Buildly</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container" style="display: flex; min-height: 100vh;">
        <!-- Sidebar -->
        <?php include '../includes/functions.php'; ?>

        <!-- Main Content -->
        <main class="admin-main" style="flex: 1; padding: 2rem; background: #f5f5f5;">
            <div class="admin-header" style="display: flex; justify-content: between; align-items: center; margin-bottom: 2rem;">
                <h1>إدارة المحافظات</h1>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> إضافة محافظة
                </button>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    if ($_GET['success'] == 1) echo 'تمت إضافة المحافظة بنجاح';
                    if ($_GET['success'] == 2) echo 'تم حذف المحافظة بنجاح';
                    ?>
                </div>
            <?php endif; ?>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المحافظة</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($provinces as $index => $province): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($province['name']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($province['created_at'])); ?></td>
                            <td>
                                <a href="materials.php?id=<?php echo $province['id']; ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-cubes"></i> المواد
                                </a>
                                <a href="provinces.php?action=delete&id=<?php echo $province['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('هل أنت متأكد من حذف هذه المحافظة؟')">
                                    <i class="fas fa-trash"></i> حذف
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal إضافة محافظة -->
    <div id="addModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h3>إضافة محافظة جديدة</h3>
            <form method="POST" action="provinces.php">
                <input type="hidden" name="action" value="add_province">
                <div class="form-group">
                    <label for="provinceName">اسم المحافظة</label>
                    <input type="text" id="provinceName" name="name" class="form-control" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">إضافة</button>
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        // إغلاق المودال عند النقر خارج المحتوى
        window.onclick = function(event) {
            const modal = document.getElementById('addModal');
            if (event.target === modal) {
                closeAddModal();
            }
        }
    </script>
</body>
</html>