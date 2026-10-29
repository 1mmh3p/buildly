<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireAdmin();

// جلب جميع المستخدمين
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

// معالجة إضافة مستخدم جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    // التحقق من عدم وجود مستخدم بنفس البريد أو اسم المستخدم
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        $error = "اسم المستخدم أو البريد الإلكتروني موجود مسبقاً";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $role])) {
            $success = "تم إضافة المستخدم بنجاح";
            header("Location: users.php");
            exit();
        } else {
            $error = "حدث خطأ أثناء إضافة المستخدم";
        }
    }
}

// معالجة تعديل المستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    // إذا تم إدخال كلمة مرور جديدة
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $result = $stmt->execute([$username, $email, $password, $role, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        $result = $stmt->execute([$username, $email, $role, $id]);
    }
    
    if ($result) {
        $success = "تم تحديث المستخدم بنجاح";
        header("Location: users.php");
        exit();
    } else {
        $error = "حدث خطأ أثناء تحديث المستخدم";
    }
}

// معالجة حذف المستخدم
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // منع حذف المستخدم الحالي
    if ($id == $_SESSION['user_id']) {
        $error = "لا يمكن حذف حسابك الخاص";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id])) {
            $success = "تم حذف المستخدم بنجاح";
            header("Location: users.php");
            exit();
        } else {
            $error = "حدث خطأ أثناء حذف المستخدم";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين - Buildly</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->

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
                        <a href="materials.php" class="sidebar-link" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
                            <i class="fas fa-cubes" style="margin-left: 0.5rem;"></i>
                            إدارة المواد
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="users.php" class="sidebar-link active" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition); background-color: rgba(255,255,255,0.1);">
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
                <h1 style="margin: 0;">إدارة المستخدمين</h1>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
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

            <!-- Users Table -->
            <div class="admin-table-container" style="background: white; border-radius: 8px; box-shadow: var(--shadow); overflow: hidden;">
                <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--secondary-color); color: white;">
                            <th style="padding: 1rem; text-align: right;">اسم المستخدم</th>
                            <th style="padding: 1rem; text-align: right;">البريد الإلكتروني</th>
                            <th style="padding: 1rem; text-align: right;">الدور</th>
                            <th style="padding: 1rem; text-align: right;">تاريخ التسجيل</th>
                            <th style="padding: 1rem; text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5" style="padding: 2rem; text-align: center; color: var(--gray-color);">
                                    لا توجد مستخدمين مسجلين
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div class="user-avatar" style="width: 40px; height: 40px; background: var(--secondary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        </div>
                                        <?php echo htmlspecialchars($user['username']); ?>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge" style="background: var(--success-color); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">أنت</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td style="padding: 1rem;">
                                    <span class="role-badge <?php echo $user['role'] == 'admin' ? 'admin' : 'user'; ?>" style="padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.8rem; font-weight: 500; background: <?php echo $user['role'] == 'admin' ? 'var(--danger-color)' : 'var(--secondary-color)'; ?>; color: white;">
                                        <?php echo $user['role'] == 'admin' ? 'مدير' : 'مستخدم'; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;"><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                <td style="padding: 1rem; text-align: center;">
                                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(<?php echo $user['id']; ?>, '<?php echo $user['username']; ?>', '<?php echo $user['email']; ?>', '<?php echo $user['role']; ?>')">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                        <i class="fas fa-trash"></i> حذف
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Users Statistics -->
            <div class="users-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--secondary-color);">
                        <?php echo count($users); ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">إجمالي المستخدمين</div>
                </div>
                
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--danger-color);">
                        <?php
                        $admin_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
                        echo $admin_count;
                        ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">المسؤولين</div>
                </div>
                
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--success-color);">
                        <?php
                        $today = date('Y-m-d');
                        $today_users = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = '$today'")->fetchColumn();
                        echo $today_users;
                        ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">مستخدمين جدد اليوم</div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add User Modal -->
    <div id="addModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: white; margin: 5% auto; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px; position: relative;">
            <span class="close" onclick="closeAddModal()" style="position: absolute; left: 1rem; top: 1rem; font-size: 1.5rem; cursor: pointer;">&times;</span>
            <h3>إضافة مستخدم جديد</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="role" class="form-label">الدور</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="user">مستخدم</option>
                        <option value="admin">مدير</option>
                    </select>
                </div>
                
                <div class="form-actions" style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">إلغاء</button>
                    <button type="submit" name="add_user" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: white; margin: 5% auto; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px; position: relative;">
            <span class="close" onclick="closeEditModal()" style="position: absolute; left: 1rem; top: 1rem; font-size: 1.5rem; cursor: pointer;">&times;</span>
            <h3>تعديل المستخدم</h3>
            <form method="POST" action="">
                <input type="hidden" id="edit_id" name="id">
                
                <div class="form-group">
                    <label for="edit_username" class="form-label">اسم المستخدم</label>
                    <input type="text" id="edit_username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password" class="form-label">كلمة المرور الجديدة (اختياري)</label>
                    <input type="password" id="edit_password" name="password" class="form-control" placeholder="اتركه فارغاً للحفاظ على كلمة المرور الحالية">
                </div>
                
                <div class="form-group">
                    <label for="edit_role" class="form-label">الدور</label>
                    <select id="edit_role" name="role" class="form-control" required>
                        <option value="user">مستخدم</option>
                        <option value="admin">مدير</option>
                    </select>
                </div>
                
                <div class="form-actions" style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">إلغاء</button>
                    <button type="submit" name="edit_user" class="btn btn-primary">تحديث</button>
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
        function openEditModal(id, username, email, role) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
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