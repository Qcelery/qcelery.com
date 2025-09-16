<?php
// 统一的管理后台标题栏组件

// 参数：页面标题和右侧操作按钮HTML
function renderAdminHeader($pageTitle, $actionsHtml = '') {
    
    // 检查是否登录
    $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    
    // 获取当前用户名
    $username = $isLoggedIn ? ($_SESSION['username'] ?? '管理员') : '';
    
    // 输出标题栏HTML
    echo <<<HTML
    <div class="admin-header">
        <div class="header-left">
            <h1>$pageTitle</h1>
        </div>
        <div class="header-right">
            $actionsHtml
            
            <!-- 用户信息和退出按钮 -->
            <div class="user-menu">
                <div class="user-info">
                    <span class="username">$username</span>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="logout-icon"></i> 退出登录
                </a>
            </div>
        </div>
    </div>
HTML;
}