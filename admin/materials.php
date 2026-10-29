<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireAdmin();

// جلب جميع المواد مع بيانات المحافظات
$materials = $pdo->query("
    SELECT pm.*, p.name as province_name 
    FROM province_materials pm 
    LEFT JOIN provinces p ON pm.province_id = p.id 
    ORDER BY p.name, pm.material_name
")->fetchAll();

// جلب المحافظات
$provinces = $pdo->query("SELECT * FROM provinces ORDER BY name")->fetchAll();

// معالجة إضافة مادة جديدة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_material'])) {
    $province_id = $_POST['province_id'];
    $material_name = $_POST['material_name'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    
    $stmt = $pdo->prepare("INSERT INTO province_materials (province_id, material_name, unit, price) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$province_id, $material_name, $unit, $price])) {
        $_SESSION['success'] = "تم إضافة المادة بنجاح";
        header("Location: materials.php");
        exit();
    } else {
        $error = "حدث خطأ أثناء إضافة المادة";
    }
}

// معالجة تعديل المادة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_material'])) {
    $id = $_POST['id'];
    $province_id = $_POST['province_id'];
    $material_name = $_POST['material_name'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    
    $stmt = $pdo->prepare("UPDATE province_materials SET province_id = ?, material_name = ?, unit = ?, price = ?, last_updated = CURRENT_DATE WHERE id = ?");
    if ($stmt->execute([$province_id, $material_name, $unit, $price, $id])) {
        $_SESSION['success'] = "تم تحديث المادة بنجاح";
        header("Location: materials.php");
        exit();
    } else {
        $error = "حدث خطأ أثناء تحديث المادة";
    }
}

// معالجة حذف المادة
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM province_materials WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "تم حذف المادة بنجاح";
        header("Location: materials.php");
        exit();
    } else {
        $error = "حدث خطأ أثناء حذف المادة";
    }
}

