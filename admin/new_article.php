<!DOCTYPE html>
<?php 
session_start(); 
// 检查是否已登录
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 处理表单提交
$errors = [];
$formData = ['title' => '', 'content' => '', 'excerpt' => '', 'image' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $excerptInput = isset($_POST['excerpt']) ? trim($_POST['excerpt']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    
    // 保存表单数据，用于显示错误时的回显
    $formData = ['title' => $title, 'content' => $content, 'excerpt' => $excerptInput, 'image' => $image];
    
    // 处理图片上传
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
        $targetDir = '../assets/images/';
        $fileName = basename($_FILES['image_upload']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
        
        // 允许的文件格式
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileType, $allowedTypes)) {
            // 移动上传的文件到目标目录
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $targetFilePath)) {
                // 设置图片URL
                $image = '../assets/images/' . $fileName;
            } else {
                $errors[] = '图片上传失败，请重试';
            }
        } else {
            $errors[] = '不支持的图片格式，仅支持JPG, JPEG, PNG, GIF';
        }
    }
    
    // 验证数据
    if (empty($title)) {
        $errors[] = '请输入文章标题';
    }
    if (empty($content)) {
        $errors[] = '请输入文章内容';
    }
    
    // 如果用户输入了摘要，使用用户输入的摘要；否则从文章内容中自动提取摘要（前200个字符）
    $excerpt = $excerptInput;
    if (empty($excerpt) && !empty($content)) {
        // 移除HTML标签
        $plainContent = strip_tags($content);
        // 提取前200个字符，并确保不截断单词
        if (strlen($plainContent) > 200) {
            $excerpt = substr($plainContent, 0, 200);
            $excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
        } else {
            $excerpt = $plainContent;
        }
    }
    if (empty($image)) {
        // 如果没有提供图片，使用默认图片
        $image = 'https://picsum.photos/seed/' . rand(100, 999) . '/800/600';
    }
    
    if (empty($errors)) {
        // 读取现有文章
        $articles = [];
        $dataFile = '../data/articles.json';
        
        if (file_exists($dataFile)) {
            $jsonContent = file_get_contents($dataFile);
            $articles = json_decode($jsonContent, true) ?: [];
        } else {
            // 如果文件不存在，创建目录
            if (!file_exists('../data')) {
                mkdir('../data', 0755, true);
            }
        }
        
        // 生成新文章ID
        $newId = 1;
        if (!empty($articles)) {
            $maxId = max(array_column($articles, 'id'));
            $newId = $maxId + 1;
        }
        
        // 创建新文章
        $newArticle = [
            'id' => $newId,
            'title' => $title,
            'excerpt' => $excerpt,
            'content' => $content,
            'date' => date('Y-m-d'),
            'image' => $image,
            'views' => 0
        ];
        
        // 添加新文章到列表
        $articles[] = $newArticle;
        
        // 保存文章
        file_put_contents($dataFile, json_encode($articles, JSON_PRETTY_PRINT));
        
        // 设置成功消息并重定向
        $_SESSION['success_message'] = '文章已成功创建';
        header('Location: articles.php');
        exit;
    }
}

?>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新建文章 - Qcelery's blog管理后台</title>
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
        .form-container {
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        /* 页面标题样式 */
        .page-title {
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        /* 返回按钮样式 */
        .back-btn {
            background-color: #95a5a6;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .back-btn:hover {
            background-color: #7f8c8d;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            font-family: inherit;
            box-sizing: border-box;
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        .btn {
            background-color: #2ecc71;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #27ae60;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
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
            .form-container {
                padding: 20px;
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
                <li><a href="articles.php">文章管理</a></li>
                <li><a href="new_article.php" class="active">新建文章</a></li>
            </ul>
        </aside>
        
        <main class="dashboard-content">
            <h1 class="page-title">新建文章</h1>
            <a href="articles.php" class="back-btn">返回</a>
            
            <div class="form-container">
                <?php
                // 显示错误消息
                if (!empty($errors)) {
                    echo '<div class="error-message">';
                    foreach ($errors as $error) {
                        echo $error . '<br>';
                    }
                    echo '</div>';
                }
                ?>
                
                <form method="POST" action="new_article.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">文章标题 <span style="color: red;">*</span></label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($formData['title']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt">文章摘要（可选）</label>
                        <textarea id="excerpt" name="excerpt" style="min-height: 100px;"><?= htmlspecialchars($formData['excerpt']) ?></textarea>
                        <small>如不填写，系统将自动从文章内容中提取前几行作为摘要</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">文章内容 <span style="color: red;">*</span></label>
                        <textarea id="content" name="content" style="min-height: 300px;"><?= htmlspecialchars($formData['content']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">文章图片URL</label>
                        <input type="text" id="image" name="image" placeholder="图片URL (留空使用随机图片)" value="<?= htmlspecialchars($formData['image']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="image_upload">上传图片</label>
                        <input type="file" id="image_upload" name="image_upload" accept="image/*">
                        <small>支持的格式: JPG, PNG, GIF</small>
                    </div>
                    
                    <div class="form-group">
                        <label>从图片库选择</label>
                        <div id="image_library" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                            <?php
                            // 显示图片库中的图片
                            $imageDir = '../assets/images/';
                            $imageFiles = scandir($imageDir);
                            foreach ($imageFiles as $file) {
                                if ($file != '.' && $file != '..' && is_file($imageDir . $file)) {
                                    $imagePath = $imageDir . $file;
                                    $imageUrl = '../assets/images/' . $file;
                                    echo '<div style="position: relative;">
                                            <img src="' . $imageUrl . '" alt="' . $file . '" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer; border: 2px solid #ddd;">
                                            <button type="button" class="select-image-btn" data-image="' . $imageUrl . '" style="position: absolute; bottom: 5px; right: 5px; background: white; border: 1px solid #ddd; border-radius: 50%; width: 20px; height: 20px; padding: 0; cursor: pointer;">
                                                ✓
                                            </button>
                                          </div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <script>
                        // 图片选择功能
                        document.querySelectorAll('.select-image-btn').forEach(btn => {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                const imageUrl = this.getAttribute('data-image');
                                document.getElementById('image').value = imageUrl;
                                
                                // 高亮选中的图片
                                document.querySelectorAll('#image_library img').forEach(img => {
                                    img.style.border = '2px solid #ddd';
                                });
                                this.parentNode.querySelector('img').style.border = '2px solid #3498db';
                            });
                        });
                    </script>
                    
                    <button type="submit" class="btn">创建文章</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>