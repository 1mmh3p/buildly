<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// التحقق من صلاحيات الأدمن
requireAdmin();

// تعريف المفاتيح لتفادي الأخطاء (warnings)
$action = isset($_GET['action']) ? $_GET['action'] : '';
$postAction = isset($_POST['action']) ? $_POST['action'] : '';

// ✅ معالجة إضافة شركة جديدة
if ($postAction === 'add_company') {
    // التحقق من المدخلات
    $name = trim($_POST['name'] ?? '');
    $province_id = (int)($_POST['province_id'] ?? 0);
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (!empty($name) && $province_id > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO companies (name, province_id, phone, email, address)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $province_id, $phone, $email, $address]);
        header("Location: companies.php?success=1");
        exit;
    } else {
        header("Location: companies.php?error=invalid_input");
        exit;
    }
}

// ✅ معالجة حذف شركة
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: companies.php?success=2");
    exit;
}

// ✅ جلب جميع الشركات مع أسماء المحافظات
$companies = $pdo->query("
    SELECT c.*, p.name AS province_name
    FROM companies c
    LEFT JOIN provinces p ON c.province_id = p.id
    ORDER BY c.name ASC
")->fetchAll(PDO::FETCH_ASSOC);

// ✅ جلب المحافظات للقائمة المنسدلة
$provinces = $pdo->query("SELECT * FROM provinces ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الشركات - Buildly</title>
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
                <h1>إدارة الشركات</h1>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> إضافة شركة
                </button>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    if ($_GET['success'] == 1) echo 'تمت إضافة الشركة بنجاح';
                    if ($_GET['success'] == 2) echo 'تم حذف الشركة بنجاح';
                    ?>
                </div>
            <?php endif; ?>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الشركة</th>
                            <th>المحافظة</th>
                            <th>الهاتف</th>
                            <th>البريد الإلكتروني</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $index => $company): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($company['name']); ?></td>
                            <td><?php echo htmlspecialchars($company['province_name']); ?></td>
                            <td><?php echo htmlspecialchars($company['phone']); ?></td>
                            <td><?php echo htmlspecialchars($company['email']); ?></td>
                            <td>
                                <a href="companies.php?action=delete&id=<?php echo $company['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('هل أنت متأكد من حذف هذه الشركة؟')">
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

    <!-- Modal إضافة شركة -->
    <div id="addModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h3>إضافة شركة جديدة</h3>
            <form method="POST" action="companies.php">
                <input type="hidden" name="action" value="add_company">
                <div class="form-group">
                    <label for="companyName">اسم الشركة</label>
                    <input type="text" id="companyName" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="province">المحافظة</label>
                    <select id="province" name="province_id" class="form-control" required>
                        <option value="">اختر المحافظة</option>
                        <?php foreach ($provinces as $province): ?>
                            <option value="<?php echo $province['id']; ?>"><?php echo htmlspecialchars($province['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="phone">الهاتف</label>
                    <input type="text" id="phone" name="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label for="address">العنوان</label>
                    <textarea id="address" name="address" class="form-control" rows="3"></textarea>
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