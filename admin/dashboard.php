<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireAdmin();

// إحصائيات لوحة التحكم
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$calculations_count = $pdo->query("SELECT COUNT(*) FROM calculations")->fetchColumn();
$messages_count = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$provinces_count = $pdo->query("SELECT COUNT(*) FROM provinces")->fetchColumn();
$companies_count = $pdo->query("SELECT COUNT(*) FROM companies")->fetchColumn();

// آخر العمليات الحسابية
$recent_calculations = $pdo->query("
    SELECT c.*, u.username, p.name as province_name 
    FROM calculations c 
    LEFT JOIN users u ON c.user_id = u.id 
    LEFT JOIN provinces p ON c.province_id = p.id 
    ORDER BY c.created_at DESC LIMIT 5
")->fetchAll();

// آخر الرسائل
$recent_messages = $pdo->query("SELECT * FROM messages ORDER BY sent_at DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - Buildly</title>
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
                        <a href="dashboard.php" class="sidebar-link active" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
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
            <h1 style="margin-bottom: 2rem;">لوحة التحكم</h1>
            
            <!-- Statistics Cards -->
            <div class="stats-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-icon" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 1rem;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--dark-color);">
                        <?php echo $users_count; ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">المستخدمين</div>
                </div>
                
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-icon" style="font-size: 2rem; color: var(--success-color); margin-bottom: 1rem;">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--dark-color);">
                        <?php echo $calculations_count; ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">عملية حسابية</div>
                </div>
                
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-icon" style="font-size: 2rem; color: var(--warning-color); margin-bottom: 1rem;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--dark-color);">
                        <?php echo $messages_count; ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">رسالة</div>
                </div>
                
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-icon" style="font-size: 2rem; color: var(--info-color); margin-bottom: 1rem;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--dark-color);">
                        <?php echo $provinces_count; ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">محافظة</div>
                </div>

                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-icon" style="font-size: 2rem; color: var(--danger-color); margin-bottom: 1rem;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--dark-color);">
                        <?php echo $companies_count; ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">شركة</div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="admin-sections" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Recent Calculations -->
                <div class="admin-section" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow);">
                    <h3 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #eee;">آخر العمليات الحسابية</h3>
                    <div class="recent-list">
                        <?php if (empty($recent_calculations)): ?>
                            <p style="text-align: center; color: var(--gray-color);">لا توجد عمليات حسابية حديثة</p>
                        <?php else: ?>
                            <?php foreach ($recent_calculations as $calculation): ?>
                            <div class="recent-item" style="padding: 1rem; border-bottom: 1px solid #f5f5f5;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong><?php echo $calculation['username'] ?: 'زائر'; ?></strong>
                                        <div style="font-size: 0.9rem; color: var(--gray-color);">
                                            <?php echo $calculation['area']; ?> م² - <?php echo $calculation['building_type']; ?>
                                            <?php if ($calculation['province_name']): ?>
                                                - <?php echo $calculation['province_name']; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="text-align: left;">
                                        <strong><?php echo number_format($calculation['total_cost'], 2); ?> $</strong>
                                        <div style="font-size: 0.8rem; color: var(--gray-color);">
                                            <?php echo date('Y-m-d', strtotime($calculation['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Recent Messages -->
                <div class="admin-section" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow);">
                    <h3 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #eee;">آخر الرسائل</h3>
                    <div class="recent-list">
                        <?php if (empty($recent_messages)): ?>
                            <p style="text-align: center; color: var(--gray-color);">لا توجد رسائل جديدة</p>
                        <?php else: ?>
                            <?php foreach ($recent_messages as $message): ?>
                            <div class="recent-item" style="padding: 1rem; border-bottom: 1px solid #f5f5f5;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($message['name']); ?></strong>
                                        <div style="font-size: 0.9rem; color: var(--secondary-color);">
                                            <?php echo htmlspecialchars($message['email']); ?>
                                        </div>
                                        <div style="font-size: 0.9rem; color: var(--gray-color); margin-top: 0.5rem;">
                                            <?php echo substr(htmlspecialchars($message['message']), 0, 50); ?>...
                                        </div>
                                    </div>
                                    <div style="text-align: left;">
                                        <div style="font-size: 0.8rem; color: var(--gray-color);">
                                            <?php echo date('Y-m-d', strtotime($message['sent_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/script.js"></script>
    <script>
        // تفعيل القائمة الجانبية
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            
            sidebarLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(255,255,255,0.1)';
                });
                
                link.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.backgroundColor = 'transparent';
                    }
                });
            });
            
            // إضافة النشاط للرابط الحالي
            const currentPage = window.location.pathname.split('/').pop();
            sidebarLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                    link.style.backgroundColor = 'rgba(255,255,255,0.1)';
                }
            });
        });
    </script>
</body>
</html>