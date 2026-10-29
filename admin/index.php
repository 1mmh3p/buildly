<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "اسم المستخدم أو كلمة المرور غير صحيحة";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - Buildly</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="login-form" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 400px;">
            <div class="login-header" style="text-align: center; margin-bottom: 2rem;">
                <h2 style="color: var(--secondary-color); margin-bottom: 0.5rem;">Buildly</h2>
                <p style="color: var(--gray-color);">لوحة تحكم المشرف</p>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem;">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="/index.html" style="color: var(--secondary-color); text-decoration: none;">
                    <i class="fas fa-arrow-right"></i> العودة إلى الموقع
                </a>
            </div>
        </div>
    </div>
</body>
</html>