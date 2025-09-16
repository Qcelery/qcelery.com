<!DOCTYPE html>
<?php session_start(); 
// 检查是否已登录
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 计算文章总数和总浏览量
$articleCount = 0;
$totalViews = 0;
$dataFile = '../data/articles.json';
if (file_exists($dataFile)) {
    $jsonContent = file_get_contents($dataFile);
    $articles = json_decode($jsonContent, true) ?: [];
    $articleCount = count($articles);
    
    // 计算总浏览量
    foreach ($articles as $article) {
        $totalViews += $article['views'] ?? 0;
    }
}

// HTML内容开始
?>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>控制台 - Qcelery's blog管理后台</title>
    <!-- 引入导航栏样式 -->
    <link rel="stylesheet" href="../assets/nav-styles.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-image: url('../assets/images/cheerio.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        
        /* 管理后台布局 */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 80px); /* 减去导航栏高度 */
        }
        
        /* 侧边栏样式 */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px 0;
            margin-top: 100px;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin: 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a:hover {
            background-color: #34495e;
            color: #fff;
            padding-left: 25px;
        }
        
        .sidebar-menu a.active {
            background-color: #3498db;
            color: #fff;
        }
        
        /* 主内容区域样式 */
        .dashboard-content {
            flex: 1;
            padding: 20px;
            margin-top: 100px;
        }
        
        /* 确保主要内容区域的可读性 */
        .stat-card,
        .quick-actions {
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        /* 统计卡片样式 */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 16px;
            font-weight: normal;
        }
        
        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        
        /* 快速操作区域样式 */
        .quick-actions {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .quick-actions h3 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
        }
        
        .action-btn {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        
        .action-btn:hover {
            background-color: #2980b9;
        }
        
        .action-btn.secondary {
            background-color: #95a5a6;
        }
        
        .action-btn.secondary:hover {
            background-color: #7f8c8d;
        }
        
        /* 页面标题样式 */
        .page-title {
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        /* 响应式设计 */
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 10px 0;
                margin-top: 20px;
            }
            
            .sidebar-menu {
                display: flex;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .sidebar-menu li {
                flex-shrink: 0;
            }
            
            .dashboard-content {
                margin-top: 20px;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- 引入导航组件 -->
    <?php include '../assets/nav.php'; ?>

    <div class="dashboard-container">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">控制台</a></li>
                <li><a href="articles.php">文章管理</a></li>
            </ul>
        </aside>
        
        <main class="dashboard-content">
            <h1 class="page-title">控制台</h1>
            
            <section class="stats">
                <div class="stat-card">
                    <h3>总文章数</h3>
                    <div class="value">
                        <?php echo $articleCount; ?>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>总浏览量</h3>
                    <div class="value">
                        <?php echo $totalViews; ?>
                    </div>
                </div>
            </section>
            
            <section class="quick-actions">
                <h3>快速操作</h3>
                <div class="action-buttons">
                    <a href="new_article.php" class="action-btn">新建文章</a>
                    <a href="articles.php" class="action-btn secondary">管理文章</a>
                </div>
            </section>
        </main>
    </div>
</body>
</html>