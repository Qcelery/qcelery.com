<!DOCTYPE html>
<?php session_start(); 
// 检查是否已登录
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 读取文章数据
$articles = [];
$dataFile = '../data/articles.json';

if (file_exists($dataFile)) {
    $jsonContent = file_get_contents($dataFile);
    $articles = json_decode($jsonContent, true) ?: [];
    
    // 按时间排序，最新的在前
    usort($articles, function($a, $b) {
        $dateA = strtotime($a['date']);
        $dateB = strtotime($b['date']);
        return $dateB - $dateA;
    });
}

// 处理删除操作
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $articleId = intval($_GET['id']);
    
    // 查找并删除文章
    foreach ($articles as $key => $article) {
        if ($article['id'] == $articleId) {
            unset($articles[$key]);
            // 保存更新后的文章列表
            file_put_contents($dataFile, json_encode(array_values($articles), JSON_PRETTY_PRINT));
            // 设置成功消息
            $_SESSION['success_message'] = '文章已成功删除';
            // 重定向以避免刷新页面时重复删除
            header('Location: articles.php');
            exit;
        }
    }
}

// HTML内容开始
?>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章管理 - Qcelery's blog管理后台</title>
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
        .article-table {
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        /* 页面标题样式 */
        .page-title {
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        /* 文章表格样式 */
        .article-table {
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #555;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        /* 操作按钮样式 */
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .edit-btn {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }
        
        .edit-btn:hover {
            background-color: #2980b9;
        }
        
        .delete-btn {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }
        
        .delete-btn:hover {
            background-color: #c0392b;
        }
        
        /* 新建文章按钮样式 */
        .new-article-btn {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .new-article-btn:hover {
            background-color: #2980b9;
        }
        
        /* 无文章提示样式 */
        .no-articles {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        /* 成功消息样式 */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
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
            
            table {
                display: block;
                overflow-x: auto;
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
                <li><a href="dashboard.php">控制台</a></li>
                <li><a href="articles.php" class="active">文章管理</a></li>
                <li><a href="new_article.php">新建文章</a></li>
            </ul>
        </aside>
        
        <main class="dashboard-content">
            <h1 class="page-title">文章管理</h1>
            
            <!-- 新建文章按钮 -->
            <a href="new_article.php" class="new-article-btn">新建文章</a>
            
            <?php
            // 检查是否有成功消息
            if (isset($_SESSION['success_message'])) {
                echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            
            if (!empty($articles)) {
                ?>
                <div class="article-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>标题</th>
                                <th>发布日期</th>
                                <th>浏览量</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?= $article['id'] ?></td>
                                <td><?= $article['title'] ?></td>
                                <td><?= $article['date'] ?></td>
                                <td><?= $article['views'] ?? 0 ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit_article.php?id=<?= $article['id'] ?>" class="edit-btn">编辑</a>
                                        <a href="articles.php?action=delete&id=<?= $article['id'] ?>" class="delete-btn" onclick="return confirm('确定要删除这篇文章吗？')">删除</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php
            } else {
                ?>
                <div class="no-articles">
                    <h3>暂无文章</h3>
                    <p>请点击上方的"新建文章"按钮创建您的第一篇文章。</p>
                </div>
                <?php
            }
            ?>
        </main>
    </div>
</body>
</html>