// التحقق من وجود رسالة نجاح في الجلسة
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المواد - Buildly</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="admin-container" style="display: flex; min-height: calc(100vh - 140px);">
        <!-- Sidebar -->
        <aside class="admin-sidebar" style="width: 250px; background: var(--dark-color); color: white;">
            <div class="sidebar-header" style="padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h3 style="margin: 0; color: var(--secondary-color);">لوحة التحكم</h3>
                <p style="margin: 0.5rem 0 0; font-size: 0.9rem; color: var(--gray-color);">مرحباً، <?php echo $_SESSION['username']; ?></p>
            </div>
            <nav class="sidebar-nav" style="padding: 1rem 0;">
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;">
                        <a href="dashboard.php" class="sidebar-link" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
                            <i class="fas fa-tachometer-alt" style="margin-left: 0.5rem;"></i>
                            الإحصائيات
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="materials.php" class="sidebar-link active" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition); background-color: rgba(255,255,255,0.1);">
                            <i class="fas fa-cubes" style="margin-left: 0.5rem;"></i>
                            إدارة المواد
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="provinces.php" class="sidebar-link" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
                            <i class="fas fa-map-marker-alt" style="margin-left: 0.5rem;"></i>
                            إدارة المحافظات
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="companies.php" class="sidebar-link" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
                            <i class="fas fa-building" style="margin-left: 0.5rem;"></i>
                            إدارة الشركات
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="users.php" class="sidebar-link" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
                            <i class="fas fa-users" style="margin-left: 0.5rem;"></i>
                            إدارة المستخدمين
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="messages.php" class="sidebar-link" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
                            <i class="fas fa-envelope" style="margin-left: 0.5rem;"></i>
                            الرسائل
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="../index.html" class="sidebar-link" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
                            <i class="fas fa-home" style="margin-left: 0.5rem;"></i>
                            العودة للموقع
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="logout.php" class="sidebar-link" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
                            <i class="fas fa-sign-out-alt" style="margin-left: 0.5rem;"></i>
                            تسجيل الخروج
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main" style="flex: 1; padding: 2rem; background: #f5f5f5;">
            <div class="admin-header" style="display: flex; justify-content: between; align-items: center; margin-bottom: 2rem;">
                <h1 style="margin: 0;">إدارة مواد البناء حسب المحافظة</h1>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> إضافة مادة جديدة
                </button>
            </div>

            <?php if (isset($success)): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <!-- Materials Table -->
            <div class="admin-table-container" style="background: white; border-radius: 8px; box-shadow: var(--shadow); overflow: hidden;">
                <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--secondary-color); color: white;">
                            <th style="padding: 1rem; text-align: right;">المادة</th>
                            <th style="padding: 1rem; text-align: right;">المحافظة</th>
                            <th style="padding: 1rem; text-align: right;">الوحدة</th>
                            <th style="padding: 1rem; text-align: right;">السعر ($)</th>
                            <th style="padding: 1rem; text-align: right;">آخر تحديث</th>
                            <th style="padding: 1rem; text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($materials)): ?>
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--gray-color);">
                                    لا توجد مواد مضافة حالياً
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($materials as $material): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($material['material_name']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($material['province_name']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($material['unit']); ?></td>
                                <td style="padding: 1rem;"><?php echo number_format($material['price'], 2); ?></td>
                                <td style="padding: 1rem;"><?php echo $material['last_updated']; ?></td>
                                <td style="padding: 1rem; text-align: center;">
                                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(
                                        <?php echo $material['id']; ?>, 
                                        '<?php echo addslashes($material['material_name']); ?>', 
                                        '<?php echo addslashes($material['unit']); ?>', 
                                        <?php echo $material['price']; ?>,
                                        <?php echo $material['province_id']; ?>
                                    )">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <a href="materials.php?delete=<?php echo $material['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذه المادة؟')">
                                        <i class="fas fa-trash"></i> حذف
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Statistics -->
            <div class="materials-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--secondary-color);">
                        <?php echo count($materials); ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">عدد المواد</div>
                </div>
                
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--success-color);">
                        <?php echo count($provinces); ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">عدد المحافظات</div>
                </div>

                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--info-color);">
                        <?php echo date('Y-m-d'); ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">تاريخ اليوم</div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Material Modal -->
    <div id="addModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: white; margin: 5% auto; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px; position: relative;">
            <span class="close" onclick="closeAddModal()" style="position: absolute; left: 1rem; top: 1rem; font-size: 1.5rem; cursor: pointer;">&times;</span>
            <h3>إضافة مادة جديدة</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="province_id" class="form-label">المحافظة</label>
                    <select id="province_id" name="province_id" class="form-control" required>
                        <option value="">اختر المحافظة</option>
                        <?php foreach ($provinces as $province): ?>
                            <option value="<?php echo $province['id']; ?>"><?php echo htmlspecialchars($province['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="material_name" class="form-label">اسم المادة</label>
                    <input type="text" id="material_name" name="material_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="unit" class="form-label">الوحدة</label>
                    <select id="unit" name="unit" class="form-control" required>
                        <option value="">اختر الوحدة</option>
                        <option value="طن">طن</option>
                        <option value="كغ">كغ</option>
                        <option value="م³">م³</option>
                        <option value="قطعة">قطعة</option>
                        <option value="لتر">لتر</option>
                        <option value="م²">م²</option>
                        <option value="جالون">جالون</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price" class="form-label">السعر ($)</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-actions" style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">إلغاء</button>
                    <button type="submit" name="add_material" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Material Modal -->
    <div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: white; margin: 5% auto; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px; position: relative;">
            <span class="close" onclick="closeEditModal()" style="position: absolute; left: 1rem; top: 1rem; font-size: 1.5rem; cursor: pointer;">&times;</span>
            <h3>تعديل المادة</h3>
            <form method="POST" action="">
                <input type="hidden" id="edit_id" name="id">
                
                <div class="form-group">
                    <label for="edit_province_id" class="form-label">المحافظة</label>
                    <select id="edit_province_id" name="province_id" class="form-control" required>
                        <option value="">اختر المحافظة</option>
                        <?php foreach ($provinces as $province): ?>
                            <option value="<?php echo $province['id']; ?>"><?php echo htmlspecialchars($province['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_material_name" class="form-label">اسم المادة</label>
                    <input type="text" id="edit_material_name" name="material_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_unit" class="form-label">الوحدة</label>
                    <select id="edit_unit" name="unit" class="form-control" required>
                        <option value="">اختر الوحدة</option>
                        <option value="طن">طن</option>
                        <option value="كغ">كغ</option>
                        <option value="م³">م³</option>
                        <option value="قطعة">قطعة</option>
                        <option value="لتر">لتر</option>
                        <option value="م²">م²</option>
                        <option value="جالون">جالون</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_price" class="form-label">السعر ($)</label>
                    <input type="number" id="edit_price" name="price" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-actions" style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">إلغاء</button>
                    <button type="submit" name="edit_material" class="btn btn-primary">تحديث</button>
                </div>
            </form>
        </div>
    </div>


    <script src="../js/script.js"></script>
    <script>
        // فتح وإغلاق مودال الإضافة
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        
        // فتح وإغلاق مودال التعديل
        function openEditModal(id, material_name, unit, price, province_id) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_material_name').value = material_name;
            document.getElementById('edit_unit').value = unit;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_province_id').value = province_id;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // إغلاق المودال عند النقر خارج المحتوى
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            
            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }
        
        // تفعيل القائمة الجانبية
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            
            sidebarLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('active')) {
                        this.style.backgroundColor = 'rgba(255,255,255,0.1)';
                    }
                });
                
                link.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.backgroundColor = 'transparent';
                    }
                });
            });
        });
    </script>
</body>
</html>