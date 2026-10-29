<?php
// ملف رأس الموقع المشترك
?>
<header class="header">
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php" class="logo">Buildly</a>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a href="calculator.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'calculator.php' ? 'active' : ''; ?>">حاسبة التكاليف</a>
                </li>
                <li class="nav-item">
                    <a href="prices.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'prices.php' ? 'active' : ''; ?>">أسعار المواد</a>
                </li>
                <li class="nav-item">
                    <a href="decoration.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'decoration.php' ? 'active' : ''; ?>">الديكور</a>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">من نحن</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">تواصل معنا</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a href="admin/dashboard.php" class="nav-link">لوحة التحكم</a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>
</header>