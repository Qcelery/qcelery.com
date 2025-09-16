<?php
// 导航菜单组件
?>
<header>
    <nav>
        <div style="display: flex; align-items: center;">
            <a href="/index.php" class="logo">Qcelery's blog</a>
        </div>
        
        <!-- 汉堡菜单按钮（默认隐藏在桌面视图） -->
        <button class="menu-toggle" aria-label="打开菜单">
            <span class="menu-icon"></span>
            <span class="menu-icon"></span>
            <span class="menu-icon"></span>
        </button>
        
        <!-- 导航链接容器 -->
        <div class="nav-links">
            <a href="/index.php">首页</a>
            <a href="/articles.php">博客列表</a>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <a href="/admin/dashboard.php">博客管理</a>
            <a href="/admin/logout.php">退出登录</a>
            <?php else: ?>
            <a href="/admin/login.php">登录</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<script>
    // 菜单展开/收起功能
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');
        
        if (menuToggle && navLinks) {
            menuToggle.addEventListener('click', function() {
                navLinks.classList.toggle('open');
                menuToggle.classList.toggle('open');
            });
        }
        
        // 响应式处理
        function handleResize() {
            if (window.innerWidth > 768) {
                if (navLinks) {
                    navLinks.classList.remove('open');
                }
                if (menuToggle) {
                    menuToggle.classList.remove('open');
                }
            }
        }
        
        // 初始化和窗口大小变化时调用
        handleResize();
        window.addEventListener('resize', handleResize);
    });
</script>