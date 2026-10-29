<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireAdmin();

// جلب جميع الرسائل
$messages = $pdo->query("SELECT * FROM messages ORDER BY sent_at DESC")->fetchAll();

// معالجة حذف الرسالة
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "تم حذف الرسالة بنجاح";
        header("Location: messages.php");
        exit();
    } else {
        $error = "حدث خطأ أثناء حذف الرسالة";
    }
}

// معالجة وضع علامة كمقروء
if (isset($_GET['mark_read'])) {
    // في التطبيق الحقيقي، يمكن إضافة حقل is_read في الجدول
    $success = "تم وضع علامة كمقروء";
    header("Location: messages.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الرسائل - Buildly</title>
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
                        <a href="users.php" class="sidebar-link" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition);">
                            <i class="fas fa-users" style="margin-left: 0.5rem;"></i>
                            إدارة المستخدمين
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="messages.php" class="sidebar-link active" style="display: block; padding: 0.75rem 1.5rem; color: white; text-decoration: none; transition: var(--transition); background-color: rgba(255,255,255,0.1);">
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
                <h1 style="margin: 0;">إدارة الرسائل</h1>
                <div class="header-stats" style="display: flex; gap: 1rem;">
                    <span class="stat-badge" style="background: var(--secondary-color); color: white; padding: 0.5rem 1rem; border-radius: 20px;">
                        <i class="fas fa-envelope" style="margin-left: 0.5rem;"></i>
                        <?php echo count($messages); ?> رسالة
                    </span>
                </div>
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

            <!-- Messages List -->
            <div class="messages-container">
                <?php if (empty($messages)): ?>
                    <div class="no-messages" style="text-align: center; padding: 3rem; background: white; border-radius: 8px; box-shadow: var(--shadow);">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: var(--gray-color); margin-bottom: 1rem;"></i>
                        <h3 style="color: var(--gray-color);">لا توجد رسائل</h3>
                        <p style="color: var(--gray-color);">لم يتم استلام أي رسائل حتى الآن</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                    <div class="message-card" style="background: white; border-radius: 8px; box-shadow: var(--shadow); margin-bottom: 1.5rem; overflow: hidden;">
                        <div class="message-header" style="background: #f8f9fa; padding: 1.5rem; border-bottom: 1px solid #eee;">
                            <div style="display: flex; justify-content: between; align-items: center;">
                                <div>
                                    <h4 style="margin: 0 0 0.5rem; color: var(--dark-color);">
                                        <?php echo htmlspecialchars($message['name']); ?>
                                    </h4>
                                    <div style="color: var(--secondary-color);">
                                        <i class="fas fa-envelope" style="margin-left: 0.5rem;"></i>
                                        <?php echo htmlspecialchars($message['email']); ?>
                                    </div>
                                </div>
                                <div style="text-align: left;">
                                    <div style="color: var(--gray-color); font-size: 0.9rem;">
                                        <i class="fas fa-clock" style="margin-left: 0.5rem;"></i>
                                        <?php echo date('Y-m-d H:i', strtotime($message['sent_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="message-body" style="padding: 1.5rem;">
                            <p style="margin: 0; line-height: 1.6; color: var(--dark-color);">
                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                            </p>
                        </div>
                        
                        <div class="message-actions" style="padding: 1rem 1.5rem; background: #f8f9fa; border-top: 1px solid #eee; display: flex; gap: 1rem;">
                            <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-reply"></i> رد
                            </a>
                            <a href="messages.php?mark_read=<?php echo $message['id']; ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-check"></i> وضع علامة كمقروء
                            </a>
                            <a href="messages.php?delete=<?php echo $message['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                <i class="fas fa-trash"></i> حذف
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Messages Statistics -->
            <div class="messages-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--secondary-color);">
                        <?php echo count($messages); ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">إجمالي الرسائل</div>
                </div>
                
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--success-color);">
                        <?php
                        $today = date('Y-m-d');
                        $today_messages = $pdo->query("SELECT COUNT(*) FROM messages WHERE DATE(sent_at) = '$today'")->fetchColumn();
                        echo $today_messages;
                        ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">رسائل اليوم</div>
                </div>
                
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); text-align: center;">
                    <div class="stat-number" style="font-size: 2rem; font-weight: 700; color: var(--warning-color);">
                        <?php
                        $last_week = date('Y-m-d', strtotime('-7 days'));
                        $week_messages = $pdo->query("SELECT COUNT(*) FROM messages WHERE sent_at >= '$last_week'")->fetchColumn();
                        echo $week_messages;
                        ?>
                    </div>
                    <div class="stat-label" style="color: var(--gray-color);">رسائل الأسبوع</div>
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
            
            // إضافة تأثير للبطاقات
            const messageCards = document.querySelectorAll('.message-card');
            messageCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'var(--shadow)';
                });
            });
        });
    </script>
</body>
</html>